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
            $table->foreignId('access_profile_id')->nullable()->constrained('access_profiles')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('login')->nullable();
            $table->string('status')->default('ativo'); // ativo, ausente, inativo, bloqueado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['access_profile_id']);
            $table->dropColumn(['access_profile_id', 'phone', 'login', 'status']);
        });
    }
};
