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
            $table->foreignId('devise_id')->nullable()->after('frais_transport')->constrained()->onDelete('set null');
            $table->foreignId('tarif_id')->nullable()->after('devise_id')->constrained()->onDelete('set null');
            $table->decimal('frais_calcule', 10, 2)->nullable()->after('tarif_id'); // Prix calculé automatiquement
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colis', function (Blueprint $table) {
            $table->dropForeign(['devise_id']);
            $table->dropForeign(['tarif_id']);
            $table->dropColumn(['devise_id', 'tarif_id', 'frais_calcule']);
        });
    }
};

