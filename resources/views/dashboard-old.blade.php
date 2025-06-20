<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Progress Tracker</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .priority-low { @apply bg-green-100 text-green-800 border-green-200; }
        .priority-normal { @apply bg-blue-100 text-blue-800 border-blue-200; }
        .priority-high { @apply bg-yellow-100 text-yellow-800 border-yellow-200; }
        .priority-urgent { @apply bg-red-100 text-red-800 border-red-200; }
        .status-todo { @apply bg-gray-100 text-gray-800 border-gray-200; }
        .status-in_progress { @apply bg-blue-100 text-blue-800 border-blue-200; }
        .status-issue { @apply bg-red-100 text-red-800 border-red-200; }
        .status-not_needed { @apply bg-purple-100 text-purple-800 border-purple-200; }
        .status-complete { @apply bg-green-100 text-green-800 border-green-200; }
        .loading { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body class="bg-gray-50" x-data="apiProgressTracker()" x-cloak>
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">API Progress Tracker</h1>
                        <span class="ml-2 text-sm text-gray-500">by Rakibul Hasan</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button @click="syncRoutes()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Sync Routes
                        </button>
                        <button @click="refreshData()"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-8">
                    <button @click="activeTab = 'dashboard'"
                        :class="activeTab === 'dashboard' ? 'border-blue-500 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm">
                        Dashboard
                    </button>
                    <button @click="activeTab = 'developers'"
                        :class="activeTab === 'developers' ? 'border-blue-500 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm">
                        Developers
                    </button>
                    <button @click="activeTab = 'api-progress'"
                        :class="activeTab === 'api-progress' ? 'border-blue-500 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm">
                        API Progress
                    </button>
                    <button @click="activeTab = 'tasks'"
                        :class="activeTab === 'tasks' ? 'border-blue-500 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="py-4 px-1 border-b-2 font-medium text-sm">
                        Tasks
                    </button>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Dashboard Tab -->
            <div x-show="activeTab === 'dashboard'" class="space-y-6">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Developers Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Developers</dt>
                                    <dd class="text-lg font-medium text-gray-900" x-text="stats.developers?.total || 0">
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- API Progress Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">API Endpoints</dt>
                                    <dd class="text-lg font-medium text-gray-900"
                                        x-text="stats.api_progress?.total || 0"></dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Tasks</dt>
                                    <dd class="text-lg font-medium text-gray-900" x-text="stats.tasks?.total || 0"></dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Comments Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Comments</dt>
                                    <dd class="text-lg font-medium text-gray-900" x-text="stats.comments?.total || 0">
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- API Progress Status Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">API Progress by Status</h3>
                        <canvas id="apiProgressChart" width="400" height="200"></canvas>
                    </div>

                    <!-- Task Priority Chart -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tasks by Priority</h3>
                        <canvas id="taskPriorityChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Comments</h3>
                    </div>
                    <div class="px-6 py-4">
                        <template x-for="comment in stats.comments?.recent || []" :key="comment.id">
                            <div class="flex items-start space-x-3 py-3 border-b border-gray-100 last:border-b-0">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700"
                                            x-text="comment.developer?.name?.charAt(0) || 'U'"></span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900"
                                        x-text="comment.developer?.name || 'Unknown'"></p>
                                    <p class="text-sm text-gray-500" x-text="comment.description"></p>
                                    <p class="text-xs text-gray-400"
                                        x-text="new Date(comment.created_at).toLocaleString()"></p>
                                </div>
                            </div>
                        </template>
                        <div x-show="!stats.comments?.recent?.length" class="text-center py-8 text-gray-500">
                            No recent comments found.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Developers Tab -->
            <div x-show="activeTab === 'developers'" class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Developers</h3>
                        <button @click="showDeveloperModal = true"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Add Developer
                        </button>
                    </div>
                    <div class="p-6">
                        <!-- Search -->
                        <div class="mb-4">
                            <input type="text" x-model="developerSearch" @input="searchDevelopers()"
                                placeholder="Search developers..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Developers Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="developer in developers.data || []" :key="developer.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                                x-text="developer.name"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                x-text="developer.email"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                x-text="new Date(developer.created_at).toLocaleDateString()"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button @click="editDeveloper(developer)"
                                                    class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                                <button @click="deleteDeveloper(developer.id)"
                                                    class="text-red-600 hover:text-red-900">Delete</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div x-show="developers.last_page > 1" class="mt-6 flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Showing <span x-text="developers.from"></span> to <span x-text="developers.to"></span>
                                of <span x-text="developers.total"></span> results
                            </div>
                            <div class="flex space-x-2">
                                <button @click="loadDevelopers(developers.current_page - 1)"
                                    :disabled="developers.current_page <= 1"
                                    class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md disabled:opacity-50">
                                    Previous
                                </button>
                                <button @click="loadDevelopers(developers.current_page + 1)"
                                    :disabled="developers.current_page >= developers.last_page"
                                    class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-md disabled:opacity-50">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Progress Tab -->
            <div x-show="activeTab === 'api-progress'" class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">API Progress</h3>
                        <button @click="showApiProgressModal = true"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Add API Endpoint
                        </button>
                    </div>
                    <div class="p-6">
                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <input type="text" x-model="apiProgressSearch" @input="searchApiProgress()"
                                placeholder="Search endpoints..."
                                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <select x-model="apiProgressFilters.status" @change="loadApiProgress()"
                                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <option value="todo">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="issue">Issue</option>
                                <option value="not_needed">Not Needed</option>
                                <option value="complete">Complete</option>
                            </select>

                            <select x-model="apiProgressFilters.priority" @change="loadApiProgress()"
                                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Priority</option>
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>

                            <select x-model="apiProgressFilters.group" @change="loadApiProgress()"
                                class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Groups</option>
                                <template x-for="group in Object.keys(stats.api_progress?.by_group || {})"
                                    :key="group">
                                    <option :value="group" x-text="group"></option>
                                </template>
                            </select>
                        </div>

                        <!-- API Progress Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Method</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Endpoint</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Group</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Priority</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-for="item in apiProgress.data || []" :key="item.id">
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                                    :class="getMethodClass(item.method)" x-text="item.method"></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                                x-text="item.endpoint"></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                                x-text="item.group_name || 'N/A'"></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                                    :class="'priority-' + item.priority"
                                                    x-text="item.priority"></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full"
                                                    :class="'status-' + item.status"
                                                    x-text="item.status.replace('_', ' ')"></span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button @click="viewComments('api-progress', item.id)"
                                                    class="text-blue-600 hover:text-blue-900">Comments</button>
                                                <button @click="editApiProgress(item)"
                                                    class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                                <button @click="deleteApiProgress(item.id)"
                                                    class="text-red-600 hover:text-red-900">Delete</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Tab -->
            <div x-show="activeTab === 'tasks'" class="space-y-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Tasks</h3>
                        <button @click="showTaskModal = true"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Add Task
                        </button>
                    </div>
                    <div class="p-6">
                        <!-- Task filters and table similar to API Progress -->
                        <div class="text-center py-8 text-gray-500">
                            Tasks management coming soon...
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modals and other components will be added here -->

        <!-- Loading Overlay -->
        <div x-show="loading" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6">
                <div class="flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function apiProgressTracker() {
            return {
                // State
                activeTab: 'dashboard',
                loading: false,

                // Data
                stats: {},
                developers: {},
                apiProgress: {},
                tasks: {},

                // Search & Filters
                developerSearch: '',
                apiProgressSearch: '',
                apiProgressFilters: {
                    status: '',
                    priority: '',
                    group: ''
                },

                // Modals
                showDeveloperModal: false,
                showApiProgressModal: false,
                showTaskModal: false,

                // Initialize
                init() {
                    this.loadStats();
                    this.loadDevelopers();
                    this.loadApiProgress();
                    this.loadTasks();
                },

                // API Methods
                async makeRequest(url, options = {}) {
                    this.loading = true;
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                ...options.headers
                            },
                            ...options
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        return await response.json();
                    } catch (error) {
                        console.error('Request failed:', error);
                        alert('Request failed. Please try again.');
                        return null;
                    } finally {
                        this.loading = false;
                    }
                },

                // Data Loading Methods
                async loadStats() {
                    const data = await this.makeRequest('/api-progress/api/stats');
                    if (data) {
                        this.stats = data;
                        this.updateCharts();
                    }
                },

                async loadDevelopers(page = 1) {
                    const params = new URLSearchParams({
                        page
                    });
                    if (this.developerSearch) params.append('search', this.developerSearch);

                    const data = await this.makeRequest(`/api-progress/api/developers?${params}`);
                    if (data) this.developers = data;
                },

                async loadApiProgress(page = 1) {
                    const params = new URLSearchParams({
                        page
                    });
                    if (this.apiProgressSearch) params.append('search', this.apiProgressSearch);
                    Object.entries(this.apiProgressFilters).forEach(([key, value]) => {
                        if (value) params.append(key, value);
                    });

                    const data = await this.makeRequest(`/api-progress/api/api-progress?${params}`);
                    if (data) this.apiProgress = data;
                },

                async loadTasks(page = 1) {
                    const data = await this.makeRequest(`/api-progress/api/tasks?page=${page}`);
                    if (data) this.tasks = data;
                },

                // Search Methods
                searchDevelopers() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => this.loadDevelopers(), 500);
                },

                searchApiProgress() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => this.loadApiProgress(), 500);
                },

                // Action Methods
                async syncRoutes() {
                    if (confirm('This will sync all API routes. Continue?')) {
                        this.loading = true;
                        try {
                            // Call artisan command via API (you'll need to implement this)
                            await this.makeRequest('/api-progress/api/sync-routes', {
                                method: 'POST'
                            });
                            await this.loadApiProgress();
                            await this.loadStats();
                            alert('Routes synced successfully!');
                        } catch (error) {
                            console.error('Sync failed:', error);
                            alert('Sync failed. Please try again.');
                        } finally {
                            this.loading = false;
                        }
                    }
                },

                refreshData() {
                    this.loadStats();
                    this.loadDevelopers();
                    this.loadApiProgress();
                    this.loadTasks();
                },

                // Helper Methods
                getMethodClass(method) {
                    const classes = {
                        'GET': 'bg-green-100 text-green-800',
                        'POST': 'bg-blue-100 text-blue-800',
                        'PUT': 'bg-yellow-100 text-yellow-800',
                        'PATCH': 'bg-orange-100 text-orange-800',
                        'DELETE': 'bg-red-100 text-red-800'
                    };
                    return classes[method] || 'bg-gray-100 text-gray-800';
                },

                // Chart Methods
                updateCharts() {
                    this.$nextTick(() => {
                        this.createApiProgressChart();
                        this.createTaskPriorityChart();
                    });
                },

                createApiProgressChart() {
                    const ctx = document.getElementById('apiProgressChart');
                    if (!ctx) return;

                    const data = this.stats.api_progress?.by_status || {};
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(data).map(key => key.replace('_', ' ')),
                            datasets: [{
                                data: Object.values(data),
                                backgroundColor: [
                                    '#EF4444', // red for todo
                                    '#3B82F6', // blue for in_progress
                                    '#F59E0B', // yellow for issue
                                    '#8B5CF6', // purple for not_needed
                                    '#10B981' // green for complete
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                },

                createTaskPriorityChart() {
                    const ctx = document.getElementById('taskPriorityChart');
                    if (!ctx) return;

                    const data = this.stats.tasks?.by_priority || {};
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(data),
                            datasets: [{
                                label: 'Tasks',
                                data: Object.values(data),
                                backgroundColor: [
                                    '#10B981', // green for low
                                    '#3B82F6', // blue for normal
                                    '#F59E0B', // yellow for high
                                    '#EF4444' // red for urgent
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
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
</body>

</html>
