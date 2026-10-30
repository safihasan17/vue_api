{{--
    Admission status of a prescription.
    Expects: $prescription  (with 'admission' loaded)
    Optional: $detailed = true  -> also prints ward / bed (used on the show page)
--}}
@php
    $statusColors = ['Admitted' => 'danger', 'Discharged' => 'success', 'Transferred' => 'info'];
@endphp

@if ($prescription->admission)
    @if (in_array(auth()->user()->role_id, [1, 3]))
        <a href="{{ route('admissions.show', $prescription->admission_id) }}"
            class="badge bg-{{ $statusColors[$prescription->admission->status] ?? 'secondary' }}">{{ $prescription->admission->status }}</a>
    @else
        <span class="badge bg-{{ $statusColors[$prescription->admission->status] ?? 'secondary' }}">{{ $prescription->admission->status }}</span>
    @endif
    @if (!empty($detailed))
        <span class="fw-medium">
            &mdash; {{ $prescription->admission->ward->name ?? '-' }}
            / {{ $prescription->admission->bed->bed_number ?? '-' }}
        </span>
    @endif
@elseif ($prescription->admission_advised)
    <span class="badge bg-warning">Admission advised</span>
    {{-- Only the receptionist / admin (Super Admin / Receptionist) can actually admit --}}
    @if (in_array(auth()->user()->role_id, [1, 3]))
        <a href="{{ route('admissions.create', ['prescription_id' => $prescription->id]) }}"
            class="btn btn-sm btn-alt-primary ms-1 d-print-none">Admit</a>
    @endif
@else
    <span class="text-muted">&mdash;</span>
@endif
