<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Exception;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::query()->with('user:id,name', 'order:id,order_number')->latest('id')->paginate(20);

        return view('admin.feedback.index', compact('feedbacks'));
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
