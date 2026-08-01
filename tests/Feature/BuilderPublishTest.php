<?php

use App\Models\ExperienceProject;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'creator', 'verified_at' => now()]);
    $this->project = ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'config' => [
            'components' => [
                ['type' => 'text', 'properties' => ['content' => 'Hello World']],
            ],
        ],
    ]);
});

it('requires authentication to view publish page', function () {
    $this->get(route('studio.builder.projects.publish', $this->project->slug))
        ->assertRedirect(route('login'));
});

it('shows publish page for authenticated user', function () {
    $this->actingAs($this->user)
        ->get(route('studio.builder.projects.publish', $this->project->slug))
        ->assertOk()
        ->assertSee('Publish')
        ->assertSee('Category');
});

it('prevents other users from viewing publish page', function () {
    $otherUser = User::factory()->create(['role' => 'creator', 'verified_at' => now()]);

    $this->actingAs($otherUser)
        ->get(route('studio.builder.projects.publish', $this->project->slug))
        ->assertForbidden();
});

it('publish creates a simulation record', function () {
    Storage::fake('public');

    $this->actingAs($this->user)
        ->postJson(route('studio.builder.projects.publish-confirm', $this->project->slug), [
            'category' => 'Science',
            'subcategory' => 'Physics',
            'tags' => 'energy,forces',
            'description' => 'A physics simulation',
        ])
        ->assertOk()
        ->assertJson(['success' => true]);

    // Verify simulation was created
    $this->assertDatabaseHas('simulations', [
        'user_id' => $this->user->id,
        'experience_project_id' => $this->project->id,
        'title' => $this->project->title,
        'category' => 'Science',
        'subcategory' => 'Physics',
        'is_published' => true,
    ]);

    // Verify project status updated
    $this->project->refresh();
    expect($this->project->status)->toBe('published');
});

it('publish requires category field', function () {
    $this->actingAs($this->user)
        ->postJson(route('studio.builder.projects.publish-confirm', $this->project->slug), [
            'category' => '',
        ])
        ->assertUnprocessable();
});

it('unpublish hides simulation from platform', function () {
    // Create a published simulation linked to the project
    $simulation = Simulation::factory()
        ->fromBuilder($this->project)
        ->create([
            'user_id' => $this->user->id,
            'is_published' => true,
        ]);

    $this->actingAs($this->user)
        ->post(route('studio.builder.projects.unpublish', $this->project->slug))
        ->assertRedirect();

    // Verify simulation is unpublished
    $simulation->refresh();
    expect($simulation->is_published)->toBeFalse();

    // Verify project status updated
    $this->project->refresh();
    expect($this->project->status)->toBe('draft');
});

it('builder index shows view on platform link for published projects', function () {
    Simulation::factory()
        ->fromBuilder($this->project)
        ->create([
            'user_id' => $this->user->id,
            'slug' => $this->project->slug.'-sim',
        ]);

    $this->actingAs($this->user)
        ->get(route('studio.builder.index'))
        ->assertOk()
        ->assertSee('View on Platform');
});

it('builder index shows publish button for draft projects', function () {
    $this->actingAs($this->user)
        ->get(route('studio.builder.index'))
        ->assertOk()
        ->assertSee('Publish');
});
