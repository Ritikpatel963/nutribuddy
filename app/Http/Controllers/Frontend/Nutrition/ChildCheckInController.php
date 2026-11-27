<?php

namespace App\Http\Controllers\Frontend\Nutrition;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreChildCheckInRequest;
use App\Models\Nutrition\Child;
use App\Services\Nutrition\MealLoggingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildCheckInController extends Controller
{
    public function __construct(private readonly MealLoggingService $mealLogging) {}

    public function create(Request $request): View
    {
        $children = Child::where('user_id', $request->user()->id)->latest()->get();
        $requestedChildId = $request->query('child_id');

        if ($requestedChildId !== null) {
            $child = $children->firstWhere('id', (int) $requestedChildId);
            abort_unless($child, 404);
        } else {
            $child = $children->first();
        }

        $latestAssessment = $child?->assessments()->latest()->first();
        $checkIns = $child
            ? $child->checkIns()->with('assessment')->latest()->limit(10)->get()
            : collect();
        $latestCheckIn = $checkIns->first();

        return view('frontend.nutrition.check-in', compact('children', 'child', 'latestAssessment', 'checkIns', 'latestCheckIn'));
    }

    public function saveCheckIn(StoreChildCheckInRequest $request)
    {
        try {
            $data = $request->validated();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
            }

            $child = Child::where('id', $data['child_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$child) {
                return response()->json(['success' => false, 'message' => 'Child not found'], 403);
            }

            $lastAssessment = $child->assessments()->latest()->first();

            if (!$lastAssessment) {
                return response()->json(['success' => false, 'message' => 'No previous assessment found'], 404);
            }

            $checkIn = $this->mealLogging->saveCheckIn($lastAssessment, $data);
            $newAssessment = $checkIn->assessment;

            return response()->json([
                'success' => true,
                'message' => 'Check-in saved successfully!',
                'assessment_id' => $newAssessment->id,
                'check_in_id' => $checkIn->id,
                'current_weight' => $newAssessment->current_weight_kg,
                'current_height' => $newAssessment->current_height_cm,
                'bmi' => $newAssessment->bmi,
            ]);

        } catch (\Exception $e) {
            Log::error('Check-In Save Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error saving check-in'], 500);
        }
    }

}
