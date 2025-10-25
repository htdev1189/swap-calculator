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
        Schema::create('swap_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('pair');
            $table->float('lot_size');
            $table->string('type');
            $table->float('swap_rate');
            $table->integer('days');
            $table->float('total_swap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap_calculations');
    }
};
