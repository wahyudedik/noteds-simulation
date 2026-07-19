<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('simulations')) {
            return;
        }

        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('subcategory')->nullable();
            $table->string('tags')->nullable(); // comma-separated tags
            $table->string('thumbnail')->nullable();
            $table->string('version')->default('1.0.0');
            $table->string('zip_path'); // path to simulation.zip
            $table->string('entry_point')->default('index.html');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('play_count')->default(0);
            $table->unsignedBigInteger('view_count')->default(0);
            $table->unsignedBigInteger('like_count')->default(0);
            $table->unsignedBigInteger('bookmark_count')->default(0);
            $table->unsignedBigInteger('share_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedinteger('rating_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('is_published');
            $table->index('is_featured');
            $table->index('play_count');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulations');
    }
};
