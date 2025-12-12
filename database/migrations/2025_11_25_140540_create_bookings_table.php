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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('apartment_id')->constrained('apartments');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('new_start_date')->nullable();
        $table->date('new_end_date')->nullable();
        $table->enum('modify_status', ['none','pending','approved','rejected'])
              ->default('none');
        // pending - waiting for owner approval
        // approved - owner approved
        // rejected - owner rejected
        // cancelled - cancelled by renter
            $table->enum('status',['pending','approved','rejected','cancelled','ended'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
