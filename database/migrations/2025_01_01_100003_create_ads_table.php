<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');

            $table->enum('condition', ['new', 'used'])->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->boolean('is_negotiable')->default(false);
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();

            $table->enum('status', ['pending', 'active', 'sold', 'expired', 'rejected'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['category_id', 'status']);
            $table->index(['district_id', 'city_id']);
            $table->index(['is_featured', 'status']);
            $table->index('price');
        });

        // Full-text index for search across title + description (MySQL/InnoDB).
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE ads ADD FULLTEXT ads_fulltext_index (title, description)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
