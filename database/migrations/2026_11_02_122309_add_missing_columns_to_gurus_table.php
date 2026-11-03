<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            if (!Schema::hasColumn('gurus', 'urutan'))        $table->unsignedInteger('urutan')->default(0);
            if (!Schema::hasColumn('gurus', 'tampil_di_web')) $table->boolean('tampil_di_web')->default(true);
            if (!Schema::hasColumn('gurus', 'jenis_kelamin')) $table->string('jenis_kelamin', 1)->nullable();
            if (!Schema::hasColumn('gurus', 'pendidikan'))    $table->string('pendidikan')->nullable();
            if (!Schema::hasColumn('gurus', 'bio'))           $table->text('bio')->nullable();
        });
    }

    public function down(): void {}
};