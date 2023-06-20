<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainFeedbackAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'complain_feedback_attachments',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('complain_feedback_id');
                $table->uuid('complain_id');
                $table->uuid('attachment_id');
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
        Schema::dropIfExists('complain_feedback_attachments');
    }
}
