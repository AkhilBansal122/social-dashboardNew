<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('instagram_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_account_id')->constrained()->onDelete('cascade');
            $table->string('instagram_media_id')->unique();
            $table->string('media_type')->nullable();
            $table->text('media_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->text('permalink')->nullable();
            $table->text('caption')->nullable();
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('reach')->default(0);
            $table->unsignedInteger('impressions')->default(0);
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->index(['social_account_id', 'posted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_media');
    }
};
