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
        if (!Schema::hasColumn('notes', 'topic_id')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->string('topic_id')->nullable()->change();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
