<!-- Developers Management Component -->
<div x-data="developersData()" x-init="init()">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Developers</h2>
            <p class="text-gray-600">Manage team members and their access</p>
        </div>
        <button @click="openAddModal()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add Developer
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" x-model="searchTerm" @input="searchDevelopers()"
                        placeholder="Search developers..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600" x-text="`${developers.length} developer(s)`"></span>
                <button @click="loadDevelopers()" class="text-blue-600 hover:text-blue-800 flex items-center text-sm">
                    <i class="fas fa-refresh mr-1"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Developers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-show="!loading">
        <template x-for="developer in filteredDevelopers" :key="developer.id">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-lg"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900" x-text="developer.name"></h3>
                            <p class="text-sm text-gray-600" x-text="developer.email"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="editDeveloper(developer)" class="text-blue-600 hover:text-blue-800 p-1">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="deleteDeveloper(developer)" class="text-red-600 hover:text-red-800 p-1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Joined:</span>
                        <span class="text-gray-900" x-text="formatDate(developer.created_at)"></span>
                    </div>
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-600">Tasks:</span>
                        <span class="text-gray-900" x-text="developer.tasks_count || 0"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <!-- Empty State -->
    <div x-show="!loading && developers.length === 0" class="text-center py-12">
        <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No developers found</h3>
        <p class="text-gray-600 mb-4">Get started by adding your first team member</p>
        <button @click="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Add Developer
        </button>
    </div>

    <!-- Add/Edit Developer Modal -->
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        @click.self="closeModal()">

        <div class="bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop>

            <!-- Modal Content -->
            <div class="p-6">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900"
                        x-text="editingDeveloper ? 'Edit Developer' : 'Add Developer'"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form @submit.prevent="saveDeveloper()" class="space-y-4">
                    <div>
                        <label for="dev-name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" id="dev-name" x-model="form.name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="dev-email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="dev-email" x-model="form.email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="dev-password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span x-show="editingDeveloper" class="text-gray-500">(leave empty to keep
                                current)</span>
                        </label>
                        <input type="password" id="dev-password" x-model="form.password"
                            :required="!editingDeveloper"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Error Messages -->
                    <div x-show="errors.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <template x-for="error in errors" :key="error">
                            <p class="text-red-700 text-sm" x-text="error"></p>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-3 pt-4 mt-6 border-t">
                        <button type="button" @click="closeModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" :disabled="saving"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50 transition-colors">
                            <span x-show="!saving" x-text="editingDeveloper ? 'Update' : 'Create'"></span>
                            <span x-show="saving">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        function developersData() {
            console.log('developersData function called');
            return {
                developers: [],
                filteredDevelopers: [],
                loading: false,
                searchTerm: '',
                showModal: false,
                editingDeveloper: null,
                saving: false,
                errors: [],
                form: {
                    name: '',
                    email: '',
                    password: ''
                },

                async init() {
                    console.log('init() called');
                    await this.loadDevelopers();
                },

                async loadDevelopers() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route('apipt.api.developers') }}');
                        const data = await response.json();

                        if (data.data) {
                            this.developers = data.data;
                            this.filteredDevelopers = [...this.developers];
                        }
                    } catch (error) {
                        console.error('Error loading developers:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                searchDevelopers() {
                    if (this.searchTerm.trim() === '') {
                        this.filteredDevelopers = [...this.developers];
                    } else {
                        const term = this.searchTerm.toLowerCase();
                        this.filteredDevelopers = this.developers.filter(dev =>
                            dev.name.toLowerCase().includes(term) ||
                            dev.email.toLowerCase().includes(term)
                        );
                    }
                },

                openAddModal() {
                    console.log('openAddModal() called');
                    this.editingDeveloper = null;
                    this.form = {
                        name: '',
                        email: '',
                        password: ''
                    };
                    this.errors = [];
                    this.showModal = true;
                    console.log('showModal set to:', this.showModal);
                },

                editDeveloper(developer) {
                    this.editingDeveloper = developer;
                    this.form = {
                        name: developer.name,
                        email: developer.email,
                        password: ''
                    };
                    this.errors = [];
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                    this.editingDeveloper = null;
                    this.form = {
                        name: '',
                        email: '',
                        password: ''
                    };
                    this.errors = [];
                },

                async saveDeveloper() {
                    this.saving = true;
                    this.errors = [];

                    try {
                        const url = this.editingDeveloper ?
                            '{{ route('apipt.api.developers.update', ':id') }}'.replace(':id', this.editingDeveloper
                                .id) :
                            '{{ route('apipt.api.developers.store') }}';

                        const method = this.editingDeveloper ? 'PUT' : 'POST';

                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.closeModal();
                            await this.loadDevelopers();
                            this.showNotification(
                                this.editingDeveloper ? 'Developer updated successfully' :
                                'Developer created successfully',
                                'success'
                            );
                        } else {
                            if (data.errors) {
                                this.errors = Object.values(data.errors).flat();
                            } else {
                                this.errors = [data.message || 'An error occurred'];
                            }
                        }
                    } catch (error) {
                        console.error('Error saving developer:', error);
                        this.errors = ['An error occurred while saving'];
                    } finally {
                        this.saving = false;
                    }
                },

                async deleteDeveloper(developer) {
                    if (!confirm(`Are you sure you want to delete ${developer.name}?`)) {
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('apipt.api.developers.delete', ':id') }}'.replace(':id',
                            developer.id), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            await this.loadDevelopers();
                            this.showNotification('Developer deleted successfully', 'success');
                        } else {
                            this.showNotification(data.message || 'Error deleting developer', 'error');
                        }
                    } catch (error) {
                        console.error('Error deleting developer:', error);
                        this.showNotification('Error deleting developer', 'error');
                    }
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString();
                },

                showNotification(message, type = 'info') {
                    // Use the global notification system
                    if (window.mainAppInstance) {
                        window.mainAppInstance.showNotification(message, type);
                    }
                }
            }
        }
    </script>
@endpush
