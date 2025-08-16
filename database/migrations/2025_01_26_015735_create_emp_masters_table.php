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
    Schema::create('emp_masters', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->string('empno', 10);
        $table->string('fullname', 50);
        
        // Tambahkan ini untuk menghubungkan ke tabel 'users'
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
        $table->foreignId('nationality_id')->constrained('nationalities')->cascadeOnDelete();
        $table->foreignId('gender_id')->constrained('genders')->cascadeOnDelete();
        $table->foreignId('maritalstatus_id')->constrained('maritalstatuses')->cascadeOnDelete();
        $table->foreignId('religion_id')->constrained('religions')->cascadeOnDelete();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emp_masters');
    }
};
