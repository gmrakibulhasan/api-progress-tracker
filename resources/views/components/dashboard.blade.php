<!-- Dashboard Overview Component -->
<div x-data="dashboardData()" x-init="init()">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total APIs -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-code text-white"></i>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Total APIs</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="stats.totalApis"></p>
                </div>
            </div>
        </div>

        <!-- Completed APIs -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-check text-white"></i>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="stats.completedApis"></p>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">In Progress</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="stats.inProgressApis"></p>
                </div>
            </div>
        </div>

        <!-- Active Tasks -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-tasks text-white"></i>
                    </div>
                </div>
                <div class="ml-5">
                    <p class="text-sm font-medium text-gray-500">Active Tasks</p>
                    <p class="text-2xl font-semibold text-gray-900" x-text="stats.activeTasks"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Progress Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">API Development Progress</h3>
            <div class="relative h-64">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Priority Distribution</h3>
            <div class="relative h-64">
                <canvas id="priorityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
        <div class="space-y-4" x-show="recentActivity.length > 0">
            <template x-for="activity in recentActivity" :key="activity.id">
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-code text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-900" x-text="activity.description"></p>
                        <p class="text-xs text-gray-500" x-text="activity.created_at"></p>
                    </div>
                </div>
            </template>
        </div>
        <div x-show="recentActivity.length === 0" class="text-center text-gray-500 py-8">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <p>No recent activity</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('apipt.dashboard') }}?tab=api-progress"
                class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-code-branch text-blue-600 text-2xl mr-4"></i>
                <div>
                    <p class="font-medium text-gray-900">Manage APIs</p>
                    <p class="text-sm text-gray-600">View and update API progress</p>
                </div>
            </a>

            <a href="{{ route('apipt.dashboard') }}?tab=tasks"
                class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-tasks text-green-600 text-2xl mr-4"></i>
                <div>
                    <p class="font-medium text-gray-900">Manage Tasks</p>
                    <p class="text-sm text-gray-600">Create and assign tasks</p>
                </div>
            </a>

            <a href="{{ route('apipt.dashboard') }}?tab=developers"
                class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <i class="fas fa-users text-purple-600 text-2xl mr-4"></i>
                <div>
                    <p class="font-medium text-gray-900">Manage Team</p>
                    <p class="text-sm text-gray-600">Add and manage developers</p>
                </div>
            </a>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function dashboardData() {
            return {
                stats: {
                    totalApis: 0,
                    completedApis: 0,
                    inProgressApis: 0,
                    activeTasks: 0
                },
                recentActivity: [],
                progressChart: null,
                priorityChart: null,

                async init() {
                    await this.loadStats();
                    await this.loadRecentActivity();
                    this.initCharts();
                },

                async loadStats() {
                    try {
                        const response = await fetch('{{ route('apipt.api.stats') }}');
                        const data = await response.json();

                        if (data.success) {
                            this.stats = data.data;
                        }
                    } catch (error) {
                        console.error('Error loading stats:', error);
                    }
                },

                async loadRecentActivity() {
                    try {
                        // Load recent activity (this would need a new endpoint)
                        this.recentActivity = [{
                                id: 1,
                                description: 'API endpoint /api/users was marked as complete',
                                created_at: '2 hours ago'
                            },
                            {
                                id: 2,
                                description: 'New task "Implement authentication" was created',
                                created_at: '4 hours ago'
                            }
                        ];
                    } catch (error) {
                        console.error('Error loading recent activity:', error);
                    }
                },

                initCharts() {
                    // Progress Chart
                    const progressCtx = document.getElementById('progressChart').getContext('2d');
                    this.progressChart = new Chart(progressCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Todo', 'In Progress', 'Complete'],
                            datasets: [{
                                data: [
                                    this.stats.totalApis - this.stats.completedApis - this.stats
                                    .inProgressApis,
                                    this.stats.inProgressApis,
                                    this.stats.completedApis
                                ],
                                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                }
                            }
                        }
                    });

                    // Priority Chart
                    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
                    this.priorityChart = new Chart(priorityCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Low', 'Medium', 'High', 'Urgent'],
                            datasets: [{
                                label: 'APIs by Priority',
                                data: [0, 0, 0, 0], // This would be loaded from API
                                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }
        }
    </script>
@endpush
