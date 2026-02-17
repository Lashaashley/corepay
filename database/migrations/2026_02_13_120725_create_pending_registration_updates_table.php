<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pending_registration_updates', function (Blueprint $table) {
            $table->id();
            $table->string('empid')->index(); // The employee being updated
            $table->unsignedBigInteger('submitted_by'); // Who submitted the changes
            $table->unsignedBigInteger('approved_by')->nullable(); // Who approved/rejected
            
            // Original values (for comparison/audit)
            $table->json('original_data')->nullable();
            
            // Pending changes
            $table->json('pending_data'); // The new values waiting approval
            
            // Status and workflow
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->text('rejection_reason')->nullable();
            $table->text('submission_notes')->nullable();
            
            // Timestamps
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['empid', 'status']);
            $table->index('status');
            $table->index('submitted_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pending_registration_updates');
    }
};