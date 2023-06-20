<?php

use App\Constants\ComplainConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->string('code', 20)->nullable()->comment('Mã khiếu nại');
            $table->uuid('complain_type_id')->comment('Loại khiếu nại');
            $table->string('solution_id')->comment('Phương án giải quyết');
            $table->integer('comment_number')->default(0)->comment('Số lượng phản hồi');
            $table->uuid('staff_care_id')->nullable()->comment('Nhân viên chăm sóc');
            $table->uuid('staff_complain_id')->nullable()->comment('Nhân viên khiếu nại');
            $table->uuid('staff_management_id')->nullable()->comment('Nhân quản lý');
            $table->uuid('customer_id')->nullable()->comment('Khách hàng');
            $table->string('status', 20)->default(ComplainConstant::KEY_STATUS_PENDING)->comment(
                'Trạng thái'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complains');
    }
}
