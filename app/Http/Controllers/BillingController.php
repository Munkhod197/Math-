<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class BillingController extends PageController
{
    public function index()
    {
        $user = Auth::user();
        $invoices = Invoice::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        return view('billing.index', compact('user', 'invoices'));
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'plan' => 'required|in:monthly,quarterly,yearly',
        ]);

        $plans = [
            'monthly' => ['amount' => 24900, 'label' => 'Сарын эрх', 'months' => 1],
            'quarterly' => ['amount' => 69900, 'label' => 'Улирлын эрх', 'months' => 3],
            'yearly' => ['amount' => 239900, 'label' => 'Жилийн эрх', 'months' => 12],
        ];

        $plan = $plans[$validated['plan']];
        $endsAt = Carbon::now()->addMonths($plan['months']);

        Invoice::create([
            'user_id' => $user->id,
            'reference' => strtoupper(Str::random(10)),
            'plan' => $validated['plan'],
            'amount' => $plan['amount'],
            'currency' => 'MNT',
            'status' => 'paid',
            'description' => "MathMon төлбөр: {$plan['label']} ({$plan['amount']} MNT)",
            'paid_at' => Carbon::now(),
        ]);

        $user->update([
            'billing_plan' => $validated['plan'],
            'billing_status' => 'active',
            'billing_ends_at' => $endsAt,
        ]);

        return Redirect::route('billing.index')
            ->with('success', 'Төлбөр амжилттай бүртгэгдлээ. Таны эрх идэвхэжсэн.');
    }

    public function cancel(Request $request)
    {
        $user = Auth::user();
        $oldPlan = $user->billing_plan;

        $user->update([
            'billing_status' => 'cancelled',
            'billing_plan' => null,
            'billing_ends_at' => null,
        ]);

        Invoice::create([
            'user_id' => $user->id,
            'reference' => strtoupper(Str::random(10)),
            'plan' => $oldPlan ?? 'cancel',
            'amount' => 0,
            'currency' => 'MNT',
            'status' => 'cancelled',
            'description' => 'Төлбөрийн эрх цуцлагдлаа',
            'paid_at' => Carbon::now(),
        ]);

        return Redirect::route('billing.index')
            ->with('success', 'Таны эрх цуцлагдлаа. Та хүсвэл дахин эрх сонгож болно.');
    }
}
