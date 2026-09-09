<?php

namespace App\Http\Controllers\Admin\Nutrition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nutrition\QuestionnaireQuestion;

class QuestionnaireAdminController extends Controller
{
    public function index()
    {
        $questions = QuestionnaireQuestion::orderBy('sort_order')->get();
        // Group by model, then by section
        $groupedQuestions = $questions->groupBy('model')->map(function ($items) {
            return $items->groupBy('section');
        });

        return view('admin.nutrition.questions.index', compact('groupedQuestions'));
    }

    public function create()
    {
        return view('admin.nutrition.questions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string',
            'section' => 'nullable|string',
            'question' => 'required|string',
            'answer_type' => 'required|string',
            'options_scale' => 'nullable|string',
            'clinical_reason' => 'nullable|string',
            'chart_effect' => 'nullable|string',
            'only_ask_if' => 'nullable|string',
            'sort_order' => 'required|integer',
        ]);

        QuestionnaireQuestion::create($validated);
        return redirect()->route('admin.nutrition.questions.index')->with('success', 'Question created successfully.');
    }

    public function edit(QuestionnaireQuestion $question)
    {
        return view('admin.nutrition.questions.edit', compact('question'));
    }

    public function update(Request $request, QuestionnaireQuestion $question)
    {
        $validated = $request->validate([
            'question' => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        $question->update($validated);
        return redirect()->route('admin.nutrition.questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(QuestionnaireQuestion $question)
    {
        $question->delete();
        return redirect()->route('admin.nutrition.questions.index')->with('success', 'Question deleted successfully.');
    }
}
