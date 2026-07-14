<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Feedback;
use Exception;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $feedbacks = Feedback::query()
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name')
                        ->withCount(['orders as completed_orders_count' => function ($q) {
                            $q->where('status', OrderStatus::Completed);
                        }]);
                },
                'order:id,order_number'
            ])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('comment', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('order_number', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('rating'), fn ($query) => $query->where('rating', (int) $request->integer('rating')))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')->value()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->value()))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'rating', 'type', 'status']);

        return view('admin.feedback.index', compact('feedbacks', 'filters'));
    }

    public function toggleFeatured(Feedback $feedback)
    {
        $feedback->update([
            'show_on_homepage' => !$feedback->show_on_homepage
        ]);

        return back()->with('success', 'Feedback visibility updated.');
    }

    public function show(Feedback $feedback)
    {
        $feedback->load('user:id,name,email', 'order:id,order_number,status,total_price');

        return view('admin.feedback.show', compact('feedback'));
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:open,reviewing,resolved,refunded'],
            'admin_response' => ['nullable', 'string', 'max:2000'],
        ]);

        $adminResponse = filled($data['admin_response'] ?? null)
            ? trim((string) $data['admin_response'])
            : null;

        $feedback->update([
            'status' => $data['status'],
            'admin_response' => $adminResponse,
            'resolved_at' => in_array($data['status'], ['resolved', 'refunded'], true) ? now() : null,
        ]);

        if ($feedback->type === 'report' && $feedback->order && $adminResponse) {
            $fulfillmentData = is_object($feedback->order->fulfillment_data) && method_exists($feedback->order->fulfillment_data, 'getArrayCopy')
                ? $feedback->order->fulfillment_data->getArrayCopy()
                : (array) ($feedback->order->fulfillment_data ?? []);

            $reportHistory = $fulfillmentData['report_history'] ?? [];
            if (!is_array($reportHistory)) {
                $reportHistory = [];
            }

            $reportHistory[] = [
                'status' => $data['status'],
                'response' => $adminResponse,
                'created_at' => now()->toIso8601String(),
                'created_by' => (string) (auth()->user()?->name ?? 'Admin'),
            ];

            $feedback->order->update([
                'fulfillment_data' => array_merge($fulfillmentData, ['report_history' => $reportHistory]),
            ]);
        }

        if ($feedback->type === 'report' && $feedback->order) {
            $feedback->order->update([
                'status' => match ($data['status']) {
                    'open', 'reviewing' => OrderStatus::Reported,
                    'resolved' => OrderStatus::Completed,
                    'refunded' => OrderStatus::Refunded,
                    default => $feedback->order->status,
                },
            ]);
        }

        return back()->with('success', 'Report status updated.');
    }

    public function destroy(Feedback $feedback)
    {
        try {
            $feedback->delete();

            return back()->with('success', 'Feedback deleted.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete feedback.');
        }
    }
}
