<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->nullable(); // Changed to string to store jurnal_id
            $table->string('productID')->unique();
            $table->string('product_code');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('description');
            $table->integer('price');
            $table->string('unit');
            $table->integer('moq')->default(1);
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            // Foreign key → categories.jurnal_id
            $table->foreign('category_id')
                ->references('categoryID')
                ->on('categories')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
