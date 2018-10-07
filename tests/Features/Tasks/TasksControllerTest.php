<?php

namespace Studio\Novacron\Tests\Features\Tasks;

use Studio\Novacron\NovaCron;
use Symfony\Component\HttpFoundation\Response;
use Studio\Novacron\Http\Controllers\ToolController;
use Studio\Novacron\Tests\TestCase;

class TasksControllerTest extends TestCase
{
    /** @test */
    public function it_can_can_return_a_response()
    {
        $response = $this
            ->get(route('studio.novacron.tasks'));

        $response->assertSuccessful();
    }
}