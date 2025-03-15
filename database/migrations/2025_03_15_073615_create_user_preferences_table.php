<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->morphs('preferable');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'preferable_id', 'preferable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
