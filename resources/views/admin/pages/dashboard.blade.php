  @extends('admin.layouts.master')
  @section('content')
  @php
      $roleId = auth()->user()->role_id;
      $canDoctors = $roleId == 1;
      $canPatients = in_array($roleId, [1, 3]);
      $canAppointments = in_array($roleId, [1, 2, 3]);
      $canAdmissions = in_array($roleId, [1, 3]);
      $canInvoices = in_array($roleId, [1, 3]);
      $statusColors = [
          'Scheduled' => 'info',
          'Checked-in' => 'success',
          'Waiting' => 'warning',
          'Completed' => 'secondary',
          'Cancelled' => 'danger',
      ];
  @endphp
  <main id="main-container">
      <!-- Page Content -->
      <div class="content">
          <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
              <div>
                  <h1 class="h3 fw-bold mb-0">Hospital Dashboard</h1>
                  <p class="text-muted mb-0">Overview of today's hospital activity — MediCare HMS</p>
              </div>
              <div class="d-flex gap-2">
              </div>
          </div>

          <div class="row items-push">
              <div class="col-6 col-lg-3">
                  <a class="block block-rounded block-link-shadow text-center" href="{{ $canPatients ? route('patients.index') : 'javascript:void(0)' }}">
                      <div class="block-content block-content-full">
                          <div class="fs-2 fw-semibold text-primary">{{ number_format($totalPatients) }}</div>
                      </div>
                      <div class="block-content py-2 bg-body-light">
                          <p class="fw-medium fs-sm text-muted mb-0">Total Patients</p>
                      </div>
                  </a>
              </div>
              <div class="col-6 col-lg-3">
                  <a class="block block-rounded block-link-shadow text-center" href="{{ $canDoctors ? route('doctors.index') : 'javascript:void(0)' }}">
                      <div class="block-content block-content-full">
                          <div class="fs-2 fw-semibold text-info">{{ number_format($totalDoctors) }}</div>
                      </div>
                      <div class="block-content py-2 bg-body-light">
                          <p class="fw-medium fs-sm text-muted mb-0">Doctors on Staff</p>
                      </div>
                  </a>
              </div>
              <div class="col-6 col-lg-3">
                  <a class="block block-rounded block-link-shadow text-center" href="{{ $canAppointments ? route('appointments.index') : 'javascript:void(0)' }}">
                      <div class="block-content block-content-full">
                          <div class="fs-2 fw-semibold text-warning">{{ number_format($todayAppointmentsCount) }}</div>
                      </div>
                      <div class="block-content py-2 bg-body-light">
                          <p class="fw-medium fs-sm text-muted mb-0">Today&#39;s Appointments</p>
                      </div>
                  </a>
              </div>
              <div class="col-6 col-lg-3">
                  <a class="block block-rounded block-link-shadow text-center" href="{{ $canAdmissions ? route('admissions.index') : 'javascript:void(0)' }}">
                      <div class="block-content block-content-full">
                          <div class="fs-2 fw-semibold text-success">{{ $availableBeds }} / {{ $totalBeds }}</div>
                      </div>
                      <div class="block-content py-2 bg-body-light">
                          <p class="fw-medium fs-sm text-muted mb-0">Beds Available</p>
                      </div>
                  </a>
              </div>
          </div>

          <div class="row">

              <div class="col-lg-7">
                  <div class="block block-rounded">
                      <div class="block-header block-header-default">
                          <h3 class="block-title">Ward Occupancy</h3>
                      </div>
                      <div class="block-content block-content-full">
                          <div class="table-responsive">
                              <table class="table table-striped table-vcenter">
                                  <thead>
                                      <tr>
                                          <th class="fs-sm">Ward</th>
                                          <th class="d-none d-sm-table-cell text-center fs-sm">Type</th>
                                          <th class="text-center fs-sm">Total Beds</th>
                                          <th class="text-center fs-sm">Occupied</th>
                                          <th class="text-center fs-sm">Available</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @forelse ($wards as $ward)
                                          <tr>
                                              <td>{{ $ward->name }}</td>
                                              <td class="d-none d-sm-table-cell text-center">{{ $ward->type }}</td>
                                              <td class="text-center">{{ $ward->total_beds }}</td>
                                              <td class="text-center">{{ $ward->occupied_beds }}</td>
                                              <td class="text-center {{ $ward->available_beds <= 1 ? 'text-danger' : 'text-success' }} fw-semibold">{{ $ward->available_beds }}</td>
                                          </tr>
                                      @empty
                                          <tr>
                                              <td colspan="5" class="text-center text-muted">No wards found.</td>
                                          </tr>
                                      @endforelse
                                  </tbody>
                              </table>
                          </div>
                          <div class="d-flex justify-content-end mt-3">
                              {{ $wards->links('pagination::bootstrap-5') }}
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-lg-5">
                  <div class="block block-rounded">
                      <div class="block-header block-header-default">
                          <h3 class="block-title">Revenue Snapshot</h3>
                      </div>
                      <div class="block-content">
                          <div class="row text-center items-push pull-t">
                              <div class="col-6">
                                  <div class="fs-sm fw-semibold text-uppercase text-muted">This Month</div>
                                  <div class="fs-3 fw-bold text-success">{!! $monthCollected !!}</div>
                              </div>
                              <div class="col-6">
                                  <div class="fs-sm fw-semibold text-uppercase text-muted">Due Payments</div>
                                  <div class="fs-3 fw-bold text-danger">{!! $totalDue !!}</div>
                              </div>
                          </div>
                          <hr>
                          <p class="fs-sm text-muted mb-1">Collected vs Billed (This Month)</p>
                          <div class="progress mb-3" style="height: 8px;">
                              <div class="progress-bar bg-success" style="width: {{ $collectedPercent }}%"></div>
                          </div>
                          @if ($canInvoices)
                              <a class="btn btn-sm btn-alt-primary w-100" href="{{ route('invoices.index') }}">View All Invoices</a>
                          @endif
                      </div>
                  </div>
              </div>
          </div>
          <div class="block block-rounded">
              <div class="block-header block-header-default">
                  <h3 class="block-title">Today&#39;s Appointments</h3>
              </div>
              <div class="block-content block-content-full">
                  <div class="table-responsive">
                      <table class="table table-striped table-vcenter">
                          <thead>
                              <tr>
                                  <th class="fs-sm">Patient</th>
                                  <th class="d-none d-sm-table-cell text-center fs-sm">Doctor</th>
                                  <th class="d-none d-sm-table-cell text-center fs-sm">Department</th>
                                  <th class="d-none d-sm-table-cell text-center fs-sm">Time</th>
                                  <th class="d-none d-sm-table-cell text-center fs-sm">Status</th>
                                  <th class="text-center fs-sm" style="width: 110px;">Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                              @forelse ($todayAppointments as $appt)
                                  <tr>
                                      <td><a class="fw-semibold" href="{{ route('patients.show', $appt->patient_id) }}">{{ $appt->patient->name ?? 'N/A' }}</a></td>
                                      <td class="d-none d-sm-table-cell text-center">Dr. {{ $appt->doctor->user->name ?? 'N/A' }}</td>
                                      <td class="d-none d-sm-table-cell text-center">{{ $appt->doctor->department->name ?? '—' }}</td>
                                      <td class="d-none d-sm-table-cell text-center">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</td>
                                      <td class="text-center d-none d-sm-table-cell"><span class="badge bg-{{ $statusColors[$appt->status] ?? 'secondary' }}">{{ $appt->status }}</span></td>
                                      <td class="text-center">
                                          <a class="btn btn-sm btn-alt-secondary" href="{{ route('appointments.show', $appt->id) }}"
                                              data-bs-toggle="tooltip" title="View"><i class="fa fa-fw fa-eye"></i></a>
                                          @if ($roleId == 1)
                                              <form action="{{ route('appointments.destroy', $appt->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Delete this appointment?')">
                                                  @csrf
                                                  @method('DELETE')
                                                  <button type="submit" class="btn btn-sm btn-alt-secondary"
                                                      data-bs-toggle="tooltip" title="Delete"><i class="fa fa-fw fa-times"></i></button>
                                              </form>
                                          @endif
                                      </td>
                                  </tr>
                              @empty
                                  <tr>
                                      <td colspan="6" class="text-center text-muted">No appointments scheduled for today.</td>
                                  </tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>

      </div>
      <!-- END Page Content -->
  </main>
  @endsection

