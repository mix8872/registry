<?php

namespace Tests\Http\Controllers\Api;

use App\Http\Controllers\Api\HooksController;
use App\Models\Project;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HooksControllerTest extends TestCase
{
    public function testProjectCreated(): void
    {
        $data = [
            'type' => 'ProjectMovedToTrash',
            'payload' => [
                'id' => 1,
                'created_by_email' => 'kromskiy@grechka.digital',
                'name' => 'test_project',
                'body' => 'test description',
                'is_trashed' => false,
                'url_path' => '/projects/124',
                'created_on' => 1720097558,
            ],
        ];

        $result = Http::withHeaders([
            'X-Angie-WebhookSecret' => config('services.collab.hook_token')
        ])->post(route('collabHook'), $data)->json();

        $project = Project::firstWhere('crm_id', 1);

        $this->assertEquals(true, $result['success'] ?? false);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals(Project::STATUS_ACTIVE, $project->status);
    }

    public function testProjectMovedToTrash(): void
    {
        $data = [
            'type' => 'ProjectMovedToTrash',
            'payload' => [
                'id' => 1,
                'updated_by_id' => 11,
            ],
        ];

        $result = Http::withHeaders([
            'X-Angie-WebhookSecret' => config('services.collab.hook_token')
        ])->post(route('collabHook'), $data)->json();

        $project = Project::firstWhere('crm_id', 1);

        $this->assertEquals(true, $result['success'] ?? false);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals(Project::STATUS_ARCHIVED, $project->status);
    }

    public function testProjectCompleted(): void
    {
        $data = [
            'type' => 'ProjectCompleted',
            'payload' => [
                'id' => 1,
                'updated_by_id' => 11,
            ],
        ];

        $result = Http::withHeaders([
            'X-Angie-WebhookSecret' => config('services.collab.hook_token')
        ])->post(route('collabHook'), $data)->json();

        $project = Project::firstWhere('crm_id', 1);

        $this->assertEquals(true, $result['success'] ?? false);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals(Project::STATUS_ARCHIVED, $project->status);
    }

    public function testProjectRestoredFromTrash(): void
    {
        $data = [
            'type' => 'ProjectRestoredFromTrash',
            'payload' => [
                'id' => 1,
                'updated_by_id' => 11,
            ],
        ];

        $result = Http::withHeaders([
            'X-Angie-WebhookSecret' => config('services.collab.hook_token')
        ])->post(route('collabHook'), $data)->json();

        $project = Project::firstWhere('crm_id', 1);

        $this->assertEquals(true, $result['success'] ?? false);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals(Project::STATUS_ACTIVE, $project->status);
    }

    public function testProjectReopened(): void
    {
        $data = [
            'type' => 'ProjectReopened',
            'payload' => [
                'id' => 1,
                'updated_by_id' => 11,
            ],
        ];

        $result = Http::withHeaders([
            'X-Angie-WebhookSecret' => config('services.collab.hook_token')
        ])->post(route('collabHook'), $data)->json();

        $project = Project::firstWhere('crm_id', 1);

        $this->assertEquals(true, $result['success'] ?? false);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals(Project::STATUS_ACTIVE, $project->status);
    }

    public function testCollabHookWithInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $data = [
            // Invalid data, missing required fields
        ];

        $controller = new HooksController();
        $controller->collabHook($data);
    }
}
