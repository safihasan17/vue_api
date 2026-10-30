<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Ward;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Top stat cards
        $totalPatients = Patient::count();
        $totalDoctors = Doctor::count();
        $todayAppointmentsCount = Appointment::whereDate('appointment_date', $today)->count();
        $totalBeds = Bed::count();
        $availableBeds = Bed::where('status', 'Available')->count();

        // Ward occupancy
        $wards = Ward::withCount([
            'beds as total_beds',
            'beds as occupied_beds' => fn ($q) => $q->where('status', 'Occupied'),
            'beds as available_beds' => fn ($q) => $q->where('status', 'Available'),
        ])->orderBy('name')->paginate(5, ['*'], 'ward_page');

        // Revenue snapshot (current month, cancelled invoices excluded)
        $monthInvoices = Invoice::where('status', '!=', 'Cancelled')
            ->whereYear('invoice_date', now()->year)
            ->whereMonth('invoice_date', now()->month);

        $monthBilled = (float) (clone $monthInvoices)->sum('total_amount');
        // Money actually received this month (from the payments table)
        $monthCollected = (float) Payment::where('status', 'Success')
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');
        $collectedPercent = $monthBilled > 0 ? min(100, round($monthCollected / $monthBilled * 100)) : 0;

        $totalDue = (float) Invoice::whereIn('status', ['Unpaid', 'Partially Paid'])
            ->selectRaw('COALESCE(SUM(total_amount - paid_amount), 0) as due')
            ->value('due');

        // Today's appointments
        $todayAppointments = Appointment::with(['patient', 'doctor.user', 'doctor.department'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->get();

        return view('admin.pages.dashboard', [
            'totalPatients' => $totalPatients,
            'totalDoctors' => $totalDoctors,
            'todayAppointmentsCount' => $todayAppointmentsCount,
            'totalBeds' => $totalBeds,
            'availableBeds' => $availableBeds,
            'wards' => $wards,
            'monthCollected' => $this->taka($monthCollected),
            'totalDue' => $this->taka($totalDue),
            'collectedPercent' => $collectedPercent,
            'todayAppointments' => $todayAppointments,
        ]);
    }

    /** Format as ৳ with Indian (lakh/crore) digit grouping, e.g. ৳5,42,300 */
    private function taka(float $amount): string
    {
        $n = (string) (int) round($amount);
        if (strlen($n) > 3) {
            $last3 = substr($n, -3);
            $rest = substr($n, 0, -3);
            $n = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest) . ',' . $last3;
        }

        return '&#2547;' . $n;
    }
}
