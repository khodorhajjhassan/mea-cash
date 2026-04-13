<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\TopupRequest;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;

class TopupController extends Controller
{
    public function __construct(private readonly WalletService $walletService)
    {
    }

    public function index(Request $request)
    {
        $topups = TopupRequest::query()
            ->with('user:id,name', 'processor:id,name')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('payment_method', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->value()))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'status']);

        return view('admin.topups.index', compact('topups', 'filters'));
    }

    public function show(TopupRequest $topup)
    {
        $topup->load('user:id,name,email', 'processor:id,name');

        return view('admin.topups.show', compact('topup'));
    }

    public function approve(Request $request, TopupRequest $topup)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        try {
            if ($topup->status !== 'pending') {
                return back()->with('error', 'Only pending requests can be approved.');
            }

            $topup->update([
                'status' => 'approved',
                'admin_note' => $data['admin_note'] ?? null,
                'processed_by' => null,
                'processed_at' => now(),
            ]);

            $this->walletService->credit($topup->user, (float) $topup->amount_requested, 'Topup approved', $topup);

            return back()->with('success', 'Top-up approved and wallet credited.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to approve top-up request.');
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

            $topup->update([
                'status' => 'rejected',
                'admin_note' => $data['admin_note'],
                'processed_by' => null,
                'processed_at' => now(),
            ]);

            return back()->with('success', 'Top-up request rejected.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to reject top-up request.');
        }
    }
}
