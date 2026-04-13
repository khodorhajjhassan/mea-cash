<?php

namespace App\Http\Controllers\Admin\Web;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Exception;

class ContactController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::query()->latest('id')->paginate(20);

        return view('admin.contact.index', compact('messages'));
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
