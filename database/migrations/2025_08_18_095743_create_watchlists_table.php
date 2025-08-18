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
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('watchable_type'); // 'show' or 'movie'
            $table->unsignedBigInteger('watchable_id'); // show_id or movie_id
            $table->enum('status', ['watching', 'completed', 'on_hold', 'dropped', 'plan_to_watch'])->default('plan_to_watch');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('current_episode')->default(0); // For shows only
            $table->integer('current_season')->default(1); // For shows only
            $table->decimal('rating', 2, 1)->nullable(); // User rating 1-10
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'watchable_type', 'watchable_id']);
            $table->index(['watchable_type', 'watchable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};
