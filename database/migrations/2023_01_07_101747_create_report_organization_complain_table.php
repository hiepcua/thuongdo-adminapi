<?php

use App\Constants\ComplainConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateReportOrganizationComplainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $columns = '';
        foreach (ComplainConstant::STATUSES as $key => $value) {
            $columns .= ",sum(`report_complains`.`$key`) AS `$key`";
        }
        $columns = trim($columns, ',');
        DB::statement(
            "CREATE VIEW `report_organization_complain` AS
select
organization_id,
    $columns
from
    `report_complains`
group by
    `report_complains`.`organization_id`;"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW report_organization_complain");
    }
}
