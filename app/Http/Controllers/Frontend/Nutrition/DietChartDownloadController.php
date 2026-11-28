<?php

namespace App\Http\Controllers\Frontend\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\Nutrition\Child;
use App\Services\Nutrition\DietChartDisplayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DietChartDownloadController extends Controller
{
    public function __construct(private readonly DietChartDisplayService $chartDisplay) {}

    public function downloadPdf(Request $request, Child $child)
    {
        $child = $request->user()->children()->findOrFail($child->id);

        try {
            $this->chartDisplay->authorizeChildAccess($child);
            $assessment = $child->assessments()->latest()->first();
            $dietPlan = $assessment?->dietPlans()->where('status', 'active')->where('review_status', 'approved')->latest()->first()
                ?? $assessment?->dietPlans()->latest()->first();

            if (! $dietPlan) {
                return redirect()->back()->with('error', 'No diet plan found to download');
            }

            $formattedPlan = $this->chartDisplay->formatPlan($dietPlan, $assessment);
            $planData = $dietPlan->plan_data;
            if (is_string($planData)) {
                $planData = json_decode($planData, true) ?? [];
            }
            $rawGaps = $planData['nutrient_gaps'] ?? ($planData['daily_gaps'] ?? []);
            $nutrientGaps = DietChartDisplayService::summarizeGaps($rawGaps);
            $clinicalTips = $this->chartDisplay->getClinicalTips($assessment, $nutrientGaps);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.diet-chart', [
                'child'         => $child,
                'assessment'    => $assessment,
                'dietPlan'      => $dietPlan,
                'formattedPlan' => $formattedPlan,
                'nutrientGaps'  => $nutrientGaps,
                'clinicalTips'  => $clinicalTips,
            ])->setPaper('a4', 'portrait');

            return $pdf->download("NutriBuddy-Diet-Plan-{$child->name}.pdf");
        } catch (\Exception $e) {
            Log::error('PDF Download Error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error downloading PDF: '.$e->getMessage());
        }
    }
}
