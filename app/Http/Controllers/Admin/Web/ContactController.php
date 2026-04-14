<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Exception;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->filled('from_date') ? $request->date('from_date') : now()->subMonths(2);
        $endDate = $request->filled('to_date') ? $request->date('to_date') : null;

        $messages = ContactMessage::query()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $q = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($q): void {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('subject', 'like', "%{$q}%")
                        ->orWhere('message', 'like', "%{$q}%");
                });
            })
            ->whereDate('created_at', '>=', $startDate)
            ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q', 'from_date', 'to_date']);

        return view('admin.contact.index', compact('messages', 'filters'));
    }

    public function show(ContactMessage $contact)
    {
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('admin.contact.show', compact('contact'));
    }

    public function destroy(ContactMessage $contact)
    {
        try {
            $contact->delete();

            return back()->with('success', 'Contact message deleted.');
        } catch (Exception $exception) {
            report($exception);

            return back()->with('error', 'Failed to delete message.');
        }
    }
}
