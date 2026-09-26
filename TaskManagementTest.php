<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_dashboard_lists_tasks_and_filters_by_status(): void
    {
        Task::factory()->create(['title' => 'Plan the next launch', 'status' => 'pending']);
        Task::factory()->create(['title' => 'Archive old notes', 'status' => 'completed']);

        $this->get(route('tasks.index', ['status' => 'pending']))
            ->assertSeeText('Plan the next launch')
            ->assertDontSeeText('Archive old notes');
    }

    public function test_valid_task_can_be_created_and_updated(): void
    {
        $this->post(route('tasks.store'), [
            'title' => 'Write launch plan',
            'description' => 'Map the next milestones.',
            'due_date' => '2026-10-02',
        ])->assertRedirectToRoute('tasks.index');

        $task = Task::query()->where('title', 'Write launch plan')->firstOrFail();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'pending']);

        $this->put(route('tasks.update', $task), [
            'title' => 'Publish launch plan',
            'description' => 'Share the final milestones.',
            'due_date' => '2026-10-04',
        ])->assertRedirectToRoute('tasks.index');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Publish launch plan',
            'description' => 'Share the final milestones.',
            'status' => 'pending',
        ]);
    }

    public function test_task_status_can_be_toggled_between_pending_and_completed(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $this->patch(route('tasks.status', $task))->assertRedirect();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);

        $this->patch(route('tasks.status', $task))->assertRedirect();
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'pending']);
    }

    public function test_edit_form_displays_existing_task_details(): void
    {
        $task = Task::factory()->create([
            'title' => 'Review the launch plan',
            'description' => 'Check the final milestones.',
            'due_date' => '2026-10-02',
        ]);

        $this->get(route('tasks.edit', $task))
            ->assertSee('value="Review the launch plan"', false)
            ->assertSee('value="2026-10-02"', false)
            ->assertSeeText('Check the final milestones.');
    }

    public function test_task_can_be_deleted(): void
    {
        $task = Task::factory()->create();

        $this->delete(route('tasks.destroy', $task))->assertRedirectToRoute('tasks.index');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_task_creation_requires_a_title(): void
    {
        $this->post(route('tasks.store'), [])
            ->assertSessionHasErrors('title');

        $this->assertDatabaseCount('tasks', 0);
    }
}
