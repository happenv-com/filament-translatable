<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('spatie_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('author')->nullable();
            $table->json('title')->nullable();
            $table->json('content')->nullable();
            $table->timestamps();
        });

        Schema::create('astrotomic_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('author')->nullable();
            $table->timestamps();
        });

        Schema::create('astrotomic_post_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astrotomic_post_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->unique(['astrotomic_post_id', 'locale']);
        });
    }
};
