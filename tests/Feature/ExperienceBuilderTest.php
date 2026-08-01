<?php

use App\Models\ExperienceProject;
use App\Models\ExperienceTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'creator', 'verified_at' => now()]);
    $this->template = ExperienceTemplate::factory()->create([
        'is_active' => true,
        'schema' => [
            'components' => [
                [
                    'type' => 'text',
                    'label' => 'Title',
                    'default' => ['content' => 'Hello', 'tag' => 'h2', 'fontSize' => 'text-xl', 'color' => '#000', 'align' => 'left'],
                ],
            ],
        ],
    ]);
});

it('requires authentication to access builder', function () {
    $this->get(route('studio.builder.index'))
        ->assertRedirect(route('login'));
});

it('shows builder dashboard for authenticated user', function () {
    $this->actingAs($this->user)
        ->get(route('studio.builder.index'))
        ->assertOk()
        ->assertSee('Experience Builder');
});

it('shows template selection page', function () {
    $this->actingAs($this->user)
        ->get(route('studio.builder.templates'))
        ->assertOk()
        ->assertSee('Pilih Template')
        ->assertSee($this->template->name);
});

it('can create a blank project', function () {
    $this->actingAs($this->user)
        ->post(route('studio.builder.projects.create'), [
            'title' => 'My Test Project',
            'description' => 'A test project',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('experience_projects', [
        'user_id' => $this->user->id,
        'title' => 'My Test Project',
        'status' => 'draft',
    ]);
});

it('can create a project from template', function () {
    $this->actingAs($this->user)
        ->post(route('studio.builder.projects.create'), [
            'template_slug' => $this->template->slug,
            'title' => 'From Template',
        ])
        ->assertRedirect();

    $project = ExperienceProject::where('title', 'From Template')->first();
    expect($project)->not->toBeNull()
        ->and($project->template_id)->toBe($this->template->id)
        ->and($project->config)->toHaveKey('components');
});

it('can show edit page for own project', function () {
    $project = ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'config' => ['components' => []],
    ]);

    $this->actingAs($this->user)
        ->get(route('studio.builder.projects.edit', $project->slug))
        ->assertOk()
        ->assertSee($project->title);
});

it('cannot edit another users project', function () {
    $otherUser = User::factory()->create(['role' => 'creator']);
    $project = ExperienceProject::factory()->create([
        'user_id' => $otherUser->id,
        'config' => ['components' => []],
    ]);

    $this->actingAs($this->user)
        ->get(route('studio.builder.projects.edit', $project->slug))
        ->assertForbidden();
});

it('can update project config', function () {
    $project = ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'config' => ['components' => []],
    ]);

    $this->actingAs($this->user)
        ->putJson(route('studio.builder.projects.update', $project->slug), [
            'config' => ['components' => [['id' => '1', 'type' => 'text', 'label' => 'Test', 'properties' => []]]],
        ])
        ->assertOk();

    $project->refresh();
    expect($project->config['components'])->toHaveCount(1);
});

it('can access publish page for a project', function () {
    $project = ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'status' => 'draft',
        'config' => ['components' => []],
    ]);

    $this->actingAs($this->user)
        ->get(route('studio.builder.projects.publish', $project->slug))
        ->assertOk()
        ->assertSee('Category');
});

it('can delete a project', function () {
    $project = ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'config' => ['components' => []],
    ]);

    $this->actingAs($this->user)
        ->delete(route('studio.builder.projects.destroy', $project->slug))
        ->assertRedirect();

    $this->assertDatabaseMissing('experience_projects', ['id' => $project->id]);
});

it('can preview a project', function () {
    $project = ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'config' => [
            'components' => [
                ['id' => '1', 'type' => 'text', 'label' => 'Title', 'properties' => ['content' => 'Hello World', 'tag' => 'h2', 'fontSize' => 'text-xl', 'color' => '#000', 'align' => 'left']],
            ],
        ],
    ]);

    $this->actingAs($this->user)
        ->postJson(route('studio.builder.projects.preview', $project->slug))
        ->assertOk()
        ->assertJsonFragment(['html' => '<h2 class="text-xl text-left font-bold" style="color: #000">Hello World</h2>']);
});

it('requires title when creating project', function () {
    $this->actingAs($this->user)
        ->post(route('studio.builder.projects.create'), [])
        ->assertSessionHasErrors('title');
});

it('lists user projects on dashboard', function () {
    ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'title' => 'My Project',
        'config' => ['components' => []],
    ]);

    $this->actingAs($this->user)
        ->get(route('studio.builder.index'))
        ->assertOk()
        ->assertSee('My Project');
});

it('can export a project to ZIP', function () {
    $project = ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'title' => 'Export Test Project',
        'config' => [
            'components' => [
                ['id' => '1', 'type' => 'text', 'label' => 'Title', 'properties' => ['content' => 'Hello', 'tag' => 'h2', 'fontSize' => 'text-xl', 'color' => '#000', 'align' => 'left']],
            ],
        ],
    ]);

    $this->actingAs($this->user)
        ->post(route('studio.builder.projects.export', $project->slug))
        ->assertOk()
        ->assertHeader('content-type', 'application/zip');
});

it('generates valid manifest in export', function () {
    $project = ExperienceProject::factory()->create([
        'user_id' => $this->user->id,
        'title' => 'Manifest Test',
        'description' => 'Test description',
        'config' => [
            'components' => [
                ['id' => '1', 'type' => 'text', 'label' => 'Title', 'properties' => ['content' => 'Hi']],
                ['id' => '2', 'type' => 'slider', 'label' => 'Speed', 'properties' => ['min' => 0, 'max' => 100]],
            ],
        ],
    ]);

    $this->actingAs($this->user)
        ->post(route('studio.builder.projects.export', $project->slug))
        ->assertOk();

    // Verify ZIP was created
    $zipPath = storage_path('app/exports/'.$project->slug.'.zip');
    expect(file_exists($zipPath))->toBeTrue();

    $zip = new ZipArchive;
    $zip->open($zipPath);
    expect($zip->locateName('index.html'))->not->toBeFalse()
        ->and($zip->locateName('manifest.json'))->not->toBeFalse();

    $manifest = json_decode($zip->getFromName('manifest.json'), true);
    expect($manifest['name'])->toBe('Manifest Test')
        ->and($manifest['description'])->toBe('Test description')
        ->and($manifest['components'])->toContain('text')
        ->and($manifest['components'])->toContain('slider')
        ->and($manifest['renderer'])->toBe('html');

    $zip->close();
});
