<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentQuestionController extends Controller
{
    // ─── List ─────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $questions = AssessmentQuestion::with('options')
            ->orderBy('section')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');

        // Unique sections list for the form dropdown.
        $sections = AssessmentQuestion::distinct()->pluck('section')->sort()->values();

        return view('admin.assessment.questions.index', compact('questions', 'sections'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'                    => ['required', 'string', 'max:500'],
            'description'              => ['nullable', 'string'],
            'section'                  => ['required', 'string', 'max:100'],
            'sort_order'               => ['nullable', 'integer', 'min:0'],
            'options'                  => ['required', 'array', 'min:2'],
            'options.*.option_text'    => ['required', 'string', 'max:255'],
            'options.*.score'          => ['required', 'integer', 'min:0', 'max:99'],
            'options.*.sort_order'     => ['nullable', 'integer', 'min:0'],
        ]);

        $question = AssessmentQuestion::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'section'     => trim($data['section']),
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => true,
        ]);

        foreach ($data['options'] as $i => $opt) {
            $question->options()->create([
                'option_text' => $opt['option_text'],
                'score'       => (int) $opt['score'],
                'sort_order'  => $opt['sort_order'] ?? $i,
            ]);
        }

        return redirect()
            ->route('admin.assessment.questions.index', ['tab' => trim($data['section'])])
            ->with('success', 'Question created successfully.');
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(AssessmentQuestion $question): View
    {
        $question->load('options');
        $sections = AssessmentQuestion::distinct()->pluck('section')->sort()->values();

        return view('admin.assessment.questions.edit', compact('question', 'sections'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, AssessmentQuestion $question)
    {
        $data = $request->validate([
            'title'                    => ['required', 'string', 'max:500'],
            'description'              => ['nullable', 'string'],
            'section'                  => ['required', 'string', 'max:100'],
            'sort_order'               => ['nullable', 'integer', 'min:0'],
            'options'                  => ['required', 'array', 'min:2'],
            'options.*.id'             => ['nullable', 'integer'],
            'options.*.option_text'    => ['required', 'string', 'max:255'],
            'options.*.score'          => ['required', 'integer', 'min:0', 'max:99'],
            'options.*.sort_order'     => ['nullable', 'integer', 'min:0'],
            'options.*._delete'        => ['nullable', 'boolean'],
        ]);

        $question->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'section'     => trim($data['section']),
            'sort_order'  => $data['sort_order'] ?? 0,
        ]);

        $existingIds = [];

        foreach ($data['options'] as $i => $opt) {
            if (! empty($opt['_delete'])) {
                if (! empty($opt['id'])) {
                    $question->options()->where('id', $opt['id'])->delete();
                }
                continue;
            }

            if (! empty($opt['id'])) {
                $option = $question->options()->find($opt['id']);
                if ($option) {
                    $option->update([
                        'option_text' => $opt['option_text'],
                        'score'       => (int) $opt['score'],
                        'sort_order'  => $opt['sort_order'] ?? $i,
                    ]);
                    $existingIds[] = $option->id;
                }
            } else {
                $newOpt = $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'score'       => (int) $opt['score'],
                    'sort_order'  => $opt['sort_order'] ?? $i,
                ]);
                $existingIds[] = $newOpt->id;
            }
        }

        return redirect()
            ->route('admin.assessment.questions.index', ['tab' => trim($data['section'])])
            ->with('success', 'Question updated successfully.');
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy(AssessmentQuestion $question)
    {
        $section = $question->section;
        $question->delete();

        return redirect()
            ->route('admin.assessment.questions.index', ['tab' => $section])
            ->with('success', 'Question deleted successfully.');
    }

    // ─── Toggle Active ────────────────────────────────────────────────────────

    public function toggleActive(AssessmentQuestion $question)
    {
        $question->update(['is_active' => ! $question->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $question->is_active,
        ]);
    }

    // ─── Reorder ─────────────────────────────────────────────────────────────

    public function reorder(Request $request)
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($request->order as $position => $id) {
            AssessmentQuestion::where('id', $id)->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }

    // ─── Rename Section ───────────────────────────────────────────────────────

    public function renameSection(Request $request)
    {
        $data = $request->validate([
            'old_name' => ['required', 'string', 'max:100'],
            'new_name' => ['required', 'string', 'max:100'],
        ]);

        $oldName = trim($data['old_name']);
        $newName = trim($data['new_name']);

        if ($oldName === $newName) {
            return response()->json(['success' => true, 'message' => 'No change.']);
        }

        $updated = AssessmentQuestion::where('section', $oldName)
            ->update(['section' => $newName]);

        return response()->json([
            'success'  => true,
            'updated'  => $updated,
            'new_name' => $newName,
        ]);
    }
}
