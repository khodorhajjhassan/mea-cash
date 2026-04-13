<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Exception;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $feedbacks = Feedback::query()
            ->with('user:id,name', 'order:id,order_number')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($nested) use ($q): void {
                    $nested->where('comment', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$q}%"))
                        ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('order_number', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('rating'), fn ($query) => $query->where('rating', (int) $request->integer('rating')))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'rating']);

        return view('admin.feedback.index', compact('feedbacks', 'filters'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load('user:id,name', 'order:id,order_number');

        return view('admin.feedback.show', compact('feedback'));
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
