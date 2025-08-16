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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('empno', 10);
            $table->string('period', 7);
            $table->decimal('basicsalary', 10);
            $table->decimal('transport', 10);
            $table->decimal('meal', 10);
            $table->decimal('overtime', 10);
            $table->decimal('medical', 10);
            $table->decimal('hospital', 10);
            $table->decimal('kacamata', 10);
            $table->decimal('tooth', 10);
            $table->decimal('premi', 10);
            $table->decimal('komisi', 10);
            $table->decimal('masabakti', 10);
            $table->decimal('thr', 10);
            $table->decimal('otherincome', 10);
            $table->string('othremark', 500);
            $table->decimal('rumah', 10);
            $table->decimal('jabatan', 10);
            $table->decimal('listrik', 10);
            $table->decimal('leave', 10);
            $table->decimal('sanksi', 10);
            $table->decimal('fixedtax', 10);
            $table->decimal('jkm', 10);
            $table->decimal('jkk', 10);
            $table->decimal('jht', 10);
            $table->decimal('bpjskaryawan', 10);
            $table->decimal('bpjsperusahaan', 10);
            $table->decimal('refund', 10);
            $table->decimal('yayasan', 10);
            $table->decimal('personaladvance', 10);
            $table->decimal('koperasi', 10);
            $table->decimal('businessadvance', 10);
            $table->decimal('loancar', 10);
            $table->decimal('loanother', 10);
            $table->decimal('credit', 10);
            $table->decimal('milenium', 10);
            $table->decimal('grossincome', 10);
            $table->decimal('netincomeexpph22', 10);
            $table->decimal('taxamonth', 10);
            $table->decimal('total', 10);
            $table->decimal('thp', 10);
            $table->unsignedBigInteger('users_id')->default(1);
            $table->foreign('users_id')->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
