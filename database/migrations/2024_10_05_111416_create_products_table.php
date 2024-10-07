<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('originalPrice', 10, 2);
            $table->decimal('discountedPrice', 10, 2);
            $table->string('image');
            $table->string('category');
            $table->boolean('isFlashSale');
            $table->boolean('isDiscount');
            $table->date('inputDate');
            $table->unsignedInteger('transactionCount');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
