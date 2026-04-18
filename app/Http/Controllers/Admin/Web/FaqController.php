<?php
 
namespace App\Http\Controllers\Admin\Web;
 
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Exception;
use Illuminate\Http\Request;
 
class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->paginate(15);
        return view('admin.faqs.index', compact('faqs'));
    }
 
    public function create()
    {
        return view('admin.faqs.create');
    }
 
    public function store(Request $request)
    {
        $data = $request->validate([
            'question_en' => ['required', 'string', 'max:255'],
            'question_ar' => ['required', 'string', 'max:255'],
            'answer_en' => ['required', 'string'],
            'answer_ar' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
 
        try {
            $data['is_active'] = $request->boolean('is_active', true);
            Faq::create($data);
            return redirect()->route('admin.faqs.index')->with('success', 'FAQ created successfully.');
        } catch (Exception $e) {
            report($e);
            return back()->withInput()->with('error', 'Failed to create FAQ.');
        }
    }
 
    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }
 
    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question_en' => ['required', 'string', 'max:255'],
            'question_ar' => ['required', 'string', 'max:255'],
            'answer_en' => ['required', 'string'],
            'answer_ar' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
 
        try {
            $data['is_active'] = $request->boolean('is_active', $faq->is_active);
            $faq->update($data);
            return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
        } catch (Exception $e) {
            report($e);
            return back()->withInput()->with('error', 'Failed to update FAQ.');
        }
    }
 
    public function destroy(Faq $faq)
    {
        try {
            $faq->delete();
            return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully.');
        } catch (Exception $e) {
            report($e);
            return back()->with('error', 'Failed to delete FAQ.');
        }
    }
}
