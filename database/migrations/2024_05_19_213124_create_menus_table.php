<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PRIMARY KEY
            $table->string('menu_name');       // E.g., "Lunch", "Snacks", "Drinks"
            $table->string('image')->nullable(); // Optional image for the menu

            // Missing in your version — links the menu to a client (restaurant)
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->onDelete('cascade'); // Ensures data integrity

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
