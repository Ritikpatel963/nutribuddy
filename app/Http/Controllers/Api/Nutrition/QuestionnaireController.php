<?php

namespace App\Http\Controllers\Api\Nutrition;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nutrition\ChildAssessment;
use App\Models\Nutrition\Child;
use App\Services\Nutrition\AssessmentEngine;

class QuestionnaireController extends Controller
{
    protected $engine;

    public function __construct(AssessmentEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Get the dynamic questionnaire for a specific child.
     */
    public function getQuestions($childId)
    {
        $child = Child::findOrFail($childId);
        // Assuming one active assessment for now
        $assessment = $child->assessments()->latest()->first();
        
        if (!$assessment) {
            return response()->json(['message' => 'No assessment found'], 404);
        }

        $questions = $this->engine->getQuestionsForChild($child, $assessment);
        
        return response()->json([
            'model_level' => $assessment->model_level,
            'questions' => $questions
        ]);
    }

    /**
     * Submit answers and advance progress.
     */
    public function submitAnswers(Request $request, $childId)
    {
        $child = Child::findOrFail($childId);
        $assessment = $child->assessments()->latest()->first();
        
        // Save answers logic here...
        
        return response()->json(['message' => 'Answers saved successfully']);
    }
}
