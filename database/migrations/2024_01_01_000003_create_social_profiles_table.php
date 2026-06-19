<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('social_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_account_id')->constrained()->onDelete('cascade');
            $table->string('platform_user_id')->nullable();
            $table->string('username')->nullable();
            $table->string('display_name')->nullable();
            $table->string('account_type')->nullable();
            $table->string('profile_picture_url', 1000)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index('social_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_profiles');
    }
};
