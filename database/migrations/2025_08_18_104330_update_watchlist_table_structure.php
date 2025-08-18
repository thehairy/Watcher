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
        Schema::table('watchlists', function (Blueprint $table) {
            // Drop old polymorphic columns if they exist
            if (Schema::hasColumn('watchlists', 'watchable_type')) {
                $table->dropColumn(['watchable_type', 'watchable_id']);
            }
            
            // Drop old datetime columns if they exist
            if (Schema::hasColumn('watchlists', 'started_at')) {
                $table->dropColumn(['started_at', 'completed_at', 'current_episode', 'current_season']);
            }
            
            // Add new foreign key columns
            if (!Schema::hasColumn('watchlists', 'show_id')) {
                $table->foreignId('show_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('watchlists', 'movie_id')) {
                $table->foreignId('movie_id')->nullable()->constrained()->onDelete('cascade');
            }
            
            // Add progress column
            if (!Schema::hasColumn('watchlists', 'progress')) {
                $table->integer('progress')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('watchlists', function (Blueprint $table) {
            $table->dropForeign(['show_id']);
            $table->dropForeign(['movie_id']);
            $table->dropColumn(['show_id', 'movie_id', 'progress']);
            
            $table->string('watchable_type');
            $table->unsignedBigInteger('watchable_id');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('current_episode')->nullable();
            $table->integer('current_season')->nullable();
        });
    }
};
