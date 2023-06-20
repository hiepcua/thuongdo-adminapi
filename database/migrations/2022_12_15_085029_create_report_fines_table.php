<?php

use App\Constants\FineConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportFinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'report_fines',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('organization_id');
                foreach (FineConstant::STATUSES as $key => $value) {
                    $table->integer($key)->default(0)->comment($value);
                }
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_fines');
    }
}
