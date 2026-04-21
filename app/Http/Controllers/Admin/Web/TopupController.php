<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\TopupRequest;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use App\Notifications\UserNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TopupController extends Controller
{
    public function __construct(private readonly WalletService $walletService)
    {
    }

    public function index(Request $request)
    {
        $startDate = $request->filled('from_date') ? $request->date('from_date') : now()->subMonths(2);
        $endDate = $request->filled('to_date') ? $request->date('to_date') : null;

        $topups = TopupRequest::query()
            ->with('user:id,name', 'processor:id,name')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('payment_method', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"));
                });
            })
            // Status logic: if explicitly provided (even if empty string for "All"), use that. Otherwise default to 'pending'.
            ->when($request->has('status'), function ($query) use ($request) {
                if ($request->filled('status')) {
                    $query->where('status', $request->string('status')->value());
                }
            }, function ($query) {
                $query->where('status', 'pending');
            })
            // Default date filtering
            ->whereDate('created_at', '>=', $startDate)
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'status']);

        return view('admin.topups.index', compact('topups', 'filters'));
    }

    public function show(TopupRequest $topup)
    {
        $topup->load('user:id,name,email', 'processor:id,name');

        $receiptUrl = null;

        if ($topup->receipt_image_path) {
            try {
                if (Storage::disk('private')->exists($topup->receipt_image_path)) {
                    $receiptUrl = Storage::disk('private')->temporaryUrl(
                        $topup->receipt_image_path,
                        now()->addMinutes(10)
                    );
                }
            } catch (Throwable $exception) {
                report($exception);
            }

            // Backward compatibility for old receipts stored on the public/local disk.
            if ($receiptUrl === null) {
                try {
                    if (Storage::disk('public')->exists($topup->receipt_image_path)) {
                        $receiptUrl = Storage::disk('public')->url($topup->receipt_image_path);
                    }
                } catch (Throwable $exception) {
                    report($exception);
                }
            }
        }

        return view('admin.topups.show', compact('topup', 'receiptUrl'));
    }

    public function approve(Request $request, TopupRequest $topup)
    {
        $data = $request->validate([
            'amount_credited' => ['required', 'numeric', 'min:0'],
            'admin_note' => ['nullable', 'string'],
            'notify_email' => ['sometimes', 'boolean'],
            'notify_whatsapp' => ['sometimes', 'boolean'],
        ]);

        try {
            if ($topup->status !== 'pending') {
                return back()->with('error', 'Only pending requests can be approved.');
            }

            $alreadyCredited = WalletTransaction::query()
                ->where('reference_type', $topup->getMorphClass())
                ->where('reference_id', $topup->id)
                ->where('type', 'topup')
                ->exists();

            $amount = (float) $data['amount_credited'];

            $topup->update([
                'status' => 'approved',
                'admin_note' => $data['admin_note'] ?? null,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            if ($alreadyCredited) {
                return back()->with('success', 'Top-up was already credited. Status repaired to approved.');
            }

            $this->walletService->credit($topup->user, $amount, 'Top-up request approved: #'.$topup->id, $topup, auth()->id());
            $topup->user?->notify(new UserNotification([
                'type' => 'Top-Up Approved',
                'message' => "Your top-up request #{$topup->id} was approved and \${$amount} was added to your wallet.",
                'link' => route('store.wallet'),
                'icon' => 'account_balance_wallet',
            ]));

            if (!empty($data['notify_email'])) {
                \Illuminate\Support\Facades\Mail::to($topup->user->email)->send(new \App\Mail\TopupApprovedMail($topup, $amount));
            }

            $response = back()->with('success', 'Top-up approved and wallet credited.');

            if (!empty($data['notify_whatsapp']) && $topup->user?->phone) {
                $message = "Hello {$topup->user->name}, your top-up request for \${$topup->amount_requested} has been approved! \${$amount} has been added to your wallet. Thanks for using MeaCash!";
                $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $topup->user->phone) . "?text=" . urlencode($message);
                
                return redirect()->away($whatsappUrl);
            }

            return $response;
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to approve top-up request: ' . $exception->getMessage());
        }
    }

    public function reject(Request $request, TopupRequest $topup)
    {
        $data = $request->validate([
            'admin_note' => ['required', 'string'],
        ]);

        try {
            if ($topup->status !== 'pending') {
                return back()->with('error', 'Only pending requests can be rejected.');
            }

            $alreadyCredited = WalletTransaction::query()
                ->where('reference_type', $topup->getMorphClass())
                ->where('reference_id', $topup->id)
                ->where('type', 'topup')
                ->exists();

            if ($alreadyCredited) {
                $topup->update([
                    'status' => 'approved',
                    'admin_note' => 'This request was already credited, so it cannot be rejected. '.$data['admin_note'],
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                ]);

                return back()->with('error', 'This top-up was already credited, so it was repaired to approved instead of rejected.');
            }

            $topup->update([
                'status' => 'rejected',
                'admin_note' => $data['admin_note'],
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            $topup->user?->notify(new UserNotification([
                'type' => 'Top-Up Rejected',
                'message' => "Your top-up request #{$topup->id} was rejected. Note: {$data['admin_note']}",
                'link' => route('store.wallet'),
                'icon' => 'report',
            ]));

            return back()->with('success', 'Top-up request rejected.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to reject top-up request.');
        }
    }
}
