<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();;
            $table->uuid('supplier_type_id')->nullable()->comment('Loại nhà cung cấp.');
            $table->string('position')->nullable();
            $table->string('details')->nullable();
            $table->uuid('supplier_id')->nullable()->comment('Nhà cung cấp.');
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
        Schema::dropIfExists('contact_methods');
    }
}
