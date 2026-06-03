<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('esb_api_deletion_audits', function (Blueprint $table) {
            $table->id();
            $table->integer('deleted_records_count')->default(0);
            $table->string('request_type', 30)->nullable();
            $table->timestamp('deleted_before');
            $table->text('deletion_criteria')->nullable();
            $table->text('error_message')->nullable();
            $table->boolean('success')->default(true);
            $table->string('triggered_by', 50)->default('scheduler');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('esb_api_deletion_audits');
    }
};
