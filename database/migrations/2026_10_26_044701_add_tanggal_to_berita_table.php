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
        if (! Schema::hasColumn('berita', 'tanggal')) {
            Schema::table('berita', function (Blueprint $table) {
                $table->dateTime('tanggal')->useCurrent()->after('penulis_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('berita', 'tanggal')) {
            Schema::table('berita', function (Blueprint $table) {
                $table->dropColumn('tanggal');
            });
        }
    }
};