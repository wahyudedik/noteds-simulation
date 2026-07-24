<?php

use App\Models\ForumCategory;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\ForumVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Forum Index & Category ───────────────────────────────────────

it('displays the forum index page', function () {
    $response = $this->get(route('forum.index'));

    $response->assertOk();
    $response->assertSee('Komunitas');
});

it('displays threads on the forum index', function () {
    $category = ForumCategory::factory()->create();
    $thread = ForumThread::factory()->create(['forum_category_id' => $category->id]);

    $response = $this->get(route('forum.index'));

    $response->assertOk();
    $response->assertSee($thread->title);
});

it('displays a category page', function () {
    $category = ForumCategory::factory()->create(['slug' => 'bantuan']);
    $thread = ForumThread::factory()->create(['forum_category_id' => $category->id]);

    $response = $this->get(route('forum.category', 'bantuan'));

    $response->assertOk();
    $response->assertSee($category->name);
    $response->assertSee($thread->title);
});

it('returns 404 for non-existent category', function () {
    $this->get(route('forum.category', 'non-existent'))->assertNotFound();
});

// ─── Thread CRUD ──────────────────────────────────────────────────

it('requires authentication to view create form', function () {
    $this->get(route('forum.create'))->assertRedirect();
});

it('displays the create thread form for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('forum.create'));

    $response->assertOk();
    $response->assertSee('Buat Thread');
});

it('creates a new thread', function () {
    $user = User::factory()->create();
    $category = ForumCategory::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.store'), [
        'title' => 'Test Thread Title',
        'body' => 'This is the body of the thread.',
        'forum_category_id' => $category->id,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('forum_threads', [
        'user_id' => $user->id,
        'forum_category_id' => $category->id,
        'title' => 'Test Thread Title',
    ]);

    // Category thread count should be incremented
    $category->refresh();
    expect($category->threads_count)->toBe(1);
});

it('validates required fields when creating a thread', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.store'), []);

    $response->assertSessionHasErrors(['title', 'body', 'forum_category_id']);
});

it('displays a thread', function () {
    $category = ForumCategory::factory()->create();
    $thread = ForumThread::factory()->create(['forum_category_id' => $category->id]);

    $response = $this->get(route('forum.show', $thread->slug));

    $response->assertOk();
    $response->assertSee($thread->title);
    $response->assertSee($thread->body);
});

it('increments view count when showing a thread', function () {
    $thread = ForumThread::factory()->create(['views_count' => 0]);

    $this->get(route('forum.show', $thread->slug));

    $thread->refresh();
    expect($thread->views_count)->toBe(1);
});

it('allows owner to edit thread', function () {
    $user = User::factory()->create();
    $category = ForumCategory::factory()->create();
    $thread = ForumThread::factory()->create([
        'user_id' => $user->id,
        'forum_category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->get(route('forum.edit', $thread->slug));

    $response->assertOk();
    $response->assertSee($thread->title);
});

it('prevents non-owner from editing thread', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $thread = ForumThread::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)->get(route('forum.edit', $thread->slug))->assertForbidden();
});

it('allows owner to update thread', function () {
    $user = User::factory()->create();
    $category = ForumCategory::factory()->create();
    $thread = ForumThread::factory()->create([
        'user_id' => $user->id,
        'forum_category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->put(route('forum.update', $thread->slug), [
        'title' => 'Updated Title',
        'body' => 'Updated body content.',
        'forum_category_id' => $category->id,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('forum_threads', [
        'id' => $thread->id,
        'title' => 'Updated Title',
    ]);
});

it('allows owner to delete thread', function () {
    $user = User::factory()->create();
    $category = ForumCategory::factory()->create(['threads_count' => 1]);
    $thread = ForumThread::factory()->create([
        'user_id' => $user->id,
        'forum_category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->delete(route('forum.destroy', $thread->slug));

    $response->assertRedirect(route('forum.index'));
    $this->assertDatabaseMissing('forum_threads', ['id' => $thread->id]);
});

it('prevents non-owner from deleting thread', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $thread = ForumThread::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)->delete(route('forum.destroy', $thread->slug))->assertForbidden();
});

it('allows admin to delete any thread', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $owner = User::factory()->create();
    $thread = ForumThread::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($admin)->delete(route('forum.destroy', $thread->slug))->assertRedirect();

    $this->assertDatabaseMissing('forum_threads', ['id' => $thread->id]);
});

// ─── Replies ──────────────────────────────────────────────────────

it('allows authenticated user to reply to a thread', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create();

    $response = $this->actingAs($user)->post(route('forum.reply', $thread->slug), [
        'body' => 'This is my reply.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('forum_replies', [
        'user_id' => $user->id,
        'forum_thread_id' => $thread->id,
        'body' => 'This is my reply.',
    ]);

    $thread->refresh();
    expect($thread->replies_count)->toBe(1);
});

it('prevents replying to a locked thread', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->locked()->create();

    $response = $this->actingAs($user)->post(route('forum.reply', $thread->slug), [
        'body' => 'This should fail.',
    ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('forum_replies', [
        'user_id' => $user->id,
        'forum_thread_id' => $thread->id,
    ]);
});

it('allows owner to delete reply', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create(['replies_count' => 1]);
    $reply = ForumReply::factory()->create([
        'user_id' => $user->id,
        'forum_thread_id' => $thread->id,
    ]);

    $response = $this->actingAs($user)->delete(route('forum.reply.destroy', $reply->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('forum_replies', ['id' => $reply->id]);
});

it('prevents non-owner from deleting reply', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $thread = ForumThread::factory()->create();
    $reply = ForumReply::factory()->create([
        'user_id' => $owner->id,
        'forum_thread_id' => $thread->id,
    ]);

    $this->actingAs($other)->delete(route('forum.reply.destroy', $reply->id))->assertForbidden();
});

it('allows thread owner to accept a reply', function () {
    $owner = User::factory()->create();
    $replyUser = User::factory()->create();
    $thread = ForumThread::factory()->create(['user_id' => $owner->id]);
    $reply = ForumReply::factory()->create([
        'user_id' => $replyUser->id,
        'forum_thread_id' => $thread->id,
    ]);

    $response = $this->actingAs($owner)->post(route('forum.reply.accept', $reply->id));

    $response->assertRedirect();

    $reply->refresh();
    $thread->refresh();

    expect($reply->is_accepted)->toBeTrue();
    expect($thread->is_solved)->toBeTrue();
});

it('prevents non-owner from accepting reply', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $thread = ForumThread::factory()->create(['user_id' => $owner->id]);
    $reply = ForumReply::factory()->create(['forum_thread_id' => $thread->id]);

    $this->actingAs($other)->post(route('forum.reply.accept', $reply->id))->assertForbidden();
});

// ─── Votes ────────────────────────────────────────────────────────

it('allows user to upvote a thread', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create(['votes_count' => 0]);

    $response = $this->actingAs($user)->postJson(route('forum.vote'), [
        'votable_type' => 'thread',
        'votable_id' => $thread->id,
        'value' => 1,
    ]);

    $response->assertOk();
    $response->assertJson(['success' => true, 'action' => 'added']);

    $thread->refresh();
    expect($thread->votes_count)->toBe(1);
    $this->assertDatabaseHas('forum_votes', [
        'user_id' => $user->id,
        'votable_type' => ForumThread::class,
        'votable_id' => $thread->id,
        'value' => 1,
    ]);
});

it('allows user to downvote a thread', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create(['votes_count' => 0]);

    $response = $this->actingAs($user)->postJson(route('forum.vote'), [
        'votable_type' => 'thread',
        'votable_id' => $thread->id,
        'value' => -1,
    ]);

    $response->assertOk();
    $response->assertJson(['success' => true, 'action' => 'added']);

    $thread->refresh();
    expect($thread->votes_count)->toBe(-1);
});

it('toggles off vote when same direction clicked again', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create(['votes_count' => 1]);

    ForumVote::factory()->create([
        'user_id' => $user->id,
        'votable_type' => ForumThread::class,
        'votable_id' => $thread->id,
        'value' => 1,
    ]);

    $response = $this->actingAs($user)->postJson(route('forum.vote'), [
        'votable_type' => 'thread',
        'votable_id' => $thread->id,
        'value' => 1,
    ]);

    $response->assertOk();
    $response->assertJson(['success' => true, 'action' => 'removed']);

    $this->assertDatabaseMissing('forum_votes', [
        'user_id' => $user->id,
        'votable_type' => ForumThread::class,
        'votable_id' => $thread->id,
    ]);
});

it('switches vote direction when opposite direction clicked', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create(['votes_count' => -1]);

    ForumVote::factory()->create([
        'user_id' => $user->id,
        'votable_type' => ForumThread::class,
        'votable_id' => $thread->id,
        'value' => -1,
    ]);

    $response = $this->actingAs($user)->postJson(route('forum.vote'), [
        'votable_type' => 'thread',
        'votable_id' => $thread->id,
        'value' => 1,
    ]);

    $response->assertOk();
    $response->assertJson(['success' => true, 'action' => 'switched']);

    $thread->refresh();
    expect($thread->votes_count)->toBe(1);
});

it('prevents voting on own content', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson(route('forum.vote'), [
        'votable_type' => 'thread',
        'votable_id' => $thread->id,
        'value' => 1,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
});

it('allows voting on a reply', function () {
    $user = User::factory()->create();
    $reply = ForumReply::factory()->create(['votes_count' => 0]);

    $response = $this->actingAs($user)->postJson(route('forum.vote'), [
        'votable_type' => 'reply',
        'votable_id' => $reply->id,
        'value' => 1,
    ]);

    $response->assertOk();
    $response->assertJson(['success' => true, 'action' => 'added']);

    $reply->refresh();
    expect($reply->votes_count)->toBe(1);
});

// ─── Admin Actions (Lock / Pin) ──────────────────────────────────

it('allows admin to toggle lock on thread', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $thread = ForumThread::factory()->create(['is_locked' => false]);

    $response = $this->actingAs($admin)->post(route('forum.lock', $thread->slug));

    $response->assertRedirect();
    $thread->refresh();
    expect($thread->is_locked)->toBeTrue();
});

it('prevents non-admin from locking thread', function () {
    $user = User::factory()->create(['role' => 'user']);
    $thread = ForumThread::factory()->create();

    $this->actingAs($user)->post(route('forum.lock', $thread->slug))->assertForbidden();
});

it('allows admin to toggle pin on thread', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $thread = ForumThread::factory()->create(['is_pinned' => false]);

    $response = $this->actingAs($admin)->post(route('forum.pin', $thread->slug));

    $response->assertRedirect();
    $thread->refresh();
    expect($thread->is_pinned)->toBeTrue();
});

it('prevents non-admin from pinning thread', function () {
    $user = User::factory()->create(['role' => 'user']);
    $thread = ForumThread::factory()->create();

    $this->actingAs($user)->post(route('forum.pin', $thread->slug))->assertForbidden();
});

// ─── Model Relationships ──────────────────────────────────────────

it('has correct user relationship on thread', function () {
    $user = User::factory()->create();
    $thread = ForumThread::factory()->create(['user_id' => $user->id]);

    expect($thread->user->id)->toBe($user->id);
});

it('has correct category relationship on thread', function () {
    $category = ForumCategory::factory()->create();
    $thread = ForumThread::factory()->create(['forum_category_id' => $category->id]);

    expect($thread->category->id)->toBe($category->id);
});

it('has correct replies relationship on thread', function () {
    $thread = ForumThread::factory()->create();
    ForumReply::factory()->create(['forum_thread_id' => $thread->id]);
    ForumReply::factory()->create(['forum_thread_id' => $thread->id]);

    expect($thread->replies)->toHaveCount(2);
});

it('has correct user relationship on reply', function () {
    $user = User::factory()->create();
    $reply = ForumReply::factory()->create(['user_id' => $user->id]);

    expect($reply->user->id)->toBe($user->id);
});

it('has correct thread relationship on reply', function () {
    $thread = ForumThread::factory()->create();
    $reply = ForumReply::factory()->create(['forum_thread_id' => $thread->id]);

    expect($reply->thread->id)->toBe($thread->id);
});

it('has correct votes relationship on thread', function () {
    $thread = ForumThread::factory()->create();
    ForumVote::factory()->create([
        'votable_type' => ForumThread::class,
        'votable_id' => $thread->id,
    ]);

    expect($thread->votes)->toHaveCount(1);
});

it('tracks user forum threads and replies', function () {
    $user = User::factory()->create();
    ForumThread::factory()->create(['user_id' => $user->id]);
    ForumThread::factory()->create(['user_id' => $user->id]);
    ForumReply::factory()->create(['user_id' => $user->id]);

    expect($user->forumThreads)->toHaveCount(2);
    expect($user->forumReplies)->toHaveCount(1);
});
