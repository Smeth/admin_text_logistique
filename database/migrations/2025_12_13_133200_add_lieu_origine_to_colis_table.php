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
        Schema::table('colis', function (Blueprint $table) {
            $table->string('pays_origine')->nullable()->after('agence_arrivee_id');
            $table->string('ville_origine')->nullable()->after('pays_origine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->dropColumn(['pays_origine', 'ville_origine']);
        });
    }
};
