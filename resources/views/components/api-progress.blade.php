<!-- API Progress Management Component -->
<div x-data="apiProgressData()" x-init="init()" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50/30">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">API Progress Dashboard</h1>
                    <p class="text-gray-600 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        Track and manage API development progress
                        <span x-show="apis.length > 0"
                            class="ml-4 px-2 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            <span x-text="apis.length"></span> total APIs
                        </span>
                        <span x-show="overallCompletion !== null"
                            class="ml-2 px-3 py-1 bg-gradient-to-r from-emerald-100 to-emerald-200 text-emerald-800 text-sm font-bold rounded-full">
                            <span x-text="overallCompletion"></span>% complete
                        </span>
                    </p>
                </div>
                <div class="hidden sm:block">
                    <button @click="syncRoutes()"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Sync Routes
                    </button>
                </div>
            </div>
        </div>

        <!-- Enhanced Filters Section -->
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-white/50 p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
                <div class="flex-1 min-w-0">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" x-model="searchTerm" @input="filterApis()"
                            placeholder="Search APIs, endpoints, or descriptions..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm transition-all duration-200">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <select x-model="statusFilter" @change="filterApis()"
                        class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm min-w-[140px]">
                        <option value="">All Statuses</option>
                        <option value="todo">📋 Todo</option>
                        <option value="in_progress">⚡ In Progress</option>
                        <option value="issue">⚠️ Issue</option>
                        <option value="not_needed">❌ Not Needed</option>
                        <option value="complete">✅ Complete</option>
                    </select>
                    <select x-model="priorityFilter" @change="filterApis()"
                        class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm min-w-[140px]">
                        <option value="">All Priorities</option>
                        <option value="low">🟢 Low</option>
                        <option value="normal">🟡 Normal</option>
                        <option value="high">🟠 High</option>
                        <option value="urgent">🔴 Urgent</option>
                    </select>
                    <select x-model="developerFilter" @change="filterApis()"
                        class="px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/80 backdrop-blur-sm min-w-[140px]">
                        <option value="">All Developers</option>
                        <template x-for="developer in developers" :key="developer.id">
                            <option :value="developer.id" x-text="`👤 ${developer.name}`"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>

        <!-- API Groups -->
        <div class="space-y-6" x-show="!loading">
            <template x-for="group in groupedApis" :key="group.name">
                <div
                    class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-sm border border-white/50 overflow-hidden hover:shadow-md transition-all duration-300">
                    <!-- Enhanced Group Header -->
                    <div class="flex items-center justify-between p-6 border-b border-gray-100 cursor-pointer hover:bg-gray-50/50 transition-colors duration-200"
                        @click="toggleGroup(group.name)">
                        <div class="flex items-center space-x-4">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg">
                                <svg class="w-5 h-5 transition-transform duration-200"
                                    :class="group.expanded ? 'rotate-90' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900" x-text="group.name"></h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span x-text="group.apis.length"></span> APIs •
                                    <span x-text="group.apis.filter(api => api.status === 'complete').length"></span>
                                    completed •
                                    <span
                                        x-text="group.apis.reduce((sum, api) => sum + (api.comments_count || 0), 0)"></span>
                                    comments
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900" x-text="`${group.progress}%`"></div>
                                <div class="text-xs text-gray-500">Progress</div>
                            </div>
                            <div class="w-32">
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 ease-out"
                                        :class="group.progress === 100 ? 'bg-gradient-to-r from-emerald-500 to-emerald-600' :
                                            'bg-gradient-to-r from-blue-500 to-blue-600'"
                                        :style="`width: ${group.progress}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Group Content -->
                    <div x-show="group.expanded" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="p-6 space-y-4 bg-gradient-to-br from-gray-50/50 to-white/50">
                        <template x-for="api in group.apis" :key="api.id">
                            <div
                                class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 hover:border-blue-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-3 mb-3">
                                            <span
                                                class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full"
                                                :class="{
                                                    'bg-blue-100 text-blue-800': api.method === 'GET',
                                                    'bg-emerald-100 text-emerald-800': api.method === 'POST',
                                                    'bg-amber-100 text-amber-800': api.method === 'PUT',
                                                    'bg-red-100 text-red-800': api.method === 'DELETE',
                                                    'bg-purple-100 text-purple-800': !['GET', 'POST', 'PUT', 'DELETE']
                                                        .includes(api.method)
                                                }"
                                                x-text="api.method"></span>
                                            <code
                                                class="font-mono text-sm font-medium text-gray-900 bg-gray-100 px-2 py-1 rounded"
                                                x-text="api.endpoint"></code>
                                        </div>
                                        <p class="text-sm text-gray-600 leading-relaxed"
                                            x-text="api.description || 'No description available'"></p>

                                        <!-- Assigned Developers -->
                                        <div x-show="api.developers && api.developers.length > 0"
                                            class="mt-3 flex items-center space-x-2">
                                            <span class="text-xs text-gray-500 font-medium">Assigned to:</span>
                                            <div class="flex flex-wrap gap-1">
                                                <template x-for="developer in api.developers" :key="developer.id">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                        <span x-text="developer.name"></span>
                                                        <button @click="unassignDeveloper(api, developer.id)"
                                                            class="ml-1 text-blue-600 hover:text-blue-800">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Estimated Completion Time -->
                                        <div x-show="api.estimated_completion_time" class="mt-2">
                                            <span class="text-xs text-gray-500">
                                                Est. completion: <span
                                                    x-text="new Date(api.estimated_completion_time).toLocaleDateString()"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3 ml-4">
                                        <!-- Enhanced Status Dropdown -->
                                        <div class="relative">
                                            <select x-model="api.status" @change="updateApiStatus(api)"
                                                class="appearance-none px-3 py-2 text-xs font-medium border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer pr-8"
                                                :class="{
                                                    'text-gray-600 bg-gray-50': api.status === 'todo',
                                                    'text-blue-600 bg-blue-50': api.status === 'in_progress',
                                                    'text-amber-600 bg-amber-50': api.status === 'issue',
                                                    'text-red-600 bg-red-50': api.status === 'not_needed',
                                                    'text-emerald-600 bg-emerald-50': api.status === 'complete'
                                                }">
                                                <option value="todo">📋 Todo</option>
                                                <option value="in_progress">⚡ In Progress</option>
                                                <option value="issue">⚠️ Issue</option>
                                                <option value="not_needed">❌ Not Needed</option>
                                                <option value="complete">✅ Complete</option>
                                            </select>
                                            <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>

                                        <!-- Enhanced Priority Dropdown -->
                                        <div class="relative">
                                            <select x-model="api.priority" @change="updateApiPriority(api)"
                                                class="appearance-none px-3 py-2 text-xs font-medium border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer pr-8"
                                                :class="{
                                                    'text-green-600 bg-green-50': api.priority === 'low',
                                                    'text-yellow-600 bg-yellow-50': api.priority === 'normal',
                                                    'text-orange-600 bg-orange-50': api.priority === 'high',
                                                    'text-red-600 bg-red-50': api.priority === 'urgent'
                                                }">
                                                <option value="low">🟢 Low</option>
                                                <option value="normal">🟡 Normal</option>
                                                <option value="high">🟠 High</option>
                                                <option value="urgent">🔴 Urgent</option>
                                            </select>
                                            <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>

                                        <!-- Enhanced Action Button with Comment Count -->
                                        <div class="flex items-center space-x-2">
                                            <button @click="showAssignDevelopers(api)"
                                                class="flex items-center justify-center px-3 py-2 text-purple-600 hover:text-purple-700 hover:bg-purple-50 rounded-lg transition-all duration-200 group"
                                                title="Assign Developers">
                                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button @click="showComments(api)"
                                                class="flex items-center justify-center px-3 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all duration-200 group relative">
                                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                                    </path>
                                                </svg>
                                                <span x-show="api.comments_count > 0" class="ml-1 text-sm font-medium"
                                                    x-text="api.comments_count"></span>
                                                <span x-show="api.comments_count === 0"
                                                    class="ml-1 text-sm text-gray-400">0</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Enhanced Loading State -->
        <div x-show="loading" class="flex flex-col justify-center items-center py-20">
            <div class="relative">
                <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-200"></div>
                <div
                    class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600 border-t-transparent absolute top-0 left-0">
                </div>
            </div>
            <p class="mt-4 text-gray-600 font-medium">Loading API progress...</p>
        </div>

        <!-- Enhanced Empty State -->
        <div x-show="!loading && apis.length === 0" class="text-center py-20">
            <div
                class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No APIs Found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">Get started by syncing your routes to automatically discover
                and track your API endpoints.</p>
            <button @click="syncRoutes()"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Sync Routes Now
            </button>
        </div>

        <!-- Enhanced Comments Modal -->
        <div x-show="showCommentsModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click.self="closeCommentsModal()">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.stop
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100">

                <!-- Enhanced Modal Header -->
                <div
                    class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Comments & Discussion</h3>
                            <p class="text-sm text-gray-600" x-show="currentApi">
                                <span class="font-mono bg-white px-2 py-1 rounded text-xs"
                                    x-text="currentApi ? `${currentApi.method} ${currentApi.endpoint}` : ''"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="closeCommentsModal()"
                        class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Enhanced Comments Content -->
                <div class="flex flex-col h-[70vh]">
                    <!-- Comments List -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/30">
                        <div x-show="loadingComments" class="flex justify-center items-center py-12">
                            <div class="relative">
                                <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-200"></div>
                                <div
                                    class="animate-spin rounded-full h-8 w-8 border-2 border-blue-600 border-t-transparent absolute top-0 left-0">
                                </div>
                            </div>
                        </div>

                        <div x-show="!loadingComments && comments.length === 0" class="text-center py-12">
                            <div
                                class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">No comments yet</h4>
                            <p class="text-gray-500">Start the conversation by adding the first comment!</p>
                        </div>

                        <!-- Enhanced Comments Tree -->
                        <template x-for="comment in comments" :key="comment.id">
                            <div class="space-y-4">
                                <!-- Parent Comment -->
                                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                                    <div class="flex items-start space-x-4">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <span class="font-semibold text-gray-900"
                                                    x-text="comment.developer.name"></span>
                                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full"
                                                    x-text="formatDate(comment.created_at)"></span>
                                            </div>
                                            <p class="text-gray-700 leading-relaxed mb-3"
                                                x-text="comment.description"></p>
                                            <button @click="showReplyForm(comment)"
                                                class="inline-flex items-center text-xs text-blue-600 hover:text-blue-700 font-medium hover:bg-blue-50 px-2 py-1 rounded transition-colors duration-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6">
                                                    </path>
                                                </svg>
                                                Reply
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Enhanced Reply Form -->
                                    <div x-show="replyingTo === comment.id" x-transition class="mt-4 ml-14">
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <textarea x-model="replyText" placeholder="Write a thoughtful reply..." rows="3"
                                                class="w-full px-3 py-2 border border-gray-200 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"></textarea>
                                            <div class="flex justify-end space-x-2 mt-3">
                                                <button @click="cancelReply()"
                                                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                                    Cancel
                                                </button>
                                                <button @click="submitReply(comment.id)"
                                                    :disabled="!replyText.trim() || submittingReply"
                                                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                                    <span x-show="!submittingReply">Post Reply</span>
                                                    <span x-show="submittingReply">Posting...</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enhanced Replies -->
                                    <div x-show="comment.replies && comment.replies.length > 0"
                                        class="mt-4 ml-14 space-y-3">
                                        <template x-for="reply in comment.replies" :key="reply.id">
                                            <div
                                                class="bg-gradient-to-r from-gray-50 to-blue-50/30 rounded-lg p-4 border-l-4 border-blue-200">
                                                <div class="flex items-start space-x-3">
                                                    <div
                                                        class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center space-x-2 mb-1">
                                                            <span class="font-medium text-gray-900 text-sm"
                                                                x-text="reply.developer.name"></span>
                                                            <span
                                                                class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full"
                                                                x-text="formatDate(reply.created_at)"></span>
                                                        </div>
                                                        <p class="text-gray-700 text-sm leading-relaxed"
                                                            x-text="reply.description"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Enhanced Add Comment Form -->
                    <div class="border-t border-gray-200 p-6 bg-white">
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <textarea x-model="newComment" placeholder="Share your thoughts, ask questions, or provide updates..." rows="3"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"></textarea>
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button @click="submitComment()" :disabled="!newComment.trim() || submittingComment"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-xl hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <span x-show="!submittingComment">Add Comment</span>
                                    <span x-show="submittingComment">Adding...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Developer Assignment Modal -->
        <div x-show="showAssignModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click.self="closeAssignModal()">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl" @click.stop
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100">

                <!-- Modal Header -->
                <div
                    class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Assign Developers</h3>
                            <p class="text-sm text-gray-600" x-show="currentAssignApi">
                                <span class="font-mono bg-white px-2 py-1 rounded text-xs"
                                    x-text="currentAssignApi ? `${currentAssignApi.method} ${currentAssignApi.endpoint}` : ''"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="closeAssignModal()"
                        class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-6">
                    <!-- Developer Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-3">Select Developers</label>
                        <div class="grid grid-cols-2 gap-3 max-h-48 overflow-y-auto">
                            <template x-for="developer in developers" :key="developer.id">
                                <label
                                    class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" :value="developer.id" x-model="selectedDevelopers"
                                        class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900" x-text="developer.name"></div>
                                        <div class="text-xs text-gray-500" x-text="developer.email"></div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Estimated Completion Time -->
                    <div>
                        <label for="estimated_completion" class="block text-sm font-semibold text-gray-900 mb-2">
                            Estimated Completion Time (Optional)
                        </label>
                        <input type="datetime-local" id="estimated_completion" x-model="estimatedCompletionTime"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button @click="closeAssignModal()"
                            class="px-6 py-3 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors duration-200">
                            Cancel
                        </button>
                        <button @click="assignDevelopers()"
                            :disabled="selectedDevelopers.length === 0 || assigningDevelopers"
                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                            <span x-show="!assigningDevelopers">Assign Developers</span>
                            <span x-show="assigningDevelopers">Assigning...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function apiProgressData() {
            return {
                apis: [],
                groupedApis: [],
                filteredApis: [],
                loading: false,
                searchTerm: '',
                statusFilter: '',
                priorityFilter: '',
                developerFilter: '',
                expandedGroups: new Set(),
                overallCompletion: null,
                developers: [],

                // Comments functionality
                showCommentsModal: false,
                currentApi: null,
                comments: [],
                loadingComments: false,
                newComment: '',
                submittingComment: false,
                replyingTo: null,
                replyText: '',
                submittingReply: false,

                // Developer assignment functionality
                showAssignModal: false,
                currentAssignApi: null,
                selectedDevelopers: [],
                estimatedCompletionTime: '',
                assigningDevelopers: false,

                async init() {
                    await this.loadApis();
                    await this.loadDevelopers();
                },
                async loadApis() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('apipt.api.progress') }}');
                        const data = await response.json();

                        if (data.data) {
                            this.apis = data.data;
                            this.filteredApis = [...this.apis];
                            this.overallCompletion = data.completion_percentage || 0;
                            this.groupApis();

                            // Log to console for debugging
                            console.log(`Loaded ${this.apis.length} APIs total`);
                            console.log(`Overall completion: ${this.overallCompletion}%`);
                            console.log(`Groups found: ${data.groups ? data.groups.join(', ') : 'none'}`);
                        }
                    } catch (error) {
                        console.error('Error loading APIs:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async loadDevelopers() {
                    try {
                        const response = await fetch('{{ route('apipt.api.developers') }}');
                        const data = await response.json();

                        if (data.data) {
                            this.developers = data.data;
                        }
                    } catch (error) {
                        console.error('Error loading developers:', error);
                    }
                },

                filterApis() {
                    let filtered = [...this.apis];

                    if (this.searchTerm) {
                        const term = this.searchTerm.toLowerCase();
                        filtered = filtered.filter(api =>
                            api.endpoint.toLowerCase().includes(term) ||
                            api.method.toLowerCase().includes(term) ||
                            (api.description && api.description.toLowerCase().includes(term))
                        );
                    }

                    if (this.statusFilter) {
                        filtered = filtered.filter(api => api.status === this.statusFilter);
                    }

                    if (this.priorityFilter) {
                        filtered = filtered.filter(api => api.priority === this.priorityFilter);
                    }

                    if (this.developerFilter) {
                        filtered = filtered.filter(api =>
                            api.developers && api.developers.some(dev => dev.id == this.developerFilter)
                        );
                    }

                    this.filteredApis = filtered;
                    this.groupApis();
                },

                groupApis() {
                    const groups = {};

                    this.filteredApis.forEach(api => {
                        const groupName = api.group_name || 'Ungrouped';
                        if (!groups[groupName]) {
                            groups[groupName] = [];
                        }
                        groups[groupName].push(api);
                    });

                    this.groupedApis = Object.keys(groups).map(groupName => {
                        const apis = groups[groupName];
                        const completedCount = apis.filter(api => api.status === 'complete').length;
                        const progress = apis.length > 0 ? Math.round((completedCount / apis.length) * 100) : 0;

                        return {
                            name: groupName,
                            apis: apis,
                            progress: progress,
                            expanded: this.expandedGroups.has(groupName) || this.expandedGroups.size === 0
                        };
                    });
                },

                toggleGroup(groupName) {
                    if (this.expandedGroups.has(groupName)) {
                        this.expandedGroups.delete(groupName);
                    } else {
                        this.expandedGroups.add(groupName);
                    }
                    this.groupApis(); // Refresh to update expanded state
                },

                async updateApiStatus(api) {
                    try {
                        const response = await fetch('{{ route('apipt.api.progress.update', ':id') }}'.replace(':id',
                            api.id), {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                method: api.method,
                                endpoint: api.endpoint,
                                group_name: api.group_name,
                                description: api.description,
                                priority: api.priority,
                                estimated_completion_time: api.estimated_completion_time,
                                status: api.status
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.groupApis(); // Refresh progress calculations
                            this.showNotification('API status updated successfully', 'success');
                        } else {
                            this.showNotification('Error updating API status', 'error');
                        }
                    } catch (error) {
                        console.error('Error updating API status:', error);
                        this.showNotification('Error updating API status', 'error');
                    }
                },

                async updateApiPriority(api) {
                    try {
                        const response = await fetch('{{ route('apipt.api.progress.update', ':id') }}'.replace(':id',
                            api.id), {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                method: api.method,
                                endpoint: api.endpoint,
                                group_name: api.group_name,
                                description: api.description,
                                priority: api.priority,
                                estimated_completion_time: api.estimated_completion_time,
                                status: api.status
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.showNotification('API priority updated successfully', 'success');
                        } else {
                            this.showNotification('Error updating API priority', 'error');
                        }
                    } catch (error) {
                        console.error('Error updating API priority:', error);
                        this.showNotification('Error updating API priority', 'error');
                    }
                },

                async syncRoutes() {
                    try {
                        const response = await fetch('{{ route('apipt.api.sync-routes') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            await this.loadApis();
                            this.showNotification('Routes synced successfully', 'success');
                        }
                    } catch (error) {
                        console.error('Error syncing routes:', error);
                        this.showNotification('Error syncing routes', 'error');
                    }
                },

                showComments(api) {
                    this.currentApi = api;
                    this.showCommentsModal = true;
                    this.loadComments();
                },

                closeCommentsModal() {
                    this.showCommentsModal = false;
                    this.currentApi = null;
                    this.comments = [];
                    this.newComment = '';
                    this.replyingTo = null;
                    this.replyText = '';
                },

                async loadComments() {
                    if (!this.currentApi) return;

                    this.loadingComments = true;
                    try {
                        const response = await fetch(
                            `{{ route('apipt.api.comments', ['type' => 'api-progress', 'id' => ':id']) }}`.replace(
                                ':id', this.currentApi.id));
                        const data = await response.json();

                        this.comments = data || [];
                    } catch (error) {
                        console.error('Error loading comments:', error);
                        this.showNotification('Error loading comments', 'error');
                    } finally {
                        this.loadingComments = false;
                    }
                },

                async submitComment() {
                    if (!this.newComment.trim() || !this.currentApi) return;

                    this.submittingComment = true;
                    try {
                        const response = await fetch('{{ route('apipt.api.comments.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                description: this.newComment,
                                commentable_type: 'Gmrakibulhasan\\ApiProgressTracker\\Models\\ApiptApiProgress',
                                commentable_id: this.currentApi.id,
                                developer_id: {{ session('apipt_user_id') ?: 'null' }},
                                parent_id: null,
                                attachments: [],
                                mentions: []
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.newComment = '';
                            await this.loadComments(); // Reload comments
                            this.showNotification('Comment added successfully', 'success');
                        } else {
                            this.showNotification('Error adding comment', 'error');
                        }
                    } catch (error) {
                        console.error('Error submitting comment:', error);
                        this.showNotification('Error adding comment', 'error');
                    } finally {
                        this.submittingComment = false;
                    }
                },

                showReplyForm(comment) {
                    this.replyingTo = comment.id;
                    this.replyText = '';
                },

                cancelReply() {
                    this.replyingTo = null;
                    this.replyText = '';
                },

                async submitReply(parentId) {
                    if (!this.replyText.trim() || !this.currentApi) return;

                    this.submittingReply = true;
                    try {
                        const response = await fetch('{{ route('apipt.api.comments.store') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                description: this.replyText,
                                commentable_type: 'Gmrakibulhasan\\ApiProgressTracker\\Models\\ApiptApiProgress',
                                commentable_id: this.currentApi.id,
                                developer_id: {{ session('apipt_user_id') ?: 'null' }},
                                parent_id: parentId,
                                attachments: [],
                                mentions: []
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.cancelReply();
                            await this.loadComments(); // Reload comments
                            this.showNotification('Reply added successfully', 'success');
                        } else {
                            this.showNotification('Error adding reply', 'error');
                        }
                    } catch (error) {
                        console.error('Error submitting reply:', error);
                        this.showNotification('Error adding reply', 'error');
                    } finally {
                        this.submittingReply = false;
                    }
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diffMs = now - date;
                    const diffMins = Math.floor(diffMs / 60000);
                    const diffHours = Math.floor(diffMs / 3600000);
                    const diffDays = Math.floor(diffMs / 86400000);

                    if (diffMins < 1) return 'Just now';
                    if (diffMins < 60) return `${diffMins} min ago`;
                    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
                    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;

                    return date.toLocaleDateString();
                },

                editApi(api) {
                    // TODO: Implement edit API functionality
                    this.showNotification('Edit API functionality coming soon', 'info');
                },

                async deleteApi(api) {
                    if (!confirm(`Are you sure you want to delete ${api.method} ${api.endpoint}?`)) {
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('apipt.api.progress.delete', ':id') }}'.replace(':id',
                            api.id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            await this.loadApis();
                            this.showNotification('API deleted successfully', 'success');
                        } else {
                            this.showNotification(data.message || 'Error deleting API', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting API:', error);
                        this.showNotification('Error deleting API', 'error');
                    }
                },

                // Developer Assignment Methods
                showAssignDevelopers(api) {
                    this.currentAssignApi = api;
                    this.selectedDevelopers = api.developers ? api.developers.map(dev => dev.id) : [];
                    this.estimatedCompletionTime = api.estimated_completion_time ?
                        new Date(api.estimated_completion_time).toISOString().slice(0, 16) : '';
                    this.showAssignModal = true;
                },

                closeAssignModal() {
                    this.showAssignModal = false;
                    this.currentAssignApi = null;
                    this.selectedDevelopers = [];
                    this.estimatedCompletionTime = '';
                },

                // Helper function to format datetime for backend
                formatDateTimeForBackend(dateTimeString) {
                    if (!dateTimeString) return null;

                    // Convert from datetime-local format (YYYY-MM-DDTHH:mm) to Y-m-d H:i:s format
                    const date = new Date(dateTimeString);
                    if (isNaN(date.getTime())) return null;

                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    const seconds = String(date.getSeconds()).padStart(2, '0');

                    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                },

                async assignDevelopers() {
                    if (this.selectedDevelopers.length === 0 || !this.currentAssignApi) {
                        this.showNotification('Please select at least one developer', 'error');
                        return;
                    }

                    this.assigningDevelopers = true;
                    try {
                        // Format the estimated completion time properly
                        const formattedDateTime = this.formatDateTimeForBackend(this.estimatedCompletionTime);

                        const response = await fetch(`{{ route('apipt.api.progress.assign-developers', ':id') }}`
                            .replace(':id', this.currentAssignApi.id), {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    developer_ids: this.selectedDevelopers,
                                    assigned_by: {{ session('apipt_user_id') ?: 'null' }},
                                    estimated_completion_time: formattedDateTime
                                })
                            });

                        const data = await response.json();

                        if (data.success) {
                            await this.loadApis(); // Reload APIs to show updated assignments
                            this.closeAssignModal();
                            this.showNotification('Developers assigned successfully', 'success');
                        } else {
                            // Handle validation errors
                            if (data.errors && data.errors.estimated_completion_time) {
                                this.showNotification(`Validation Error: ${data.errors.estimated_completion_time[0]}`,
                                    'error');
                            } else {
                                this.showNotification(data.message || 'Error assigning developers', 'error');
                            }
                        }
                    } catch (error) {
                        console.error('Error assigning developers:', error);
                        this.showNotification('Error assigning developers', 'error');
                    } finally {
                        this.assigningDevelopers = false;
                    }
                },

                async unassignDeveloper(api, developerId) {
                    if (!confirm('Are you sure you want to unassign this developer?')) {
                        return;
                    }

                    try {
                        const response = await fetch(
                            `{{ route('apipt.api.progress.unassign-developer', ['id' => ':id', 'developerId' => ':devId']) }}`
                            .replace(':id', api.id).replace(':devId', developerId), {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json'
                                }
                            });

                        const data = await response.json();

                        if (data.success) {
                            await this.loadApis();
                            this.showNotification('Developer unassigned successfully', 'success');
                        } else {
                            this.showNotification('Error unassigning developer', 'error');
                        }
                    } catch (error) {
                        console.error('Error unassigning developer:', error);
                        this.showNotification('Error unassigning developer', 'error');
                    }
                },

                showNotification(message, type = 'info') {
                    if (window.mainAppInstance) {
                        window.mainAppInstance.showNotification(message, type);
                    }
                }
            }
        }
    </script>
@endpush
