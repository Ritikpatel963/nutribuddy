<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AssessmentQuizWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_public_questionnaire_page_loads_with_valid_plan(): void
    {
        SubscriptionPlan::create([
            'name' => 'Basic Assessment',
            'slug' => 'basic',
            'price' => 499,
            'is_active' => true,
        ]);

        $response = $this->get(route('frontend.nutrition.questionnaire', ['plan' => 'basic']));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.nutrition.questionnaire');
        $response->assertViewHas(['plan', 'cycle', 'questions']);
    }

    public function test_public_questionnaire_redirects_if_plan_missing(): void
    {
        $response = $this->get(route('frontend.nutrition.questionnaire'));

        $response->assertRedirect(route('diet_chart'));
        $response->assertSessionHas('error');
    }

    public function test_admin_with_permission_can_view_assessment_questions(): void
    {
        Permission::create(['name' => 'view assessment', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Quiz Admin', 'guard_name' => 'admin']);
        $role->givePermissionTo('view assessment');

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.assessment.questions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.assessment.questions.index');
        $response->assertViewHas(['questions', 'sections']);
    }

    public function test_admin_can_toggle_assessment_question_status(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        Permission::create(['name' => 'manage assessment', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Quiz Manager', 'guard_name' => 'admin']);
        $role->givePermissionTo('manage assessment');

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $question = AssessmentQuestion::create([
            'title' => 'How many hours does your child sleep?',
            'section' => 'Daily Routine & Sleep',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin, 'admin')->patchJson(route('admin.assessment.questions.toggle', $question));

        $response->assertSuccessful();
        $this->assertDatabaseHas('assessment_questions', [
            'id' => $question->id,
            'is_active' => false,
        ]);
    }

    public function test_assessment_question_and_option_mutations_persist_together(): void
    {
        Permission::create(['name' => 'manage assessment', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Assessment Editor', 'guard_name' => 'admin']);
        $role->givePermissionTo('manage assessment');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.assessment.questions.store'), [
                'title' => 'How often does your child exercise?',
                'section' => 'Growth & Physical Development',
                'options' => [
                    ['option_text' => 'Rarely', 'score' => 1],
                    ['option_text' => 'Daily', 'score' => 5],
                ],
            ])
            ->assertRedirect();

        $question = AssessmentQuestion::with('options')->firstOrFail();
        $this->assertTrue($question->is_active);
        $this->assertCount(2, $question->options);
        $rarely = $question->options->firstWhere('option_text', 'Rarely');
        $daily = $question->options->firstWhere('option_text', 'Daily');

        $this->patch(route('admin.assessment.questions.update', $question), [
            'title' => 'How often does your child exercise each week?',
            'section' => 'Growth & Physical Development',
            'options' => [
                ['id' => $rarely->id, 'option_text' => 'Once', 'score' => 2],
                ['id' => $daily->id, '_delete' => true, 'option_text' => 'Daily', 'score' => 5],
                ['option_text' => 'Several times', 'score' => 4],
            ],
        ])->assertRedirect();

        $question->refresh()->load('options');
        $this->assertSame('How often does your child exercise each week?', $question->title);
        $this->assertCount(2, $question->options);
        $this->assertSame('Once', $question->options->firstWhere('id', $rarely->id)->option_text);
        $this->assertFalse($question->options->contains('id', $daily->id));
        $this->assertTrue($question->options->contains('option_text', 'Several times'));
    }

    public function test_admin_with_permission_can_view_nutrition_questions_list(): void
    {
        Permission::create(['name' => 'view diet chart', 'guard_name' => 'admin']);
        $role = Role::create(['name' => 'Nutrition Admin', 'guard_name' => 'admin']);
        $role->givePermissionTo('view diet chart');

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $admin->assignRole($role);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.nutrition.questions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.nutrition.questions.index');
        $response->assertViewHas('groupedQuestions');
    }
}
