<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EoEventWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_event_creation(): void
    {
        $this->get(route('eo.events.create'))->assertRedirect(route('login'));
    }

    public function test_buyer_cannot_open_event_creation(): void
    {
        $this->actingAs($this->user('user'))
            ->get(route('eo.events.create'))
            ->assertForbidden();
    }

    public function test_eo_can_open_event_creation(): void
    {
        $this->actingAs($this->user('eo'))
            ->get(route('eo.events.create'))
            ->assertOk()
            ->assertSee('Create Event')
            ->assertSee('Simpan Draft');
    }

    public function test_eo_creates_owned_draft_and_cannot_spoof_owner_or_workflow_fields(): void
    {
        $owner = $this->user('eo');
        $other = $this->user('eo');

        $response = $this->actingAs($owner)->post(route('eo.events.store'), [
            ...$this->eventData(),
            'user_id' => $other->id,
            'workflow_status' => 'submitted',
            'approval_status' => 'approved',
            'rejection_reason' => 'spoofed',
            'status' => 'past_event',
        ]);

        $event = Event::query()->where('name', 'EO Workflow Event')->firstOrFail();
        $response->assertRedirect(route('eo.events.index'));
        $this->assertSame($owner->id, $event->user_id);
        $this->assertSame('draft', $event->workflow_status);
        $this->assertSame('pending', $event->approval_status);
        $this->assertSame('coming_soon', $event->status);
        $this->assertNull($event->rejection_reason);
    }

    public function test_eo_cannot_edit_another_owners_event(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($this->user('eo'));

        $this->actingAs($owner)
            ->get(route('eo.events.edit', $event))
            ->assertNotFound();
    }

    public function test_draft_is_not_public(): void
    {
        $event = $this->event($this->user('eo'));

        $this->get(route('events.show', $event))->assertNotFound();
        $this->get(route('home'))->assertDontSee('EO Workflow Event');
    }

    public function test_preview_of_saved_draft_does_not_change_database(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);

        $this->actingAs($owner)
            ->get(route('eo.events.preview', $event))
            ->assertOk()
            ->assertSee('Preview ini hanya dapat dilihat oleh pemilik event');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'workflow_status' => 'draft',
            'approval_status' => 'pending',
        ]);
    }

    public function test_submit_changes_draft_to_submitted_pending(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner, ['rejection_reason' => 'should clear']);
        $this->ticket($event);

        $this->actingAs($owner)
            ->post(route('eo.events.submit', $event))
            ->assertRedirect(route('eo.events.index'))
            ->assertSessionHas('success', 'Event berhasil dikirim untuk ditinjau Admin.');

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'workflow_status' => 'submitted',
            'approval_status' => 'pending',
            'rejection_reason' => null,
        ]);
    }

    public function test_rejected_event_can_be_resubmitted_as_pending(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner, [
            'workflow_status' => 'submitted',
            'approval_status' => 'rejected',
            'rejection_reason' => 'Perlu perbaikan.',
        ]);
        $this->ticket($event);

        $this->actingAs($owner)
            ->post(route('eo.events.submit', $event))
            ->assertRedirect(route('eo.events.index'));

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'workflow_status' => 'submitted',
            'approval_status' => 'pending',
            'rejection_reason' => null,
        ]);
    }

    public function test_approval_fields_cannot_be_spoofed_during_update(): void
    {
        $owner = $this->user('eo');
        $event = $this->event($owner);

        $this->actingAs($owner)
            ->put(route('eo.events.update', $event), [
                ...$this->eventData(['name' => 'Updated Event']),
                'workflow_status' => 'submitted',
                'approval_status' => 'approved',
                'rejection_reason' => 'spoofed',
                'status' => 'past_event',
                'user_id' => $this->user('eo')->id,
            ])
            ->assertRedirect(route('eo.events.index'));

        $event->refresh();
        $this->assertSame($owner->id, $event->user_id);
        $this->assertSame('draft', $event->workflow_status);
        $this->assertSame('pending', $event->approval_status);
        $this->assertSame('coming_soon', $event->status);
        $this->assertSame('Updated Event', $event->name);
    }

    public function test_existing_submitted_approved_event_remains_public(): void
    {
        $event = Event::create(array_merge($this->eventData(), [
            'user_id' => $this->user('eo')->id,
            'status' => 'coming_soon',
            'approval_status' => 'approved',
        ]));

        $this->assertSame('submitted', $event->workflow_status);
        $this->get(route('events.show', $event))
            ->assertOk()
            ->assertSee('EO Workflow Event');
        $this->get(route('home'))->assertSee('EO Workflow Event');
    }

    private function user(string $role): User
    {
        return User::create([
            'name' => 'Workflow ' . ucfirst($role),
            'email' => $role . '-' . uniqid() . '@example.test',
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function eventData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'EO Workflow Event',
            'category' => 'Music',
            'description' => 'Workflow test event.',
            'location' => 'Jakarta',
            'venue' => 'Test Venue',
            'event_date' => today()->addDays(10)->toDateString(),
            'seating_type' => 'general_admission',
        ], $overrides);
    }

    private function event(User $owner, array $overrides = []): Event
    {
        return Event::create(array_merge($this->eventData(), [
            'user_id' => $owner->id,
            'status' => 'coming_soon',
            'workflow_status' => 'draft',
            'approval_status' => 'pending',
        ], $overrides));
    }

    private function ticket(Event $event): Ticket
    {
        return $event->tickets()->create([
            'name' => 'Regular',
            'price' => 50000,
            'quota' => 100,
            'description' => null,
        ]);
    }
}
