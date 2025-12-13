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
        Schema::table('caisses', function (Blueprint $table) {
            $table->foreignId('devise_id')->nullable()->after('responsable_id')
                  ->constrained('devises')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caisses', function (Blueprint $table) {
            $table->dropForeign(['devise_id']);
            $table->dropColumn('devise_id');
        });
    }
};
