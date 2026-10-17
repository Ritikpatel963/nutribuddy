<?php

namespace App\Http\Controllers\Admin\Nutrition;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Nutrition\StoreQuestionnaireQuestionRequest;
use App\Http\Requests\Admin\Nutrition\UpdateQuestionnaireQuestionRequest;
use App\Models\Nutrition\QuestionnaireQuestion;

class QuestionnaireAdminController extends Controller
{
    use \App\Http\Controllers\Admin\ResourceIndexFallbacks;
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

    public function store(StoreQuestionnaireQuestionRequest $request)
    {
        QuestionnaireQuestion::create($request->validated());
        return redirect()->route('admin.nutrition.questions.index')->with('success', 'Question created successfully.');
    }

    public function edit(QuestionnaireQuestion $question)
    {
        return view('admin.nutrition.questions.edit', compact('question'));
    }

    public function update(UpdateQuestionnaireQuestionRequest $request, QuestionnaireQuestion $question)
    {
        $question->update($request->validated());
        return redirect()->route('admin.nutrition.questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(QuestionnaireQuestion $question)
    {
        $question->delete();
        return redirect()->route('admin.nutrition.questions.index')->with('success', 'Question deleted successfully.');
    }
}
