<!-- Tasks Management Component -->
<div x-data="tasksData()" x-init="init()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Tasks</h2>
            <p class="text-gray-600">Manage development tasks and assignments</p>
        </div>
        <button @click="openAddModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add Task
        </button>
    </div>

    <!-- Task Board -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- TODO Column -->
        <div class="bg-gray-100 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">To Do</h3>
                <span class="bg-gray-600 text-white text-xs px-2 py-1 rounded-full"
                    x-text="tasksByStatus.todo.length"></span>
            </div>
            <div class="space-y-3">
                <template x-for="task in tasksByStatus.todo" :key="task.id">
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-gray-400">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-medium text-gray-900" x-text="task.title"></h4>
                            <div class="flex items-center space-x-1">
                                <button @click="editTask(task)" class="text-blue-600 hover:text-blue-800 p-1">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button @click="deleteTask(task)" class="text-red-600 hover:text-red-800 p-1">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-3" x-text="task.description"></p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2 py-1 rounded-full"
                                :class="{
                                    'bg-green-100 text-green-800': task.priority === 'low',
                                    'bg-yellow-100 text-yellow-800': task.priority === 'medium',
                                    'bg-orange-100 text-orange-800': task.priority === 'high',
                                    'bg-red-100 text-red-800': task.priority === 'urgent'
                                }"
                                x-text="task.priority"></span>
                            <button @click="showComments(task)" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-comments"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- IN PROGRESS Column -->
        <div class="bg-blue-100 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">In Progress</h3>
                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full"
                    x-text="tasksByStatus.in_progress.length"></span>
            </div>
            <div class="space-y-3">
                <template x-for="task in tasksByStatus.in_progress" :key="task.id">
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-400">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-medium text-gray-900" x-text="task.title"></h4>
                            <div class="flex items-center space-x-1">
                                <button @click="editTask(task)" class="text-blue-600 hover:text-blue-800 p-1">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button @click="deleteTask(task)" class="text-red-600 hover:text-red-800 p-1">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-3" x-text="task.description"></p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2 py-1 rounded-full"
                                :class="{
                                    'bg-green-100 text-green-800': task.priority === 'low',
                                    'bg-yellow-100 text-yellow-800': task.priority === 'medium',
                                    'bg-orange-100 text-orange-800': task.priority === 'high',
                                    'bg-red-100 text-red-800': task.priority === 'urgent'
                                }"
                                x-text="task.priority"></span>
                            <button @click="showComments(task)" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-comments"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- COMPLETED Column -->
        <div class="bg-green-100 rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Completed</h3>
                <span class="bg-green-600 text-white text-xs px-2 py-1 rounded-full"
                    x-text="tasksByStatus.complete.length"></span>
            </div>
            <div class="space-y-3">
                <template x-for="task in tasksByStatus.complete" :key="task.id">
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-400 opacity-75">
                        <div class="flex items-start justify-between mb-2">
                            <h4 class="font-medium text-gray-900" x-text="task.title"></h4>
                            <div class="flex items-center space-x-1">
                                <button @click="editTask(task)" class="text-blue-600 hover:text-blue-800 p-1">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <button @click="deleteTask(task)" class="text-red-600 hover:text-red-800 p-1">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-3" x-text="task.description"></p>
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2 py-1 rounded-full"
                                :class="{
                                    'bg-green-100 text-green-800': task.priority === 'low',
                                    'bg-yellow-100 text-yellow-800': task.priority === 'medium',
                                    'bg-orange-100 text-orange-800': task.priority === 'high',
                                    'bg-red-100 text-red-800': task.priority === 'urgent'
                                }"
                                x-text="task.priority"></span>
                            <button @click="showComments(task)" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-comments"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && tasks.length === 0" class="text-center py-12">
        <i class="fas fa-tasks text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks found</h3>
        <p class="text-gray-600 mb-4">Create your first task to get started</p>
        <button @click="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Add Task
        </button>
    </div>
</div>

@push('scripts')
    <script>
        function tasksData() {
            return {
                tasks: [],
                tasksByStatus: {
                    todo: [],
                    in_progress: [],
                    complete: []
                },
                loading: false,

                async init() {
                    await this.loadTasks();
                },

                async loadTasks() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('apipt.api.tasks') }}');
                        const data = await response.json();

                        if (data.data) {
                            this.tasks = data.data;
                            this.organizeTasks();
                        }
                    } catch (error) {
                        console.error('Error loading tasks:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                organizeTasks() {
                    this.tasksByStatus = {
                        todo: this.tasks.filter(task => task.status === 'todo'),
                        in_progress: this.tasks.filter(task => task.status === 'in_progress'),
                        complete: this.tasks.filter(task => task.status === 'complete')
                    };
                },

                openAddModal() {
                    // This would open a modal for adding tasks
                    alert('Add task modal - to be implemented');
                },

                editTask(task) {
                    // This would open a modal for editing tasks
                    alert(`Edit task: ${task.title} - to be implemented`);
                },

                async deleteTask(task) {
                    if (!confirm(`Are you sure you want to delete "${task.title}"?`)) {
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route('apipt.api.tasks.store') }}`.replace('/tasks',
                            `/tasks/${task.id}`), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();
                        if (data.success) {
                            await this.loadTasks();
                            this.showNotification('Task deleted successfully', 'success');
                        }
                    } catch (error) {
                        console.error('Error deleting task:', error);
                        this.showNotification('Error deleting task', 'error');
                    }
                },

                showComments(task) {
                    // This would show comments for the task
                    alert(`Show comments for: ${task.title} - to be implemented`);
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
