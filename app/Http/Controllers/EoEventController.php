<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EoEventController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->getKey();
        $status = $request->query('status');
        $allowedStatuses = ['pending', 'approved', 'rejected'];

        if (! in_array($status, $allowedStatuses, true)) {
            $status = null;
        }

        $ownedEvents = Event::query()->where('user_id', $userId);
        $statusCounts = (clone $ownedEvents)
            ->selectRaw('approval_status, COUNT(*) as aggregate')
            ->groupBy('approval_status')
            ->pluck('aggregate', 'approval_status');

        $events = (clone $ownedEvents)
            ->with(['tickets:id,event_id,name,quota,sold'])
            ->when($status, fn ($query) => $query->where('approval_status', $status))
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return view('eo.events.index', [
            'events' => $events,
            'activeStatus' => $status,
            'statusCounts' => [
                'all' => (clone $ownedEvents)->count(),
                'pending' => (int) ($statusCounts['pending'] ?? 0),
                'approved' => (int) ($statusCounts['approved'] ?? 0),
                'rejected' => (int) ($statusCounts['rejected'] ?? 0),
            ],
        ]);
    }

    public function create()
    {
        return view('eo.events.create', [
            'event' => new Event([
                'seating_type' => 'general_admission',
                'event_date' => today()->toDateString(),
            ]),
            'isEditing' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->eventRules());
        $imagePath = $this->storeImage($request);

        $event = Event::create([
            ...$validated,
            'image' => $imagePath,
            'user_id' => $request->user()->getKey(),
            'status' => 'coming_soon',
            'workflow_status' => 'draft',
            'approval_status' => 'pending',
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('eo.events.index')
            ->with('success', 'Draft event berhasil disimpan.');
    }

    public function edit(Request $request, int $event)
    {
        $ownedEvent = $this->ownedEvent($request, $event);

        abort_unless(
            $ownedEvent->workflow_status === 'draft' || $ownedEvent->approval_status === 'rejected',
            404
        );

        return view('eo.events.create', [
            'event' => $ownedEvent,
            'isEditing' => true,
        ]);
    }

    public function update(Request $request, int $event)
    {
        $ownedEvent = $this->ownedEvent($request, $event);
        abort_unless(
            $ownedEvent->workflow_status === 'draft' || $ownedEvent->approval_status === 'rejected',
            404
        );

        $validated = $request->validate($this->eventRules());
        $oldImagePath = $ownedEvent->image;
        $newImagePath = $this->storeImage($request);
        unset($validated['image']);

        $ownedEvent->fill($validated);
        if ($newImagePath !== null) {
            $ownedEvent->image = $newImagePath;
        }
        $ownedEvent->save();

        if ($newImagePath !== null && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()
            ->route('eo.events.index')
            ->with('success', $ownedEvent->approval_status === 'rejected'
                ? 'Perubahan event berhasil disimpan. Ajukan ulang setelah siap ditinjau.'
                : 'Draft event berhasil diperbarui.');
    }

    public function preview(Request $request, int $event)
    {
        $ownedEvent = $this->ownedEvent($request, $event);
        abort_unless(
            $ownedEvent->workflow_status === 'draft' || $ownedEvent->approval_status === 'rejected',
            404
        );

        return view('eo.events.preview', ['event' => $ownedEvent]);
    }

    public function submit(Request $request, int $event)
    {
        return DB::transaction(function () use ($request, $event) {
            $ownedEvent = Event::query()
                ->where('user_id', $request->user()->getKey())
                ->lockForUpdate()
                ->findOrFail($event);

            abort_unless(
                $ownedEvent->workflow_status === 'draft' || $ownedEvent->approval_status === 'rejected',
                404
            );

            validator($ownedEvent->only([
                'name',
                'category',
                'location',
                'venue',
                'event_date',
                'description',
                'seating_type',
            ]), $this->eventRules(false))->validate();

            if ($ownedEvent->tickets()->count() < 1) {
                return redirect()
                    ->route('eo.events.tickets.index', $ownedEvent)
                    ->with('error', 'Tambahkan setidaknya satu tipe tiket sebelum mengajukan event.');
            }

            $ownedEvent->workflow_status = 'submitted';
            $ownedEvent->approval_status = 'pending';
            $ownedEvent->rejection_reason = null;
            $ownedEvent->save();

            return redirect()
                ->route('eo.events.index')
                ->with('success', 'Event berhasil dikirim untuk ditinjau Admin.');
        });
    }

    private function ownedEvent(Request $request, int $event): Event
    {
        return Event::query()
            ->where('user_id', $request->user()->getKey())
            ->findOrFail($event);
    }

    private function eventRules(bool $includeImage = true): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:255'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'description' => ['nullable', 'string'],
            'seating_type' => ['required', Rule::in(['general_admission', 'numbered_seat'])],
        ];

        if ($includeImage) {
            $rules['image'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
        }

        return $rules;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $path = $request->file('image')->store('events', 'public');

        if (! is_string($path) || $path === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'image' => 'Gambar event gagal disimpan. Silakan coba kembali.',
            ]);
        }

        return $path;
    }
}
