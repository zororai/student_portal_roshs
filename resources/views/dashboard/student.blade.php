

    <div class="w-full block mt-8 bg-white">
        <div class="flex flex-wrap sm:flex-no-wrap justify-between">
            <div class="w-full sm:w-1/2 mr-2 mb-6">
                <!-- Log on to codeastro.com for more projects -->
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Name : 
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <span class="block text-gray-600 font-bold">{{ $student->user->name }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Email :
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <span class="text-gray-600 font-bold">{{ $student->user->email }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Class :
                        </label>
                    </div>
                    <div class="md:w-2/3 block text-gray-600 font-bold">
                        <span class="text-gray-600 font-bold">{{ $student->class->class_name ?? 'Not Assigned' }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Roll Number :
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <span class="text-gray-600 font-bold">{{ $student->roll_number }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Phone :
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <span class="text-gray-600 font-bold">{{ $student->phone }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Gender :
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <span class="text-gray-600 font-bold">{{ $student->gender }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Date of Birth :
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <span class="text-gray-600 font-bold">{{ $student->dateofbirth }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Current Address :
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <span class="text-gray-600 font-bold">{{ $student->current_address }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Permanent Address :
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <span class="text-gray-600 font-bold">{{ $student->permanent_address }}</span>
                    </div>
                </div>
                <!-- Log on to codeastro.com for more projects -->
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Student's Parent :
                        </label>
                    </div>
                    <div class="md:w-2/3 block text-gray-600 font-bold">
                        <span class="text-gray-600 font-bold">{{ $student->parent->user->name }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Parent's Email :
                        </label>
                    </div>
                    <div class="md:w-2/3 block text-gray-600 font-bold">
                        <span class="text-gray-600 font-bold">{{ $student->parent->user->email }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Parent's Phone :
                        </label>
                    </div>
                    <div class="md:w-2/3 block text-gray-600 font-bold">
                        <span class="text-gray-600 font-bold">{{ $student->parent->phone }}</span>
                    </div>
                </div>
                <div class="md:flex md:items-center mb-3">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">
                            Parent's Address :
                        </label>
                    </div>
                    <div class="md:w-2/3 block text-gray-600 font-bold">
                        <span class="text-gray-600 font-bold">{{ $student->parent->current_address }}</span>
                    </div>
                </div>
    
    
            </div>   
            
            <!-- Assessment Performance Section -->
            <div class="w-full sm:w-1/2 ml-2 mb-6">
                @if(isset($subjectStats) && count($subjectStats) > 0)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3">Subject Performance</h4>
                    <div class="space-y-3">
                        @foreach($subjectStats as $stat)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $stat['subject'] }}</span>
                                <span class="font-bold {{ $stat['performance'] >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $stat['performance'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $stat['performance'] >= 50 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ $stat['performance'] }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $stat['assessments'] }} assessments</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

        </div>

    </div>

    <!-- My Assessment Performance -->
    @if(isset($studentAssessmentStats))
    <div class="w-full block mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">My Assessment Performance</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart -->
                <div>
                    <canvas id="studentAssessmentChart" height="300"></canvas>
                </div>
                <!-- Table -->
                <div>
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Performance Summary</h5>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-2 px-3">Assessment Type</th>
                                <th class="text-center py-2 px-3">Taken</th>
                                <th class="text-center py-2 px-3">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentAssessmentStats as $stat)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 px-3 text-gray-700">{{ $stat['type'] }}</td>
                                <td class="text-center py-2 px-3">{{ $stat['given'] }}</td>
                                <td class="text-center py-2 px-3">
                                    @if($stat['given'] > 0)
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $stat['performance'] >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $stat['performance'] }}%
                                    </span>
                                    @else
                                    <span class="text-gray-400">--</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('studentAssessmentChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(collect($studentAssessmentStats)->pluck('type')) !!},
                    datasets: [{
                        label: 'My Performance %',
                        data: {!! json_encode(collect($studentAssessmentStats)->pluck('performance')) !!},
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(236, 72, 153, 0.7)',
                            'rgba(20, 184, 166, 0.7)',
                            'rgba(249, 115, 22, 0.7)',
                            'rgba(99, 102, 241, 0.7)',
                            'rgba(34, 197, 94, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: { display: true, text: 'Performance (%)' }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'My Performance by Assessment Type'
                        }
                    }
                }
            });
        }
    });
    </script>
    @endif