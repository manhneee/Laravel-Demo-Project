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
        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('created_by_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->text('description');
                $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('priority_id')->nullable()->constrained('priorities')->nullOnDelete();
                $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed'])->default('open');
                $table->timestamps();
            });
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
