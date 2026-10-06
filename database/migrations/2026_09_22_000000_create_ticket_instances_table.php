<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete();
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('seat_id')
                ->nullable()
                ->constrained('seats')
                ->restrictOnDelete();
            $table->string('status');
            $table->unsignedBigInteger('active_seat_id')
                ->nullable()
                ->storedAs("CASE WHEN status <> 'cancelled' THEN seat_id ELSE NULL END");
            $table->string('ticket_code')->unique();
            $table->string('qr_token')->unique();
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique('active_seat_id');
            $table->index(['order_id', 'status']);
            $table->index(['ticket_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_instances');
    }
};
