<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained('ads')->cascadeOnDelete();
            $table->string('attribute_key');
            $table->string('attribute_value')->nullable();
            $table->timestamps();

            $table->index('ad_id');
            $table->unique(['ad_id', 'attribute_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_attributes');
    }
};
