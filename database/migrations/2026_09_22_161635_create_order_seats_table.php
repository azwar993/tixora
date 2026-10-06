<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_seats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->restrictOnDelete();

            $table->foreignId('seat_id')
                ->constrained('seats')
                ->restrictOnDelete();

            $table->string('status')->default('reserved');

            $table->unsignedBigInteger('active_seat_id')
                ->nullable()
                ->storedAs("CASE WHEN status <> 'cancelled' THEN seat_id ELSE NULL END");

            $table->timestamps();

            /*
            |--------------------------------------------------------------
            | Constraints
            |--------------------------------------------------------------
            |
            | Satu seat hanya boleh aktif pada satu order pada satu waktu.
            | Ketika order cancelled, active_seat_id menjadi NULL sehingga
            | seat dapat digunakan lagi.
            |
            */

            $table->unique('active_seat_id');

            $table->unique([
                'order_id',
                'seat_id',
            ]);

            $table->index([
                'order_id',
                'status',
            ]);

            $table->index([
                'seat_id',
                'status',
            ]);

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_seats');
    }
};