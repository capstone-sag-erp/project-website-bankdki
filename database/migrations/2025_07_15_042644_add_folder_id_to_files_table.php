<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::table('files', function (Blueprint $table) {
        if (!Schema::hasColumn('files', 'folder_id')) {
            $table->foreignId('folder_id')->nullable()->constrained()->onDelete('cascade');
        }
    });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            //
        });
    }
};
