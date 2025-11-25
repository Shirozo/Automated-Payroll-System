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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId("employee_id")->constrained("employees")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId("device_id")->nullable()->constrained("devices")->onDelete("set null")->onUpdate("cascade");
            $table->enum("action", ['am_login', 'am_logout', 'pm_login', 'pm_logout']);
            $table->enum("tag", ['present', 'late'])->nullable();
            $table->date("date");
            $table->time("time");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
