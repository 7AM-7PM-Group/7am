<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('esb_api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->text('request_url');
            $table->text('request_body')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('request_source', 20)->default('system');
            $table->string('request_type', 30)->default('sync_success');
            $table->timestamp('expired_at');
            $table->index('expired_at');
            $table->index('request_type');
            $table->integer('response_code')->nullable();
            $table->longText('response_body')->nullable();
            $table->boolean('success')->default(false);
            $table->boolean('retried_with_refresh')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('esb_api_request_logs');
    }
};
