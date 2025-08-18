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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('password')->nullable()->change();
            
            // Social login fields
            $table->string('discord_id')->nullable()->unique();
            $table->string('github_id')->nullable()->unique();
            $table->string('steam_id')->nullable()->unique();
            $table->string('avatar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'discord_id',
                'github_id', 
                'steam_id',
                'avatar'
            ]);
            $table->string('password')->nullable(false)->change();
        });
    }
};
