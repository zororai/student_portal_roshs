@extends('layouts.app')

@section('title', 'Teacher Sessions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock text-primary mr-2"></i>
                        Manage Teacher Sessions
                    </h4>
                    <a href="{{ route('teacher.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Teachers
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Note:</strong> Assign each teacher to their work session (Morning, Afternoon, or Both). 
                        This determines which attendance time slot applies to them.
                    </div>

                    <form action="{{ route('teacher.update-sessions') }}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect" value="teacher.sessions">

                        <!-- Quick Set Buttons -->
                        <div class="mb-4">
                            <span class="font-weight-bold mr-3">Quick Set All:</span>
                            <button type="button" class="btn btn-warning btn-sm mr-2" onclick="setAllSessions('morning')">
                                <i class="fas fa-sun mr-1"></i> All Morning
                            </button>
                            <button type="button" class="btn btn-info btn-sm mr-2" onclick="setAllSessions('afternoon')">
                                <i class="fas fa-moon mr-1"></i> All Afternoon
                            </button>
                            <button type="button" class="btn btn-purple btn-sm" onclick="setAllSessions('both')">
                                <i class="fas fa-calendar-alt mr-1"></i> All Both
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Teacher Name</th>
                                        <th>Current Session</th>
                                        <th style="width: 400px;">Select Session</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teachers as $index => $teacher)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('images/profile/' . ($teacher->user->profile_picture ?? 'avatar.png')) }}" 
                                                     alt="{{ $teacher->user->name }}" 
                                                     class="rounded-circle mr-2" 
                                                     style="width: 35px; height: 35px; object-fit: cover;">
                                                <span>{{ $teacher->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($teacher->session === 'morning')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-sun mr-1"></i> Morning
                                                </span>
                                            @elseif($teacher->session === 'afternoon')
                                                <span class="badge badge-info">
                                                    <i class="fas fa-moon mr-1"></i> Afternoon
                                                </span>
                                            @else
                                                <span class="badge badge-purple" style="background-color: #6f42c1; color: white;">
                                                    <i class="fas fa-calendar-alt mr-1"></i> Both
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <label class="btn btn-outline-warning {{ $teacher->session === 'morning' ? 'active' : '' }}">
                                                    <input type="radio" name="sessions[{{ $teacher->id }}]" value="morning" 
                                                           {{ $teacher->session === 'morning' ? 'checked' : '' }} autocomplete="off">
                                                    <i class="fas fa-sun mr-1"></i> Morning
                                                </label>
                                                <label class="btn btn-outline-info {{ $teacher->session === 'afternoon' ? 'active' : '' }}">
                                                    <input type="radio" name="sessions[{{ $teacher->id }}]" value="afternoon" 
                                                           {{ $teacher->session === 'afternoon' ? 'checked' : '' }} autocomplete="off">
                                                    <i class="fas fa-moon mr-1"></i> Afternoon
                                                </label>
                                                <label class="btn btn-outline-purple {{ ($teacher->session === 'both' || !$teacher->session) ? 'active' : '' }}" style="border-color: #6f42c1; color: #6f42c1;">
                                                    <input type="radio" name="sessions[{{ $teacher->id }}]" value="both" 
                                                           {{ ($teacher->session === 'both' || !$teacher->session) ? 'checked' : '' }} autocomplete="off">
                                                    <i class="fas fa-calendar-alt mr-1"></i> Both
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                            No teachers found. Please add teachers first.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($teachers->count() > 0)
                        <div class="text-right mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i> Save Teacher Sessions
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-outline-purple:hover,
    .btn-outline-purple.active {
        background-color: #6f42c1 !important;
        color: white !important;
        border-color: #6f42c1 !important;
    }
    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: white;
    }
    .btn-purple:hover {
        background-color: #5a32a3;
        border-color: #5a32a3;
        color: white;
    }
</style>

<script>
function setAllSessions(session) {
    document.querySelectorAll(`input[value="${session}"]`).forEach(function(radio) {
        radio.checked = true;
        // Update button group visual state
        radio.closest('.btn-group').querySelectorAll('label').forEach(function(label) {
            label.classList.remove('active');
        });
        radio.closest('label').classList.add('active');
    });
}
</script>
@endsection
