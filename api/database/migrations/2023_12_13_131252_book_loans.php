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
        Schema::create('book_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('book_id');
            $table->date('loan_date')->default(now());
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->tinyInteger('extended')->default(0);
            $table->date('extension_date')->nullable();
            $table->integer('penalty_amount')->nullable();
            $table->string('penalty_status', 15)->nullable();
            $table->string('status', 20);
            $table->integer('added_by');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->primary('id');
            $table->index('user_id');
            $table->index('book_id');

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('book_id')->references('id')->on('books');
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
