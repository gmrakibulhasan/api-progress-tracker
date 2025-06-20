<!-- Sidebar Navigation -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out" 
     x-data="{ open: false }" :class="{ 'translate-x-0': open }">
    
    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button @click="open = !open" class="p-2 bg-white rounded-lg shadow-md">
            <i class="fas fa-bars text-gray-600"></i>
        </button>
    </div>

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-6 bg-blue-600 text-white">
        <div class="flex items-center">
            <i class="fas fa-code text-2xl mr-3"></i>
            <span class="font-bold text-lg">API Tracker</span>
        </div>
        <div class="lg:hidden">
            <button @click="open = false" class="text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- User Info -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center">
            <div class="bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-user text-blue-600"></i>
            </div>
            <div>
                <div class="font-medium text-gray-800">{{ session('apipt_user_name') }}</div>
                <div class="text-sm text-gray-600">{{ session('apipt_user_email') }}</div>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="mt-6">
        <div class="px-3">
            <a href="{{ route('apipt.dashboard') }}?tab=dashboard" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors group mb-1"
               :class="{ 'bg-blue-50 text-blue-600': '{{ request()->get('tab', 'dashboard') }}' === 'dashboard' }">
                <i class="fas fa-chart-pie mr-3 text-lg"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('apipt.dashboard') }}?tab=developers" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors group mb-1"
               :class="{ 'bg-blue-50 text-blue-600': '{{ request()->get('tab') }}' === 'developers' }">
                <i class="fas fa-users mr-3 text-lg"></i>
                <span class="font-medium">Developers</span>
            </a>

            <a href="{{ route('apipt.dashboard') }}?tab=api-progress" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors group mb-1"
               :class="{ 'bg-blue-50 text-blue-600': '{{ request()->get('tab') }}' === 'api-progress' }">
                <i class="fas fa-code-branch mr-3 text-lg"></i>
                <span class="font-medium">API Progress</span>
            </a>

            <a href="{{ route('apipt.dashboard') }}?tab=tasks" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors group mb-1"
               :class="{ 'bg-blue-50 text-blue-600': '{{ request()->get('tab') }}' === 'tasks' }">
                <i class="fas fa-tasks mr-3 text-lg"></i>
                <span class="font-medium">Tasks</span>
            </a>
        </div>

        <!-- Divider -->
        <div class="mx-3 my-6 border-t border-gray-200"></div>

        <!-- Actions -->
        <div class="px-3">
            <button onclick="syncRoutes()" 
                    class="flex items-center w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-green-50 hover:text-green-600 transition-colors group mb-1">
                <i class="fas fa-sync mr-3 text-lg"></i>
                <span class="font-medium">Sync Routes</span>
            </button>

            <form method="POST" action="{{ route('apipt.logout') }}" class="w-full">
                @csrf
                <button type="submit" 
                        class="flex items-center w-full px-4 py-3 text-gray-700 rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors group">
                    <i class="fas fa-sign-out-alt mr-3 text-lg"></i>
                    <span class="font-medium">Logout</span>
                </button>
            </form>
        </div>
    </nav>
</div>

<!-- Mobile Overlay -->
<div x-show="open" @click="open = false" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>
