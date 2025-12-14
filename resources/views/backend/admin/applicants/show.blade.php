@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Application Details</h1>
        <a href="{{ route('admin.applicants.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Student Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">First Name</label>
                            <p class="text-gray-900">{{ $application->first_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Last Name</label>
                            <p class="text-gray-900">{{ $application->last_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Gender</label>
                            <p class="text-gray-900">{{ $application->gender }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date of Birth</label>
                            <p class="text-gray-900">{{ $application->date_of_birth ? $application->date_of_birth->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Applying for Form</label>
                            <p class="text-gray-900">{{ $application->applying_for_form }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Religion</label>
                            <p class="text-gray-900">{{ $application->religion ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Street Address</label>
                            <p class="text-gray-900">{{ $application->street_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Residential Area</label>
                            <p class="text-gray-900">{{ $application->residential_area ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">School Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">School Applying For</label>
                            <p class="text-gray-900">{{ $application->school_applying_for ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Previous School</label>
                            <p class="text-gray-900">{{ $application->previous_school ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($application->subjects_of_interest)
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-500">Subjects of Interest</label>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($application->subjects_of_interest as $subject)
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">{{ $subject }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Guardian Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Guardian Name</label>
                            <p class="text-gray-900">{{ $application->guardian_full_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Relationship</label>
                            <p class="text-gray-900">{{ $application->guardian_relationship }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone Number</label>
                            <p class="text-gray-900">{{ $application->guardian_phone }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $application->guardian_email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Other Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Birth Entry Number</label>
                            <p class="text-gray-900">{{ $application->birth_entry_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Dream Job</label>
                            <p class="text-gray-900">{{ $application->dream_job ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Expected Start Date</label>
                            <p class="text-gray-900">{{ $application->expected_start_date ? $application->expected_start_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($application->documents && count($application->documents) > 0)
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Uploaded Documents</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-2">
                        @foreach($application->documents as $document)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <span class="text-gray-700">{{ $document['name'] ?? 'Document' }}</span>
                                <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow sticky top-4">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Application Status</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-500">Current Status</label>
                        <div class="mt-1">
                            @if($application->status == 'pending')
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @elseif($application->status == 'approved')
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-500">Submitted On</label>
                        <p class="text-gray-900">{{ $application->created_at->format('d M Y, h:i A') }}</p>
                    </div>

                    <form action="{{ route('admin.applicants.updateStatus', $application->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                            <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                            <textarea name="admin_notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $application->admin_notes }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
