<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aicms_changes', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->longText('content_before');
            $table->longText('content_after');
            $table->string('summary')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('file_path');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aicms_changes');
    }
};
