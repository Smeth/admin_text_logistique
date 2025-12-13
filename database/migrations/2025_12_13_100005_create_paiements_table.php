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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coli_id')->constrained('colis')->onDelete('cascade');
            $table->foreignId('caisse_id')->nullable()->constrained('caisses')->onDelete('set null');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->decimal('montant', 10, 2);
            $table->foreignId('devise_id')->nullable()->constrained('devises')->onDelete('set null');
            $table->enum('mode_paiement', ['espece', 'carte', 'virement', 'cheque', 'mobile_money'])->default('espece');
            $table->date('date_paiement');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};

