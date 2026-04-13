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
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $filters = $request->only(['q']);

        return view('admin.contact.index', compact('messages', 'filters'));
    }

    public function show(ContactMessage $contact)
    {
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
