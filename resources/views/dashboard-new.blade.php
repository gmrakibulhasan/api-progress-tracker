@extends('api-progress-tracker::layouts.app')

@section('content')
    <div class="p-6" x-data="mainApp()">
        @php
            $currentTab = request()->get('tab', 'dashboard');
        @endphp

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">API Progress Tracker</h1>
            <p class="text-gray-600 mt-2">Monitor and manage your API development progress</p>
        </div>

        <!-- Tab Content -->
        @if ($currentTab === 'dashboard')
            @include('api-progress-tracker::components.dashboard')
        @elseif($currentTab === 'developers')
            @include('api-progress-tracker::components.developers')
        @elseif($currentTab === 'api-progress')
            @include('api-progress-tracker::components.api-progress')
        @elseif($currentTab === 'tasks')
            @include('api-progress-tracker::components.tasks')
        @endif
    </div>

    @push('scripts')
        <script>
            function mainApp() {
                return {
                    currentTab: '{{ $currentTab }}',
                    loading: false,

                    // Sync Routes
                    syncRoutes() {
                        this.loading = true;
                        fetch('{{ route('apipt.api.sync-routes') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.showNotification('Routes synced successfully!', 'success');
                                    // Refresh the page to show updated data
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    this.showNotification(data.message || 'Error syncing routes', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                this.showNotification('Error syncing routes', 'error');
                            })
                            .finally(() => {
                                this.loading = false;
                            });
                    },

                    // Notification system
                    showNotification(message, type = 'info') {
                        const notification = document.createElement('div');
                        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                    type === 'success' ? 'bg-green-500 text-white' : 
                    type === 'error' ? 'bg-red-500 text-white' : 
                    'bg-blue-500 text-white'
                }`;
                        notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'} mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;
                        document.body.appendChild(notification);

                        setTimeout(() => {
                            notification.remove();
                        }, 5000);
                    }
                }
            }

            // Global sync routes function for navigation
            function syncRoutes() {
                if (window.mainAppInstance) {
                    window.mainAppInstance.syncRoutes();
                }
            }

            // Store main app instance globally
            document.addEventListener('alpine:init', () => {
                Alpine.data('mainApp', () => {
                    const instance = {
                        currentTab: '{{ $currentTab }}',
                        loading: false,

                        syncRoutes() {
                            this.loading = true;
                            fetch('{{ route('apipt.api.sync-routes') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        this.showNotification('Routes synced successfully!', 'success');
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1000);
                                    } else {
                                        this.showNotification(data.message || 'Error syncing routes',
                                            'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    this.showNotification('Error syncing routes', 'error');
                                })
                                .finally(() => {
                                    this.loading = false;
                                });
                        },

                        showNotification(message, type = 'info') {
                            const notification = document.createElement('div');
                            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                        type === 'success' ? 'bg-green-500 text-white' : 
                        type === 'error' ? 'bg-red-500 text-white' : 
                        'bg-blue-500 text-white'
                    }`;
                            notification.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'} mr-2"></i>
                            <span>${message}</span>
                        </div>
                    `;
                            document.body.appendChild(notification);

                            setTimeout(() => {
                                notification.remove();
                            }, 5000);
                        }
                    };

                    window.mainAppInstance = instance;
                    return instance;
                });
            });
        </script>
    @endpush
@endsection
