<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ControllerRouteIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_nutrition_route_modules_register_the_existing_contract_once(): void
    {
        $expectedRoutes = [
            'nutrition.index' => ['GET', 'nutrition'],
            'frontend.nutrition.questionnaire' => ['GET', 'nutrition/questionnaire'],
            'nutrition.toggle-item' => ['POST', 'nutrition/toggle-item'],
            'nutrition.check-in' => ['POST', 'nutrition/check-in'],
            'api.nutrition.questionnaire.submit' => ['POST', 'api/nutrition/questionnaire/submit'],
            'child.diet-chart.show' => ['GET', 'child/{child}/diet-chart'],
            'child.diet-chart.regenerate' => ['POST', 'child/{child}/diet-chart/regenerate'],
            'child.diet-chart.download-pdf' => ['GET', 'child/{child}/diet-chart/download-pdf'],
        ];

        foreach ($expectedRoutes as $name => [$method, $uri]) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [{$name}] is missing.");
            $this->assertSame($uri, $route->uri(), "Route [{$name}] changed its URI.");
            $this->assertContains($method, $route->methods(), "Route [{$name}] changed its HTTP method.");
            $this->assertSame(
                1,
                collect(Route::getRoutes())->filter(
                    fn ($registered) => $registered->uri() === $uri && in_array($method, $registered->methods(), true)
                )->count(),
                "Route [{$uri}] is registered more than once for {$method}."
            );
        }
    }

    public function test_every_controller_route_resolves_to_an_existing_class_and_action(): void
    {
        foreach (Route::getRoutes() as $route) {
            $action = $route->getActionName();
            if (!str_contains($action, '@')) {
                continue;
            }

            [$class, $method] = explode('@', $action, 2);
            if (!str_starts_with($class, 'App\\Http\\Controllers\\')) {
                continue;
            }

            $this->assertTrue(class_exists($class), "Controller class missing for route [{$route->getName()}]: {$class}");
            $this->assertTrue(method_exists($class, $method), "Controller action missing for route [{$route->getName()}]: {$class}@{$method}");
        }
    }
}
