<?php

use App\Constants\PackageConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_package', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('order');
            $table->string('order_code', 40)->nullable()->comment('Mã đơn hàng');
            $table->string('code')->nullable()->comment('Mâ kiện');
            $table->string('code_po')->nullable()->comment('Mã đặt hàng');
            $table->string('bill_code')->nullable()->comment('Mã vận đơn');
            $table->uuid('delivery_id')->nullable()->comment('Mã giao hàng');
            $table->uuid('warehouse_id')->nullable()->comment('Kho nhận ở VN');
            $table->uuid('warehouse_cn')->nullable()->comment('Kho Nhận ở TQ');
            $table->string('product_name', 500)->nullable()->comment('Tên hàng hóa');
            $table->integer('quantity')->default(0)->comment('Số lượng kiểm đếm');
            $table->integer('package_number')->default(0)->comment('Số kiện');
            $table->decimal('order_cost', 20)->default(0)->comment('Giá trị hàng hóa');
            $table->decimal('inspection_cost', 10)->default(0)->comment('Phí Kiểm hàng');
            $table->boolean('is_inspection')->default(0)->comment('Kiểm hàng');
            $table->decimal('insurance_cost', 10)->default(0)->comment('Chi phí Bảo hiểm');
            $table->boolean('is_insurance')->default(0)->comment('Bảo hiểm');
            $table->decimal('woodworking_cost', 10)->default(0)->comment('Phí Đóng gỗ');
            $table->boolean('is_woodworking')->default(0)->comment('Đóng gỗ');
            $table->float('height')->default(0)->comment('Chiều cao của đơn hàng (cm).');
            $table->float('length')->default(0)->comment('Chiều dài của đơn hàng (cm).');
            $table->float('width')->default(0)->comment('Chiều rộng của đơn hàng (cm).');
            $table->float('weight')->default(0)->comment('Trọng lượng');
            $table->boolean('is_delivery')->default(0)->comment('Yêu cầu giao hàng');
            $table->decimal('delivery_cost', 20)->default(0)->comment('Chi phí vận chuyển nội địa');
            $table->enum('delivery_type', ['normal', 'fast'])->default('normal')->comment('Ký gửi - Loại vận chuyển');
            $table->string('in_transit', 10)->nullable()->comment('Ký gửi - Vận chuyển từ A đến B: A - B');
            $table->string('transporter')->nullable()->comment('Ký gửi Hãng vận chuyển');
            $table->uuid('transporter_id')->nullable()->comment('Hãng vận chuyển');
            $table->decimal('exchange_rate', 20)->default(0)->comment('Tỷ giá');
            $table->decimal('international_shipping_cost', 20)->default(0)->comment('Phí vận chuyển quốc tế');
            $table->decimal('china_shipping_cost', 20)->default(0)->comment('Phí vận chuyển TQ');
            $table->boolean('is_shock_proof')->default(0)->comment('Chống shock');
            $table->decimal('shock_proof_cost', 20)->default(0)->comment('Phí chống sốc');
            $table->decimal('storage_cost', 20)->default(0)->comment('Phí lưu kho');
            $table->decimal('discount_cost', 20)->default(0)->comment('Giá Chiết khâú');
            $table->float('discount_percent')->default(0)->comment('% Chiết khâú');
            $table->decimal('amount', 20)->default(0)->comment('Tổng phí vận chuyển');
            $table->string('note', 500)->nullable()->comment('Ghi chú + Ký gửi');
            $table->string('note_ordered', 500)->nullable()->comment('Ghi chú Đặt hàng');
            $table->string('description', 500)->nullable()->comment('Nội dung + Ký gửi');
            $table->uuid('customer_id')->comment('Khách hàng');
            $table->uuid('customer_delivery_id')->comment('Giao nhận');
            $table->uuid('category_id')->nullable()->comment('Danh mục');
            $table->string('category')->nullable()->comment('Danh mục khác');
            $table->uuid('staff_care_id')->nullable()->comment('Nhân viên chăm sóc');
            $table->uuid('staff_quotation_id')->nullable()->comment('Nhân viên báo giá');
            $table->uuid('staff_order_id')->nullable()->comment('Nhân viên đặt hàng');
            $table->uuid('staff_counselor_id')->nullable()->comment('Nhân viên tư vân');
            $table->dateTime('date_ordered')->nullable()->comment('Ngày đặt hàng');
            $table->dateTime('bill_code_at')->nullable()->comment('Ngày đặt mã vận đơn');
            $table->boolean('is_extension')->default(0)->nullable()->comment('Kiện được tạo từ đơn extension');
            $table->string('status', 20)->default(PackageConstant::STATUS_PENDING)->comment('Trạng thái theo Key');
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
        Schema::dropIfExists('order_package');
    }
}
