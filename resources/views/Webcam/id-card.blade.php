<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card - {{ $student->user->name ?? 'Student' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print { display: none !important; }
            .id-card { 
                page-break-inside: avoid;
                box-shadow: none !important;
            }
        }
        @page {
            size: auto;
            margin: 10mm;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-8">
    <!-- Controls -->
    <div class="no-print fixed top-4 right-4 flex gap-3">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
        <button onclick="window.close()" class="px-4 py-2 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors">
            Close
        </button>
    </div>

    <!-- ID Card - Front -->
    <div class="space-y-8">
        <div class="id-card mx-auto" style="width: 340px;">
            <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-2xl overflow-hidden shadow-2xl">
                <!-- Header -->
                <div class="bg-white/10 backdrop-blur-sm px-4 py-3 text-center border-b border-white/20">
                    <h3 class="text-white font-bold text-lg tracking-wide">ROSHS</h3>
                    <p class="text-white/80 text-xs">Student Identification Card</p>
                </div>

                <!-- Body -->
                <div class="p-5">
                    <div class="flex gap-4">
                        <!-- Photo -->
                        <div class="flex-shrink-0">
                            <div class="w-24 h-28 bg-white rounded-lg overflow-hidden flex items-center justify-center border-2 border-white/50">
                                @if($student->user && $student->user->profile_picture)
                                    <img src="{{ asset($student->user->profile_picture) }}" class="w-full h-full object-cover" alt="Student Photo">
                                @else
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 text-white space-y-2">
                            <div>
                                <p class="text-xs text-white/60 uppercase tracking-wide">Name</p>
                                <p class="font-semibold text-sm">{{ $student->user->name ?? 'Unknown' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-white/60 uppercase tracking-wide">Class</p>
                                <p class="font-medium text-sm">{{ $student->class->class_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-white/60 uppercase tracking-wide">Roll No.</p>
                                <p class="font-medium text-sm">{{ $student->roll_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-4 grid grid-cols-2 gap-2 text-white">
                        <div class="bg-white/10 rounded-lg p-2">
                            <p class="text-xs text-white/60 uppercase tracking-wide">Gender</p>
                            <p class="font-medium text-sm">{{ ucfirst($student->gender ?? 'N/A') }}</p>
                        </div>
                        <div class="bg-white/10 rounded-lg p-2">
                            <p class="text-xs text-white/60 uppercase tracking-wide">DOB</p>
                            <p class="font-medium text-sm">{{ $student->dateofbirth ? \Carbon\Carbon::parse($student->dateofbirth)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- ID Number -->
                    <div class="mt-4 bg-white/10 rounded-lg p-3 text-center">
                        <p class="text-xs text-white/60 uppercase tracking-wide">Student ID</p>
                        <p class="font-mono font-bold text-white text-xl tracking-wider">ROSHS-{{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-white/10 backdrop-blur-sm px-4 py-2 text-center border-t border-white/20">
                    <p class="text-white/70 text-xs">Valid for Academic Year {{ date('Y') }}-{{ date('Y') + 1 }}</p>
                </div>
            </div>
        </div>

        <!-- ID Card - Back -->
        <div class="id-card mx-auto" style="width: 340px;">
            <div class="bg-gradient-to-br from-gray-700 via-gray-800 to-gray-900 rounded-2xl overflow-hidden shadow-2xl">
                <!-- Header -->
                <div class="bg-white/10 backdrop-blur-sm px-4 py-3 text-center border-b border-white/20">
                    <h3 class="text-white font-bold text-lg tracking-wide">ROSHS</h3>
                    <p class="text-white/80 text-xs">Terms & Conditions</p>
                </div>

                <!-- Body -->
                <div class="p-5 text-white/80 text-xs space-y-3">
                    <div class="space-y-2">
                        <p class="flex items-start gap-2">
                            <span class="text-white/60">1.</span>
                            <span>This card is the property of ROSHS and must be returned upon request.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-white/60">2.</span>
                            <span>This card is non-transferable and must be carried at all times on school premises.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-white/60">3.</span>
                            <span>Report immediately if lost or stolen.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-white/60">4.</span>
                            <span>Any misuse of this card will result in disciplinary action.</span>
                        </p>
                    </div>

                    <div class="border-t border-white/20 pt-3">
                        <p class="text-center text-white/60">If found, please return to:</p>
                        <p class="text-center font-semibold text-white">ROSHS Administration Office</p>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="bg-white/10 rounded-lg p-3 text-center">
                        <p class="text-white/60 text-xs uppercase tracking-wide">Emergency Contact</p>
                        <p class="font-semibold text-white">+263 XXX XXX XXX</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-white/10 backdrop-blur-sm px-4 py-2 text-center border-t border-white/20">
                    <p class="text-white/70 text-xs">www.roshs.edu.zw</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
