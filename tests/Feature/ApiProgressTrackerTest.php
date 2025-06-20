<?php

namespace Gmrakibulhasan\ApiProgressTracker\Tests\Feature;

use Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptTask;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Orchestra\Testbench\TestCase;

class ApiProgressTrackerTest extends TestCase
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            \Gmrakibulhasan\ApiProgressTracker\ApiProgressTrackerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        
        // Set app key for encryption
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    /** @test */
    public function it_can_create_a_developer()
    {
        $developer = ApiptDeveloper::create([
            'name' => 'Test Developer',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertInstanceOf(ApiptDeveloper::class, $developer);
        $this->assertEquals('Test Developer', $developer->name);
        $this->assertEquals('test@example.com', $developer->email);
    }

    /** @test */
    public function it_can_create_api_progress()
    {
        $apiProgress = ApiptApiProgress::create([
            'method' => 'GET',
            'endpoint' => '/api/test',
            'group_name' => 'Test Group',
            'description' => 'Test API endpoint',
            'priority' => 'normal',
            'status' => 'todo',
        ]);

        $this->assertInstanceOf(ApiptApiProgress::class, $apiProgress);
        $this->assertEquals('GET', $apiProgress->method);
        $this->assertEquals('/api/test', $apiProgress->endpoint);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $developer = ApiptDeveloper::create([
            'name' => 'Test Developer',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $task = ApiptTask::create([
            'title' => 'Test Task',
            'description' => 'Test task description',
            'assigned_by' => $developer->id,
            'priority' => 'normal',
            'status' => 'todo',
        ]);

        $this->assertInstanceOf(ApiptTask::class, $task);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals($developer->id, $task->assigned_by);
    }

    /** @test */
    public function it_can_create_comments()
    {
        $developer = ApiptDeveloper::create([
            'name' => 'Test Developer',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $apiProgress = ApiptApiProgress::create([
            'method' => 'GET',
            'endpoint' => '/api/test',
            'priority' => 'normal',
            'status' => 'todo',
        ]);

        $comment = ApiptComment::create([
            'description' => 'Test comment',
            'commentable_type' => ApiptApiProgress::class,
            'commentable_id' => $apiProgress->id,
            'developer_id' => $developer->id,
        ]);

        $this->assertInstanceOf(ApiptComment::class, $comment);
        $this->assertEquals('Test comment', $comment->description);
        $this->assertEquals($developer->id, $comment->developer_id);
    }

    /** @test */
    public function it_can_access_dashboard_route()
    {
        $response = $this->get('/api-progress');

        $response->assertStatus(200);
        $response->assertSee('API Progress Tracker');
    }

    /** @test */
    public function it_automatically_updates_completion_time()
    {
        $apiProgress = ApiptApiProgress::create([
            'method' => 'GET',
            'endpoint' => '/api/test',
            'priority' => 'normal',
            'status' => 'todo',
        ]);

        $this->assertNull($apiProgress->completion_time);

        $apiProgress->update(['status' => 'complete']);

        $this->assertNotNull($apiProgress->fresh()->completion_time);
    }

    /** @test */
    public function it_can_assign_developers_to_tasks()
    {
        $developer1 = ApiptDeveloper::create([
            'name' => 'Developer 1',
            'email' => 'dev1@example.com',
            'password' => Hash::make('password'),
        ]);

        $developer2 = ApiptDeveloper::create([
            'name' => 'Developer 2',
            'email' => 'dev2@example.com',
            'password' => Hash::make('password'),
        ]);

        $task = ApiptTask::create([
            'title' => 'Test Task',
            'assigned_by' => $developer1->id,
            'priority' => 'normal',
            'status' => 'todo',
        ]);

        $task->developers()->attach([$developer1->id, $developer2->id]);

        $this->assertEquals(2, $task->developers()->count());
        $this->assertTrue($task->developers->contains($developer1));
        $this->assertTrue($task->developers->contains($developer2));
    }
}
