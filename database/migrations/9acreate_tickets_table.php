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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_contract_id')->constrained();
            $table->string('title');
            $table->tinyInteger('priority');
            $table->boolean('needsHumanInteraction');
            $table->tinyInteger('complexity');
            $table->foreignId('user_id')->constrained();
            $table->boolean('status')->default(1);
            $table->boolean('newClientMessage')->default(0);
            $table->boolean('newTechnicianMessage')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
