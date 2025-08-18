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
        Schema::table('watch_progress', function (Blueprint $table) {
            $table->integer('tmdb_episode_id')->nullable()->after('episode_id');
            $table->integer('tmdb_show_id')->nullable()->after('show_id');
            
            // Make episode_id nullable since we'll be using tmdb_episode_id for some records
            $table->foreignId('episode_id')->nullable()->change();
            $table->foreignId('show_id')->nullable()->change();
            
            // Add unique constraint for tmdb-based tracking
            $table->unique(['user_id', 'tmdb_episode_id', 'tmdb_show_id'], 'unique_user_tmdb_episode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('watch_progress', function (Blueprint $table) {
            $table->dropUnique('unique_user_tmdb_episode');
            $table->dropColumn(['tmdb_episode_id', 'tmdb_show_id']);
            $table->foreignId('episode_id')->nullable(false)->change();
            $table->foreignId('show_id')->nullable(false)->change();
        });
    }
};
