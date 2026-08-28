<?php

namespace App\Http\Controllers\Admin\Nutrition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nutrition\QuestionnaireQuestion;

class QuestionnaireAdminController extends Controller
{
    public function index()
    {
        $questions = QuestionnaireQuestion::orderBy('model')->orderBy('sort_order')->get();
        return view('admin.nutrition.questions.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.nutrition.questions.create');
    }

    public function store(Request $request)
    {
        // Validation and save
        $validated = $request->validate([
            'model' => 'required|string',
            'question' => 'required|string',
            'answer_type' => 'required|string',
            // ... other fields
        ]);

        QuestionnaireQuestion::create($validated);
        return redirect()->route('admin.nutrition.questions.index');
    }
}
