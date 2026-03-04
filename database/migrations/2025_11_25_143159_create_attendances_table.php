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
            $table->enum("tag", ['present', 'absent'])->nullable();
            $table->date("date");
            $table->time("am_login")->nullable();
            $table->time("am_logout")->nullable();
            $table->time("pm_login")->nullable();
            $table->time("pm_logout")->nullable();
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
