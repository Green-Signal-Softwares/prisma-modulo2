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
        Schema::create('solicitation_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('atendente_id')->constrained('users')->cascadeOnDelete();
            $table->string('category')->nullable()->index();
            $table->string('problema_identificado');
            $table->string('solucao_aplicada');
            $table->string('encaminhamento')->nullable();
            $table->text('descricao');
            $table->timestamps();

            $table->index('atendente_id');
            $table->index('solucao_aplicada');
            $table->index('problema_identificado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitation_checklists');
    }
};
