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
        Schema::create('payroll_datas', function (Blueprint $table) {
            $table->id();
            $table->foreignId("payroll_id")->constrained("payrolls")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId("employee_id")->constrained("employees")->onDelete("cascade")->onUpdate("cascade");
            $table->float("rate");
            $table->float("pera");
            $table->float("period_earned");
            $table->float("gsis_mpl");
            $table->float("philhealth");
            $table->float("local_pave");
            $table->float("life_retirement");
            $table->float("pagibig_premium");
            $table->float("pagibig_mp3");
            $table->float("pagibig_calamity");
            $table->float("city_savings");
            $table->float("withholding_tax");
            $table->float("absence_wo_pay");
            $table->float("cottage_rental");
            $table->float("essu_fa");
            $table->float("retiree_asst");
            $table->float("essu_union");
            $table->float("cfi");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_datas');
    }
};
