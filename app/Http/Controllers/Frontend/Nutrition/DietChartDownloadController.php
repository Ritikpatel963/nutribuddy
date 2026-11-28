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

            $dayIndexParam = $request->has('day_index') && $request->query('day_index') !== ''
                ? (int) $request->query('day_index')
                : null;
            $weekParam = $request->has('week') && $request->query('week') !== ''
                ? (int) $request->query('week')
                : null;

            $downloadTitle = "NutriBuddy-Diet-Plan-{$child->name}";

            if ($dayIndexParam !== null) {
                $matchedDay = collect($formattedPlan)->firstWhere('index', $dayIndexParam);
                if ($matchedDay) {
                    $formattedPlan = [$matchedDay];
                    $dayLabel = preg_replace('/[^A-Za-z0-9_-]/', '', str_replace(' ', '-', $matchedDay['label'] ?? 'Day'));
                    $dateStr = \Illuminate\Support\Carbon::parse($matchedDay['date'])->format('d-M');
                    $downloadTitle .= "-Day-" . ($dayIndexParam + 1) . "-{$dayLabel}-{$dateStr}";
                }
            } elseif ($weekParam !== null) {
                $filtered = array_values(array_filter($formattedPlan, fn ($d) => ($d['week'] ?? 1) === $weekParam));
                if (! empty($filtered)) {
                    $formattedPlan = $filtered;
                    $downloadTitle .= "-Week-{$weekParam}";
                }
            } else {
                $downloadTitle .= "-Full-Plan";
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.diet-chart', [
                'child'         => $child,
                'assessment'    => $assessment,
                'dietPlan'      => $dietPlan,
                'formattedPlan' => $formattedPlan,
                'nutrientGaps'  => $nutrientGaps,
                'clinicalTips'  => $clinicalTips,
                'isSingleDay'   => count($formattedPlan) === 1,
            ])->setPaper('a4', 'portrait');

            return $pdf->download("{$downloadTitle}.pdf");
        } catch (\Exception $e) {
            Log::error('PDF Download Error: '.$e->getMessage());

            return redirect()->back()->with('error', 'Error downloading PDF: '.$e->getMessage());
        }
    }
}
