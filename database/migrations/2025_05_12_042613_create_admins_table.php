<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED PRIMARY KEY
            $table->string('name');
            $table->string('email')->unique(); // Admin login identity
            $table->timestamp('email_verified_at')->nullable(); // Optional verification
            $table->string('password'); // Hashed password

            $table->string('token')->nullable(); // Used for session reset/login API
            $table->string('photo')->nullable(); // Profile photo
            $table->string('phone')->nullable(); // Optional contact number
            $table->string('address')->nullable(); // Admin address if used

            $table->string('role')->default('admin'); // For RBAC: e.g., 'admin', 'superadmin'
            $table->string('status')->default('1'); // '1' = active, '0' = suspended

            $table->rememberToken(); // Laravel’s persistent login token
            $table->timestamps();    // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
