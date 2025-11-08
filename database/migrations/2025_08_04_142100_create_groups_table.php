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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('slug',255);
            $table->string('cover_path',1024)->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->text('about')->nullable();
            $table->boolean('auto_approval')->default(true);
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->string('role');
            $table->string('status');// approved,pending

            $table->softDeletes();            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
