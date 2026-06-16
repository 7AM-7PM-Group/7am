<?php

use App\Models\Business;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_pricelists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Business::class, 'customer_id')->constrained();
            $table->foreignIdFor(Product::class)->constrained();

            $table->double('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_pricelists');
    }
};