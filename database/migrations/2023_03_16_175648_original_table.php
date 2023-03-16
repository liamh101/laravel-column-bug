<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('text_table', static function (Blueprint $table) {
            $table->id();
            $table->text('col1');
            $table->text('col2');
            $table->text('col3');
        });
    }
};
