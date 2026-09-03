<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function create(Registration $registration): View
    {
        return view('payments.create', compact('registration'));
    }


    public function store(Request $request, Registration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required','numeric'],
            'transaction_id' => ['nullable','string'],
        ]);

        Payment::create([
            'registration_id' => $registration->id,
            'amount' => $validated['amount'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'status' => Payment::STATUS_PENDING,
        ]);

        return redirect()
            ->route('tournaments.show', $registration->tournament_id)
            ->with('success', 'Payment submitted successfully.');
    }


    public function show(Payment $payment): View
    {
        return view('payments.show', compact('payment'));
    }


    public function verify(Payment $payment): RedirectResponse
    {
        $payment->update([
            'status' => Payment::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return back()
            ->with('success', 'Payment verified.');
    }


    public function reject(Payment $payment): RedirectResponse
    {
        $payment->update([
            'status' => Payment::STATUS_REJECTED,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        return back()
            ->with('success', 'Payment rejected.');
    }
}