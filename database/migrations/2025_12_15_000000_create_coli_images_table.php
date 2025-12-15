<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coli_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coli_id')
                ->constrained('colis')
                ->onDelete('cascade');
            $table->string('path');
            $table->string('original_name');
            $table->unsignedInteger('size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coli_images');
    }
};


