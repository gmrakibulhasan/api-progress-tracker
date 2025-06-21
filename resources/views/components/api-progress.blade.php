<!-- API Progress Management Component -->
<div x-data="apiProgressData()" x-init="init()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">API Progress</h2>
            <p class="text-gray-600">Track and manage API development progress</p>
        </div>
        <div class="flex space-x-3">
            <button @click="syncRoutes()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-sync mr-2"></i>
                Sync Routes
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <input type="text" x-model="searchTerm" @input="filterApis()" placeholder="Search APIs..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <select x-model="statusFilter" @change="filterApis()" class="px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">All Statuses</option>
                <option value="todo">Todo</option>
                <option value="in_progress">In Progress</option>
                <option value="issue">Issue</option>
                <option value="not_needed">Not Needed</option>
                <option value="complete">Complete</option>
            </select>
            <select x-model="priorityFilter" @change="filterApis()" class="px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">All Priorities</option>
                <option value="low">Low</option>
                <option value="normal">Normal</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
            </select>
        </div>
    </div>

    <!-- API Groups -->
    <div class="space-y-4" x-show="!loading">
        <template x-for="group in groupedApis" :key="group.name">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <!-- Group Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 cursor-pointer"
                    @click="toggleGroup(group.name)">
                    <div class="flex items-center">
                        <i class="fas" :class="group.expanded ? 'fa-chevron-down' : 'fa-chevron-right'"
                            class="text-gray-400 mr-3"></i>
                        <h3 class="text-lg font-medium text-gray-900" x-text="group.name"></h3>
                        <span class="ml-3 text-sm text-gray-500" x-text="`${group.apis.length} APIs`"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" :style="`width: ${group.progress}%`"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700" x-text="`${group.progress}%`"></span>
                    </div>
                </div>

                <!-- Group Content -->
                <div x-show="group.expanded" class="p-4 space-y-3">
                    <template x-for="api in group.apis" :key="api.id">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full"
                                        x-text="api.method"></span>
                                    <span class="font-medium text-gray-900" x-text="api.endpoint"></span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1" x-text="api.description || 'No description'"></p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <!-- Status Dropdown -->
                                <select x-model="api.status" @change="updateApiStatus(api)"
                                    class="px-2 py-1 text-xs border border-gray-300 rounded">
                                    <option value="todo">Todo</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="issue">Issue</option>
                                    <option value="not_needed">Not Needed</option>
                                    <option value="complete">Complete</option>
                                </select>

                                <!-- Priority Dropdown -->
                                <select x-model="api.priority" @change="updateApiPriority(api)"
                                    class="px-2 py-1 text-xs border border-gray-300 rounded">
                                    <option value="low">Low</option>
                                    <option value="normal">Normal</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>

                                <!-- Actions -->
                                <button @click="showComments(api)" class="text-blue-600 hover:text-blue-800 p-1">
                                    <i class="fas fa-comments"></i>
                                </button>
                                <button @click="editApi(api)" class="text-green-600 hover:text-green-800 p-1">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button @click="deleteApi(api)" class="text-red-600 hover:text-red-800 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && apis.length === 0" class="text-center py-12">
        <i class="fas fa-code-branch text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No APIs found</h3>
        <p class="text-gray-600 mb-4">Sync your routes to get started</p>
        <button @click="syncRoutes()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            Sync Routes
        </button>
    </div>

    <!-- Comments Modal -->
    <div x-show="showCommentsModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        @click.self="closeCommentsModal()">

        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.stop>

            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Comments</h3>
                    <p class="text-sm text-gray-600" x-show="currentApi"
                        x-text="currentApi ? `${currentApi.method} ${currentApi.endpoint}` : ''"></p>
                </div>
                <button @click="closeCommentsModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Comments Content -->
            <div class="flex flex-col h-[70vh]">
                <!-- Comments List -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    <div x-show="loadingComments" class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    </div>

                    <div x-show="!loadingComments && comments.length === 0" class="text-center py-8 text-gray-500">
                        <i class="fas fa-comments text-4xl mb-2"></i>
                        <p>No comments yet. Start the conversation!</p>
                    </div>

                    <!-- Comments Tree -->
                    <template x-for="comment in comments" :key="comment.id">
                        <div class="space-y-3">
                            <!-- Parent Comment -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-start space-x-3">
                                    <div
                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="font-medium text-gray-900"
                                                x-text="comment.developer.name"></span>
                                            <span class="text-xs text-gray-500"
                                                x-text="formatDate(comment.created_at)"></span>
                                        </div>
                                        <p class="text-gray-700 text-sm leading-relaxed" x-text="comment.description">
                                        </p>
                                        <button @click="showReplyForm(comment)"
                                            class="mt-2 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            Reply
                                        </button>
                                    </div>
                                </div>

                                <!-- Reply Form -->
                                <div x-show="replyingTo === comment.id" x-transition class="mt-4 ml-11">
                                    <textarea x-model="replyText" placeholder="Write a reply..." rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <button @click="cancelReply()"
                                            class="px-3 py-1 text-xs text-gray-600 hover:text-gray-800">
                                            Cancel
                                        </button>
                                        <button @click="submitReply(comment.id)"
                                            :disabled="!replyText.trim() || submittingReply"
                                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50">
                                            <span x-show="!submittingReply">Reply</span>
                                            <span x-show="submittingReply">Replying...</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Replies -->
                                <div x-show="comment.replies && comment.replies.length > 0"
                                    class="mt-4 ml-11 space-y-3">
                                    <template x-for="reply in comment.replies" :key="reply.id">
                                        <div class="bg-white rounded-lg p-3 border border-gray-200">
                                            <div class="flex items-start space-x-3">
                                                <div
                                                    class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-user text-green-600 text-xs"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <span class="font-medium text-gray-900 text-sm"
                                                            x-text="reply.developer.name"></span>
                                                        <span class="text-xs text-gray-500"
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

                <!-- Add Comment Form -->
                <div class="border-t border-gray-200 p-6 bg-gray-50">
                    <div class="space-y-3">
                        <textarea x-model="newComment" placeholder="Write a comment..." rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <div class="flex justify-end">
                            <button @click="submitComment()" :disabled="!newComment.trim() || submittingComment"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                                <span x-show="!submittingComment">Add Comment</span>
                                <span x-show="submittingComment">Adding...</span>
                            </button>
                        </div>
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
                expandedGroups: new Set(),

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

                async init() {
                    await this.loadApis();
                },

                async loadApis() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('apipt.api.progress') }}');
                        const data = await response.json();

                        if (data.data) {
                            this.apis = data.data;
                            this.filteredApis = [...this.apis];
                            this.groupApis();
                        }
                    } catch (error) {
                        console.error('Error loading APIs:', error);
                    } finally {
                        this.loading = false;
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

                showNotification(message, type = 'info') {
                    if (window.mainAppInstance) {
                        window.mainAppInstance.showNotification(message, type);
                    }
                }
            }
        }
    </script>
@endpush
