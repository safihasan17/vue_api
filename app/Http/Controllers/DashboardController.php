<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Ward;

/**
 * JSON version of App\Http\Controllers\DashboardController (the Blade one stays for web.php).
 * GET /api/dashboard
 */
class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Revenue snapshot (current month, cancelled invoices excluded)
        $monthBilled = (float) Invoice::where('status', '!=', 'Cancelled')
            ->whereYear('invoice_date', now()->year)
            ->whereMonth('invoice_date', now()->month)
            ->sum('total_amount');

        // Money actually received this month (from the payments table)
        $monthCollected = (float) Payment::where('status', 'Success')
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $totalDue = (float) Invoice::whereIn('status', ['Unpaid', 'Partially Paid'])
            ->selectRaw('COALESCE(SUM(total_amount - paid_amount), 0) as due')
            ->value('due');

        $wards = Ward::withCount([
            'beds as total_beds',
            'beds as occupied_beds'  => fn ($q) => $q->where('status', 'Occupied'),
            'beds as available_beds' => fn ($q) => $q->where('status', 'Available'),
        ])->orderBy('name')->limit(6)->get(['id', 'name']);

        $todayAppointments = Appointment::with(['patient:id,name', 'doctor.user:id,name', 'doctor.department:id,name'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get();

        return response()->json([
            'success' => true,
            'stats'   => [
                'totalPatients'          => Patient::count(),
                'totalDoctors'           => Doctor::count(),
                'todayAppointmentsCount' => $todayAppointments->count(),
                'totalBeds'              => Bed::count(),
                'availableBeds'          => Bed::where('status', 'Available')->count(),
            ],
            'money' => [
                'monthBilled'      => $monthBilled,
                'monthCollected'   => $monthCollected,
                'collectedPercent' => $monthBilled > 0 ? min(100, (int) round($monthCollected / $monthBilled * 100)) : 0,
                'totalDue'         => $totalDue,
            ],
            'wards'             => $wards,
            'todayAppointments' => $todayAppointments,
        ]);
    }
}