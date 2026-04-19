<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function create()
    {
        $locale = app()->getLocale();
        $title = $locale === 'ar' ? 'تواصل معنا' : 'Contact Us';

        return view('storefront.contact', compact('title'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'company_website' => ['nullable', 'prohibited'],
        ]);

        $normalizedMessage = Str::of($data['message'])->squish()->lower()->toString();
        $duplicateKey = 'contact:duplicate:' . sha1(Str::lower($data['email']) . '|' . Str::lower($data['subject'] ?? '') . '|' . $normalizedMessage);
        $ipKey = 'contact:ip:' . sha1((string) $request->ip());

        $recentDuplicate = ContactMessage::query()
            ->where('email', $data['email'])
            ->where('subject', $data['subject'] ?? null)
            ->where('message', $data['message'])
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($recentDuplicate || Cache::has($duplicateKey) || Cache::get($ipKey, 0) >= 3) {
            return back()
                ->withInput($request->except('company_website'))
                ->with('error', __('Please wait before sending another message.'));
        }

        if ($this->looksSuspicious($data['message'])) {
            return back()
                ->withInput($request->except('company_website'))
                ->with('error', __('Your message looks unusual. Please remove links or promotional content and try again.'));
        }

        ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'is_read' => false,
        ]);

        Cache::put($duplicateKey, true, now()->addMinutes(30));
        Cache::put($ipKey, Cache::get($ipKey, 0) + 1, now()->addHour());

        return back()->with('success', __('Thanks, your message has been sent. We will reply soon.'));
    }

    private function looksSuspicious(string $message): bool
    {
        $lower = Str::lower($message);
        $linkCount = preg_match_all('/https?:\/\/|www\.|\.com|\.net|\.org|\.info/i', $message);
        $blockedTerms = ['crypto recovery', 'loan offer', 'seo backlinks', 'casino bonus', 'telegram investment'];

        return $linkCount > 1 || Str::contains($lower, $blockedTerms);
    }
}
