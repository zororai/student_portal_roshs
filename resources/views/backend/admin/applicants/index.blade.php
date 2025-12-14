@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Student Applicants</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Total Applications:</span>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-bold">{{ $applications->count() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Pending:</span>
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-bold">{{ $applications->where('status', 'pending')->count() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Approved:</span>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-bold">{{ $applications->where('status', 'approved')->count() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Rejected:</span>
                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-bold">{{ $applications->where('status', 'rejected')->count() }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applying For</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guardian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $index => $application)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $application->first_name }} {{ $application->last_name }}</div>
                                <div class="text-sm text-gray-500">{{ $application->date_of_birth ? $application->date_of_birth->format('d M Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $application->gender == 'Male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $application->gender }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $application->applying_for_form }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $application->guardian_full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $application->guardian_phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($application->status == 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($application->status == 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $application->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.applicants.show', $application->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                <form action="{{ route('admin.applicants.destroy', $application->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this application?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
