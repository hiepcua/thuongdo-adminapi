<?php

use App\Constants\ServiceConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 15)->comment('Mã khách hàng');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('password');
            $table->string('phone_number', 13);
            $table->string('address')->nullable();
            $table->uuid('district_id')->nullable();
            $table->uuid('province_id')->nullable();
            $table->date('bod')->nullable();
            $table->enum('gender', ['male', 'female', 'undetermined'])->nullable();
            $table->string('via')->nullable()->comment('Nguồn mà khách hàng tiếp cận: fb, gg');
            $table->string('facebook_link')->nullable();
            $table->enum('business_type', ['business', 'personal'])->nullable()->comment('Loại hình kinh doanh: cá nhân, doanh nghiệp');
            $table->enum('type', ['order', 'consignment'])->nullable()->comment('Loại hình khách hàng.');
            $table->enum('delivery_type', ServiceConstant::DELIVERIES)->default('normal')->comment('Vận chuyển: nhanh, bình thường');
            $table->tinyInteger('level')->default(0)->comment('Cấp độ khách hàng');
            $table->uuid('staff_counselor_id')->nullable()->comment('Nhân viên tư vấn');
            $table->uuid('staff_care_id')->nullable()->comment('Nhân viên chăm sóc');
            $table->uuid('staff_sale_id')->nullable()->comment('Nhân viên kinh doanh');
            $table->uuid('staff_order_id')->nullable()->comment('Nhân viên đặt hàng');
            $table->uuid('warehouse_id')->nullable()->comment('Kho hàng nhận hàng');
            $table->dateTime('last_order_at')->nullable()->comment('Ngày đặt hàng gần nhất');
            $table->uuid('label_id')->nullable()->comment('Nhãn');
            $table->uuid('organization_id')->nullable();
            $table->tinyInteger('service')->default(1);
            $table->string('verify_code', 5)->nullable()->comment('Code reset mật khẩu');
            $table->uuid('customer_reason_inactive_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
