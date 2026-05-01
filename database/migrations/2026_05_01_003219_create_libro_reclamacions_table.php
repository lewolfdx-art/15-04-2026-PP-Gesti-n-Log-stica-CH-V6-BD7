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
        Schema::create('libro_reclamacions', function (Blueprint $table) {
            $table->id();
            $table->string('claimant_name');
            $table->string('document_type'); // DNI, CE, etc.
            $table->string('document_number');
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('order_number')->nullable();
            $table->text('claim_description');
            $table->text('petition')->nullable();
            $table->string('status')->default('pending'); // pending, in_process, resolved, rejected
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libro_reclamacions');
    }
};
