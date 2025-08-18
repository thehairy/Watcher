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
        Schema::dropIfExists('watchlists');
        
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('show_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('status', ['watching', 'completed', 'plan_to_watch', 'on_hold', 'dropped'])
                  ->default('plan_to_watch');
            $table->integer('progress')->default(0);
            $table->decimal('rating', 2, 1)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
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
