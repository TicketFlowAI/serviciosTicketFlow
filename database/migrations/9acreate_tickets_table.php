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
            $table->tinyInteger('priority')->nullable();
            $table->boolean('needsHumanInteraction')->nullable();
            $table->tinyInteger('complexity')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->integer('status')->default(1);
            $table->boolean('newClientMessage')->default(0);
            $table->boolean('newTechnicianMessage')->default(0);
            $table->string('job_id')->nullable();
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
