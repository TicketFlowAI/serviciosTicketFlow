<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets');
            $table->foreignId('user_id')->constrained('users');
            $table->string('action'); // e.g., 'assigned', 'updated', 'closed'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_histories');
    }
};
