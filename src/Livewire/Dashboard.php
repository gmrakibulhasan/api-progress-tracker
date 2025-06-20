<?php

namespace Gmrakibulhasan\ApiProgressTracker\Livewire;

use Livewire\Component;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptTask;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptComment;

class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $this->stats = [
            'developers' => [
                'total' => ApiptDeveloper::count(),
                'active' => ApiptDeveloper::whereHas('apiProgresses', function ($q) {
                    $q->where('status', '!=', 'complete');
                })->count(),
            ],
            'api_progress' => [
                'total' => ApiptApiProgress::count(),
                'by_status' => ApiptApiProgress::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'by_priority' => ApiptApiProgress::selectRaw('priority, COUNT(*) as count')
                    ->groupBy('priority')
                    ->pluck('count', 'priority')
                    ->toArray(),
                'by_group' => ApiptApiProgress::selectRaw('group_name, COUNT(*) as count')
                    ->whereNotNull('group_name')
                    ->groupBy('group_name')
                    ->pluck('count', 'group_name')
                    ->toArray(),
            ],
            'tasks' => [
                'total' => ApiptTask::count(),
                'by_status' => ApiptTask::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray(),
                'by_priority' => ApiptTask::selectRaw('priority, COUNT(*) as count')
                    ->groupBy('priority')
                    ->pluck('count', 'priority')
                    ->toArray(),
            ],
            'comments' => [
                'total' => ApiptComment::count(),
                'recent' => ApiptComment::with('developer')
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->toArray(),
            ],
        ];
    }

    public function refreshStats()
    {
        $this->loadStatistics();
        $this->dispatch('stats-updated', $this->stats);
    }

    public function render()
    {
        return view('api-progress-tracker::livewire.dashboard');
    }
}
