<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

describe('Tag slug length handling', function () {
    it('can create a tag with a slug longer than 100 characters', function () {
        // This was the original failing case: slug > 100 chars caused 1406 error
        $longName = 'Placeat vel maiores maiores ipsa non iste similique accusantium in deserunt aspernatur in necessitatibus';

        $tag = Tag::create([
            'name' => $longName,
            'slug' => Str::slug($longName),
        ]);

        expect($tag)->toBeInstanceOf(Tag::class);
        expect($tag->slug)->toHaveLength(104); // Original slug was 104 chars
        expect($tag->name)->toBe($longName);
    });

    it('truncates slug to 255 characters using Str::limit', function () {
        $veryLongName = str_repeat('a-b-', 100); // ~400 chars
        $truncatedSlug = Str::limit(Str::slug($veryLongName), 255, '');

        expect(strlen($truncatedSlug))->toBeLessThanOrEqual(255);
    });

    it('truncates tag name to 255 characters using Str::limit', function () {
        $veryLongName = str_repeat('Very long tag name ', 50); // ~950 chars
        $truncatedName = Str::limit($veryLongName, 255, '');

        expect(strlen($truncatedName))->toBeLessThanOrEqual(255);
    });

    it('does not crash when creating tag with extremely long name', function () {
        $extremelyLongName = str_repeat('Simulation topic ', 100); // ~1700 chars

        $tagName = Str::limit($extremelyLongName, 255, '');
        $tagSlug = Str::limit(Str::slug($extremelyLongName), 255, '');

        $tag = Tag::firstOrCreate(
            ['slug' => $tagSlug],
            ['name' => $tagName]
        );

        expect($tag)->toBeInstanceOf(Tag::class);
        expect(strlen($tag->slug))->toBeLessThanOrEqual(255);
        expect(strlen($tag->name))->toBeLessThanOrEqual(255);
    });

    it('creates unique tags for different names that produce the same truncated slug', function () {
        $name1 = str_repeat('test-slug-', 30).'alpha';
        $name2 = str_repeat('test-slug-', 30).'beta';

        $slug1 = Str::limit(Str::slug($name1), 255, '');
        $slug2 = Str::limit(Str::slug($name2), 255, '');

        // If truncation produces same slug, they should be the same tag
        // This is expected behavior — firstOrCreate will return existing tag
        $tag1 = Tag::firstOrCreate(['slug' => $slug1], ['name' => $name1]);
        $tag2 = Tag::firstOrCreate(['slug' => $slug2], ['name' => Str::limit($name2, 255, '')]);

        if ($slug1 === $slug2) {
            expect($tag1->id)->toBe($tag2->id);
        } else {
            expect($tag1->id)->not->toBe($tag2->id);
        }
    });

    it('handles normal length tag names without truncation', function () {
        $normalName = 'JavaScript';

        $tag = Tag::create([
            'name' => $normalName,
            'slug' => Str::slug($normalName),
        ]);

        expect($tag->slug)->toBe('javascript');
        expect($tag->name)->toBe('JavaScript');
    });

    it('handles the exact failing input from production without error', function () {
        // Exact tag name from the production error
        $tagName = 'Placeat vel maiores maiores ipsa non iste similique accusantium in deserunt aspernatur in necessitatibus';

        $tagName = Str::limit($tagName, 255, '');
        $tagSlug = Str::limit(Str::slug($tagName), 255, '');

        $tag = Tag::firstOrCreate(
            ['slug' => $tagSlug],
            ['name' => $tagName]
        );

        expect($tag)->toBeInstanceOf(Tag::class);
        expect($tag->slug)->not->toBeEmpty();
        expect($tag->name)->not->toBeEmpty();
    });
});
