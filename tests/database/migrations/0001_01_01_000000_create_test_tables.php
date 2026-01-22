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
        // Table for Spatie translatable - uses JSON column
        Schema::create('spatie_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('author');
            $table->json('title')->nullable(); // Spatie stores translations in JSON
            $table->json('content')->nullable();
            $table->timestamps();
        });

        // Table for Astrotomic translatable - main table
        Schema::create('astrotomic_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('author');
            $table->timestamps();
        });

        // Translations table for Astrotomic translatable
        Schema::create('astrotomic_post_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('astrotomic_post_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->text('content')->nullable();

            $table->unique(['astrotomic_post_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrotomic_post_translations');
        Schema::dropIfExists('astrotomic_posts');
        Schema::dropIfExists('spatie_posts');
    }
};
