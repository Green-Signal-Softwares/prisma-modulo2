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
        Schema::create('solicitation_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('nota');
            $table->boolean('problema_resolvido');
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->unique(['solicitation_id', 'user_id']);
            $table->index('nota');
            $table->index('problema_resolvido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitation_evaluations');
    }
};
