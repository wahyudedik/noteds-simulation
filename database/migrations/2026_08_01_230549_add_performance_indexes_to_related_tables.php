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
        $this->addIndexIfMissing('play_history', ['user_id', 'simulation_id']);
        $this->addIndexIfMissing('play_history', ['user_id', 'created_at']);
        $this->addIndexIfMissing('comments', ['simulation_id', 'created_at']);
        $this->addIndexIfMissing('notifications', ['user_id', 'read_at']);
        $this->addIndexIfMissing('ratings', ['simulation_id']);
        $this->addIndexIfMissing('bookmarks', ['simulation_id']);
        $this->addIndexIfMissing('reactions', ['simulation_id']);
        $this->addIndexIfMissing('shares', ['simulation_id']);
        $this->addIndexIfMissing('simulations', ['user_id', 'is_published']);
        $this->addIndexIfMissing('simulations', ['is_published', 'created_at']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfMissing('play_history', ['user_id', 'simulation_id']);
        $this->dropIndexIfMissing('play_history', ['user_id', 'created_at']);
        $this->dropIndexIfMissing('comments', ['simulation_id', 'created_at']);
        $this->dropIndexIfMissing('notifications', ['user_id', 'read_at']);
        $this->dropIndexIfMissing('ratings', ['simulation_id']);
        $this->dropIndexIfMissing('bookmarks', ['simulation_id']);
        $this->dropIndexIfMissing('reactions', ['simulation_id']);
        $this->dropIndexIfMissing('shares', ['simulation_id']);
        $this->dropIndexIfMissing('simulations', ['user_id', 'is_published']);
        $this->dropIndexIfMissing('simulations', ['is_published', 'created_at']);
    }

    /**
     * Add a composite index only if no index exists for the given columns.
     */
    private function addIndexIfMissing(string $table, array $columns): void
    {
        if (! $this->columnsAreIndexed($table, $columns)) {
            Schema::table($table, function (Blueprint $table) use ($columns) {
                $table->index($columns);
            });
        }
    }

    /**
     * Drop a composite index only if it exists for the given columns.
     */
    private function dropIndexIfMissing(string $table, array $columns): void
    {
        $indexName = $this->getIndexName($table, $columns);

        if ($this->columnsAreIndexed($table, $columns)) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }

    /**
     * Check if the given columns are already indexed on a table.
     * Uses Schema::getIndexes() which works across MySQL, SQLite, etc.
     */
    private function columnsAreIndexed(string $table, array $columns): bool
    {
        $indexes = Schema::getIndexes($table);

        $sortedColumns = $columns;
        sort($sortedColumns);

        foreach ($indexes as $index) {
            $indexColumns = $index['columns'] ?? [];
            sort($indexColumns);

            if ($indexColumns === $sortedColumns) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate the standard Laravel index name for a table and columns.
     */
    private function getIndexName(string $table, array $columns): string
    {
        return $table.'_'.implode('_', $columns).'_index';
    }
};
