<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Required as the referenced key for the composite Event/Ticket FK below.
            $table->unique(['event_id', 'id'], 'tickets_event_id_id_unique');
        });

        Schema::create('event_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->unsignedBigInteger('ticket_id');
            $table->string('code', 50);
            $table->string('name', 100);
            $table->timestamps();

            $table->foreign(['event_id', 'ticket_id'], 'event_sections_event_ticket_fk')
                ->references(['event_id', 'id'])
                ->on('tickets')
                ->cascadeOnDelete();
            $table->unique(['event_id', 'code']);
            $table->index('ticket_id');
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->foreignId('section_id')
                ->nullable()
                ->after('event_id')
                ->constrained('event_sections')
                ->cascadeOnDelete();

            $table->index(['event_id', 'section_id']);
            $table->index(['section_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropIndex(['event_id', 'section_id']);
            $table->dropIndex(['section_id', 'status']);
            $table->dropForeign(['section_id']);
            $table->dropColumn('section_id');
        });

        Schema::dropIfExists('event_sections');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique('tickets_event_id_id_unique');
        });
    }
};
