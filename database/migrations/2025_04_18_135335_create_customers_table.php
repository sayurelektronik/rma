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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            // Relasi ke kategori customer
            $table->foreignId('customer_category_id')->nullable()->constrained()->nullOnDelete();


            // Relasi ke kecamatan (district)
            $table->foreignId('district_id')->nullable()->constrained('indonesia_districts')->nullOnDelete();


            // Data customer
            $table->string('name');
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('other_contact')->nullable();
            $table->string('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
