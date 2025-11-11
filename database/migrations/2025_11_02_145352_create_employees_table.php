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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number');
            $table->float('deduction_gsis_mpl', 2);
            $table->float('deduction_pagibig_mp3', 2);
            $table->float('deduction_pagibig_calamity', 2);
            $table->float('deduction_city_savings', 2);
            $table->float('deduction_withholding_tax', 2);
            $table->float('deduction_igp_cottage', 2);
            $table->float('deduction_cfi', 2);
            $table->string("device")->nullable();
            $table->integer("fingerprint_id")->nullable();
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
