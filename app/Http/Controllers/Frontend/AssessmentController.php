<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreAssessmentRequest;
use App\Models\AssessmentAttempt;
use App\Services\AssessmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(private readonly AssessmentService $service)
    {
    }

    // ─── Quiz Page ────────────────────────────────────────────────────────────

    /**
     * Show the quiz.
     */
    public function index(): View
    {
        $questionsGrouped = $this->service->getActiveQuestionsGrouped();
        $questions        = $this->service->getActiveQuestions();
        $totalQuestions   = $questions->count();

        // We will no longer abort, so the view can display a friendly empty state message.

        return view('pages.user-panel.assessment.index', compact(
            'questionsGrouped',
            'questions',
            'totalQuestions',
        ));
    }

    // ─── Submit ───────────────────────────────────────────────────────────────

    /**
     * Submit answers, calculate scores, redirect to result.
     */
    public function store(StoreAssessmentRequest $request)
    {
        $answers = $request->validated()['answers']; // [ question_id => option_id ]

        $attempt = $this->service->calculateAndStore(
            user: $request->user(),
            answers: $answers,
        );

        return redirect()
            ->route('user.assessment.result', $attempt)
            ->with('success', 'Assessment completed! Here are your results.');
    }

    // ─── Result Page ──────────────────────────────────────────────────────────

    public function result(AssessmentAttempt $attempt): View
    {
        abort_unless((int) $attempt->user_id === (int) auth()->id(), 403);

        $attempt->load(['sectionScores', 'answers.question', 'answers.option']);

        // Fetch prev attempt for comparison hint.
        $previousAttempt = AssessmentAttempt::where('user_id', auth()->id())
            ->where('id', '<', $attempt->id)
            ->latest()
            ->first();

        return view('pages.user-panel.assessment.result', compact('attempt', 'previousAttempt'));
    }

    // ─── History ──────────────────────────────────────────────────────────────

    public function history(Request $request): View
    {
        $attempts = AssessmentAttempt::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pages.user-panel.assessment.history', compact('attempts'));
    }

    // ─── Comparison ───────────────────────────────────────────────────────────

    public function compare(Request $request): View
    {
        $userAttempts = AssessmentAttempt::where('user_id', auth()->id())
            ->with('sectionScores')
            ->latest()
            ->take(2)
            ->get();

        abort_if($userAttempts->count() < 2, 404, 'You need at least 2 attempts to compare.');

        [$latest, $previous] = [$userAttempts->first(), $userAttempts->last()];

        $comparison = $this->service->compareAttempts($latest, $previous);

        return view('pages.user-panel.assessment.compare', compact(
            'latest',
            'previous',
            'comparison',
        ));
    }

    // ─── Detail / Breakdown ───────────────────────────────────────────────────

    public function detail(AssessmentAttempt $attempt): View
    {
        abort_unless((int) $attempt->user_id === (int) auth()->id(), 403);

        $attempt->load([
            'answers.question.options',
            'answers.option',
            'sectionScores',
        ]);

        // Group answers by section for display.
        $answersBySection = $attempt->answers->groupBy(fn ($a) => $a->question->section);

        return view('pages.user-panel.assessment.detail', compact('attempt', 'answersBySection'));
    }
}
