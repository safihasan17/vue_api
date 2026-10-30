@extends('admin.layouts.master')

@section('title', 'Prescription Details')

@section('content')
    <main id="main-container">
        <div class="content">

            {{-- Action bar (screen only) --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
                <h1 class="h4 mb-0">Prescription
                    <span class="text-muted fs-6">RX-{{ str_pad($prescription->id, 4, '0', STR_PAD_LEFT) }}</span>
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('prescriptions.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-arrow-left opacity-50 me-1"></i> Back
                    </a>
                    <a href="{{ route('prescriptions.edit', $prescription->id) }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-pencil-alt opacity-50 me-1"></i> Edit
                    </a>
                    @if (auth()->user()->role_id != 2)
                        <form action="{{ route('prescriptions.generate-invoice', $prescription->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fa fa-file-invoice-dollar opacity-50 me-1"></i> Generate Invoice
                            </button>
                        </form>
                    @endif
                    <button type="button" class="btn btn-sm btn-alt-secondary" onclick="window.print()">
                        <i class="fa fa-print opacity-50 me-1"></i> Print
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif

            {{-- ===================== PRINT SHEET ===================== --}}
            <div id="rx-print-area" class="bg-white text-dark mx-auto p-4 p-print-0 border" style="max-width: 210mm;">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="display-3 fw-bold lh-1 font-serif">R<sub>x</sub></div>

                    <div class="text-end">
                        <div class="fs-5 fw-semibold">{{ $prescription->doctor->user->name ?? 'N/A' }}</div>
                        <div class="fs-6">{{ $prescription->doctor->qualification ?? '' }}</div>
                        <div class="fs-6">{{ $prescription->doctor->specialization ?? '' }}</div>

                        @if ($prescription->doctor->registration_no ?? false)
                            <div class="small text-muted">Registration No: {{ $prescription->doctor->registration_no }}
                            </div>
                        @endif
                        @if ($prescription->doctor->user->email ?? false)
                            <div class="small text-muted">Email: {{ $prescription->doctor->user->email }}</div>
                        @endif
                        <div class="small text-muted">
                            {{ config('app.name', 'Hospital Management') }}
                            @if ($prescription->doctor->department->name ?? false)
                                ({{ $prescription->doctor->department->name }})
                            @endif
                        </div>
                        @if ($prescription->doctor->address ?? false)
                            <div class="small text-muted">{{ $prescription->doctor->address }}</div>
                        @endif
                    </div>
                </div>

                <hr class="border-dark border-2 opacity-100 my-2">

                {{-- Patient + reference --}}
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-muted">Patient Details</div>
                        <div class="fw-medium">{{ $prescription->patient->name ?? 'N/A' }}</div>
                        @php
                            $age = $prescription->patient->age ?? null;
                            $dob = $prescription->patient->date_of_birth ?? null;
                            if (!$age && $dob) {
                                $age = \Carbon\Carbon::parse($dob)->age;
                            }
                            $gender = $prescription->patient->gender ?? null;
                        @endphp
                        <div class="fw-medium">
                            {{ $age ? $age . ' yrs' : '' }}{{ $age && $gender ? ', ' : '' }}{{ ucfirst($gender ?? '') }}
                        </div>
                        <div class="text-muted">Address:
                            <span class="text-dark">{{ $prescription->patient->address ?? '' }}</span>
                        </div>
                        <div class="text-muted">Ph No.:
                            <span class="text-dark">{{ $prescription->patient->phone ?? '' }}</span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="d-flex gap-2">
                            <span class="text-muted">Ref no:</span>
                            <span class="fw-medium">RX-{{ str_pad($prescription->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="text-muted">Date &amp; Time:</span>
                            <span class="fw-medium">{{ $prescription->prescription_date?->format('d M Y') ?? '-' }}
                                {{ $prescription->created_at?->format('H:i:s') }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="text-muted">Admission:</span>
                            @include('admin.pages.prescription._admission-status', [
                                'prescription' => $prescription,
                                'detailed' => true,
                            ])
                        </div>
                        @if ($prescription->appointment)
                            <div class="d-flex gap-2">
                                <span class="text-muted">Appointment:</span>
                                <span class="fw-medium">#{{ $prescription->appointment->id }} &mdash;
                                    {{ $prescription->appointment->appointment_date?->format('d M Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Clinical --}}
                <div class="mt-2">
                    <div class="text-muted">Symptoms(HOPI):</div>
                    <div class="fw-medium">{{ $prescription->symptoms ?? '—' }}</div>
                    <div class="text-muted">Provisional Diagnosis:</div>
                    <div class="fw-medium">{{ $prescription->diagnosis ?? '—' }}</div>
                </div>

                {{-- Lab tests | Medicines --}}
                <table class="table table-borderless mt-3 mb-0" style="table-layout: fixed;">
                    <thead>
                        <tr class="border-top border-bottom border-dark">
                            <th class="text-center fw-normal py-2" style="width: 30%;">Lab Tests</th>
                            <th class="text-center fw-normal py-2">Medicines</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border-end border-dark align-top pt-3" style="height: 340px;">
                                {{-- Lab tests come from lab_test_orders linked to this prescription --}}
                                @forelse ($prescription->labTestOrders as $order)
                                    <div>{{ $loop->iteration }}. {{ $order->test->test_name ?? '-' }}</div>
                                @empty
                                    <div>No tests prescribed.</div>
                                @endforelse
                            </td>

                            <td class="align-top pt-3">
                                @forelse ($prescription->prescriptionMedicines as $row)
                                    <div class="d-flex gap-2 mb-2">
                                        <div style="width: 18px;">{{ $loop->iteration }}.</div>
                                        <div class="flex-shrink-0" style="width: 42%;">
                                            <div class="fw-medium">{{ $row->medicine->name ?? 'N/A' }}</div>
                                            @if ($row->medicine->generic_name ?? false)
                                                <div class="text-muted">{{ $row->medicine->generic_name }}</div>
                                            @endif
                                            @if ($row->medicine->type ?? false)
                                                <div class="text-muted fst-italic">{{ $row->medicine->type }}</div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 small">
                                            <div>{{ $row->dosage ?? '-' }}</div>
                                            <div>{{ $row->duration ?? '-' }}</div>
                                            <div>{{ $row->instructions ?? '-' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted">No medicines added.</div>
                                @endforelse

                                <div class="text-center small my-3">
                                    ***** <strong>Note:</strong> Substitution allowed wherever applicable. *****
                                </div>

                                <div class="text-muted">General Instructions:</div>
                                @if ($prescription->notes)
                                    <div class="small" style="white-space: pre-line;">{{ $prescription->notes }}</div>
                                @endif

                                <div class="text-muted mt-1">Next Appointment:
                                    <span class="text-dark">
                                        {{ $prescription->appointment?->appointment_date?->format('d M Y') ?? '' }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Signature --}}
                <div class="text-end mt-4">
                    <div class="border-top border-dark ms-auto mb-1" style="width: 180px;"></div>
                    <div class="fw-medium">{{ $prescription->doctor->user->name ?? '' }}</div>
                    <div>{{ $prescription->doctor->qualification ?? '' }}</div>
                    <div>{{ $prescription->doctor->specialization ?? '' }}</div>
                </div>

                {{-- Disclaimer --}}
                <div class="border-top border-dark border-2 opacity-100 mt-3 pt-2">

                </div>

                <div class="small text-muted mt-3 no-print">
                    Issued {{ $prescription->created_at?->format('d M Y, h:i A') ?? '-' }}
                    &middot; Updated {{ $prescription->updated_at?->format('d M Y, h:i A') ?? '-' }}
                </div>

            </div>
        </div>
    </main>


    <style>
        .font-serif {
            font-family: Georgia, 'Times New Roman', serif;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            #page-header,
            #sidebar,
            #page-footer,
            #page-loader,
            .no-print {
                display: none !important;
            }

            #page-container,
            #main-container,
            .content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            #rx-print-area {
                max-width: none !important;
                width: 100% !important;
                border: 0 !important;
                padding: 0 !important;
                font-size: 10.5px;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            #rx-print-area * {
                break-inside: avoid;
            }

            #rx-print-area td[style*="height"] {
                height: auto !important;
                min-height: 110mm;
            }
        }
    </style>
@endsection
