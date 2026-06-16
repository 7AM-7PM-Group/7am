<?php

use App\Models\SetCategory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bussinesses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->unsignedBigInteger('customerID')->unique()->nullable();
            $table->string('name');
            $table->text('address');
            $table->string('npwp');
            $table->string('bank');
            $table->string('account_number');
            $table->string('account_name');
            $table->string('representative');
            $table->string('id_card');
            $table->string('phone');
            $table->integer('tenor')->default(0);
            $table->enum('status', ['requested', 'approved', 'rejected'])->default('requested');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};