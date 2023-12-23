<?php

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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('publisher',50); 
            $table->string('isbn',50); 
            $table->string('category',100); 
            $table->string('sub_category',100);
            $table->text('description');
            $table->integer('pages');
            $table->string('image',200);
            $table->unsignedBigInteger('added_by');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('added_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
