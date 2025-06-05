<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('token')->nullable();          // Used for API/session/reset
            $table->string('photo')->nullable();          // Profile image
            $table->string('phone')->nullable();          // Contact number
            $table->string('address')->nullable();        // Address or campus location
            $table->text('shop_info')->nullable();        // Description/about the restaurant
            $table->string('cover_photo')->nullable();    // Banner/header image
            $table->string('role')->default('client');    // Useful if managers are added later
            $table->string('status')->default('1');       // '1' = active, '0' = inactive
            $table->rememberToken();                      // For Laravel's auth "remember me"
            $table->timestamps();                         // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
