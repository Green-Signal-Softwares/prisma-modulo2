<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitations', function (Blueprint $table) {
            $table->foreignId('atendente_id')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('solicitations', function (Blueprint $table) {
            $table->dropForeign(['atendente_id']);
            $table->dropColumn('atendente_id');
        });
    }
};
