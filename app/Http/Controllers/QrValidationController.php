<?php

namespace App\Http\Controllers;

use App\Models\TicketInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QrValidationController extends Controller
{
    public function index()
    {
        return view('admin.qr-validation.index');
    }

    public function validateTicket(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        $ticket = $this->findTicket($validated['code']);

        if (! $ticket) {
            return back()
                ->withInput()
                ->with('validation_error', 'Ticket code atau QR token tidak ditemukan.');
        }

        $ticket->load([
            'order.user',
            'order.payments',
            'ticket.event',
            'seat',
            'checkedInBy',
        ]);

        if ($ticket->order?->payments?->where('status', 'paid')->isEmpty()) {
            return view('admin.qr-validation.index', [
                'ticket' => $ticket,
                'validationError' => 'Payment order belum berstatus paid.',
                'inputCode' => $validated['code'],
            ]);
        }

        return view('admin.qr-validation.index', [
            'ticket' => $ticket,
            'inputCode' => $validated['code'],
        ]);
    }

    public function checkIn(Request $request, TicketInstance $ticketInstance)
    {
        $ticket = DB::transaction(function () use ($ticketInstance) {
            $lockedTicket = TicketInstance::query()
                ->with(['order.payments', 'ticket.event', 'user', 'seat'])
                ->whereKey($ticketInstance->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedTicket->status !== 'issued') {
                return $lockedTicket;
            }

            if (! $lockedTicket->order
                || $lockedTicket->order->status !== 'paid'
                || $lockedTicket->order->payments->where('status', 'paid')->isEmpty()) {
                return $lockedTicket;
            }

            $lockedTicket->update([
                'status' => 'used',
                'checked_in_at' => now(),
                'checked_in_by' => auth()->id(),
            ]);

            return $lockedTicket->fresh(['order.payments', 'ticket.event', 'user', 'seat', 'checkedInBy']);
        });

        if ($ticket->status !== 'used' || $ticket->checked_in_by !== auth()->id()) {
            return back()->with('validation_error', 'Ticket tidak dapat di-check-in: status, payment, atau ticket tidak valid.');
        }

        return back()->with('validation_success', 'Ticket berhasil check-in.');
    }

    private function findTicket(string $code): ?TicketInstance
    {
        return TicketInstance::query()
            ->where('ticket_code', $code)
            ->orWhere('qr_token', $code)
            ->first();
    }
}
