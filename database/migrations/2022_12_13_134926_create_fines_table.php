<?php

use App\Constants\FineConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'fines',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id')->comment('NV Vi phạm');
                $table->uuid('cause_id')->comment('Người tạo');
                $table->nullableUuidMorphs('source');
                $table->string('order_code')->nullable()->comment('Mã đơn hàng');
                $table->string('bill_code')->nullable()->comment('Mã vận đơn');
                $table->decimal('amount', 20);
                $table->integer('comment_number')->default(0);
                $table->string('reason', 500)->comment('Nguyên nhân');
                $table->string('solution', 500)->comment('Phương án giải quyết');
                $table->enum('type', array_keys(FineConstant::TYPES))->default(FineConstant::TYPE_ORDER)->comment('Loại');
                $table->enum('status', array_keys(FineConstant::STATUSES))->default(FineConstant::KEY_STATUS_PENDING)->comment('Trạng thái');
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
        Schema::dropIfExists('fines');
    }
}
