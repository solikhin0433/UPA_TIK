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
        // Tabel pages
        Schema::create('pages', function (Blueprint $table) {
            $table->id('pages_id');
            $table->string('title');
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->longText('content')->nullable();
            $table->timestamps();

            // Foreign Key untuk user_id mengacu pada m_user(user_id)
            $table->foreign('user_id')->references('user_id')->on('m_user')->onDelete('cascade');
        });
        // Tabel menus
        Schema::create('menus', function (Blueprint $table) {
            $table->id('menus_id');
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->tinyInteger('order_number')->default(1);
            $table->string('slug')->nullable();
            $table->timestamps();

            // Foreign Key ke parent_id mengacu pada menus(menus_id)
            $table->foreign('parent_id')->references('menus_id')->on('menus')->onDelete('set null');
            // Foreign Key ke page_id mengacu pada pages(pages_id)
            $table->foreign('page_id')->references('pages_id')->on('pages')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
        Schema::dropIfExists('pages');
    }
};