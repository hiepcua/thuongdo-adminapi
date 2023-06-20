<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('code', 15)->comment('Mã nhân viên');
            $table->string('password');
            $table->uuid('department_id')->nullable();
            $table->string('phone_number', 13);
            $table->string('verify_code', 5)->nullable()->comment('Code reset mật khẩu');
            $table->tinyInteger('login_failed')->default(0)->comment('Số lần login failed');
            $table->timestamp('blocked_at')->nullable()->comment('Thời điểm tài khoản bị khóa');
            $table->tinyInteger('is_system')->default(0)->comment('Tài khoản hệ thống');
            $table->boolean('status')->default(1);
            $table->string('organization_id')->nullable();
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
        Schema::dropIfExists('users');
    }
}
