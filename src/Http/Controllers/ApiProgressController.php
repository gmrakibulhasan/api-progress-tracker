<?php

namespace Gmrakibulhasan\ApiProgressTracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptDeveloper;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptApiProgress;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptTask;
use Gmrakibulhasan\ApiProgressTracker\Models\ApiptComment;

class ApiProgressController
{
    public function index(): View
    {
        return view('api-progress-tracker::dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['apipt_user_id', 'apipt_user_name']);
        return redirect()->route('apipt.dashboard')->with('message', 'Logged out successfully');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $developer = ApiptDeveloper::where('email', $request->email)->first();

        if ($developer && Hash::check($request->password, $developer->password)) {
            $request->session()->put([
                'apipt_user_id' => $developer->id,
                'apipt_user_name' => $developer->name,
                'apipt_user_email' => $developer->email,
            ]);

            return redirect()->route('apipt.dashboard');
        }

        return back()->withErrors([
            'credentials' => 'Invalid email or password.'
        ])->withInput($request->only('email'));
    }

    // Developer Management
    public function getDevelopers(Request $request): JsonResponse
    {
        $query = ApiptDeveloper::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $developers = $query->latest()->paginate(15);

        return response()->json($developers);
    }

    public function storeDeveloper(Request $request): JsonResponse
    {
        // Debug: Check which database connection is being used
        Log::info('ApiptDeveloper connection: ' . (new ApiptDeveloper())->getConnectionName());
        Log::info('Database connections available: ' . json_encode(array_keys(config('database.connections'))));

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:apipt.apipt_developers,email',
            'password' => 'required|string|min:6',
        ]);

        $developer = ApiptDeveloper::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Developer created successfully',
            'data' => $developer
        ], 201);
    }

    public function updateDeveloper(Request $request, $id): JsonResponse
    {
        $developer = ApiptDeveloper::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:apipt.apipt_developers,email,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $developer->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Developer updated successfully',
            'data' => $developer
        ]);
    }

    public function deleteDeveloper($id): JsonResponse
    {
        $developer = ApiptDeveloper::findOrFail($id);
        $developer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Developer deleted successfully'
        ]);
    }

    // API Progress Management
    public function getApiProgress(Request $request): JsonResponse
    {
        $query = ApiptApiProgress::withCount('comments')
            ->with(['developers', 'assignedBy']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->has('group')) {
            $query->where('group_name', $request->get('group'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('endpoint', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('group_name', 'like', "%{$search}%");
            });
        }

        // Get all APIs sorted by creation date (oldest first)
        $apiProgress = $query->oldest()->get();

        // Calculate overall completion percentage
        $totalApis = $apiProgress->count();
        $completedApis = $apiProgress->where('status', 'complete')->count();
        $overallCompletion = $totalApis > 0 ? round(($completedApis / $totalApis) * 100, 1) : 0;

        // Group by group_name for better organization
        $groupedApis = $apiProgress->groupBy('group_name');

        return response()->json([
            'data' => $apiProgress,
            'grouped' => $groupedApis,
            'total' => $totalApis,
            'completed' => $completedApis,
            'completion_percentage' => $overallCompletion,
            'groups' => $groupedApis->keys()
        ]);
    }

    public function storeApiProgress(Request $request): JsonResponse
    {
        $request->validate([
            'method' => 'required|string',
            'endpoint' => 'required|string',
            'group_name' => 'nullable|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_completion_time' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,issue,not_needed,complete',
        ]);

        $apiProgress = ApiptApiProgress::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'API Progress created successfully',
            'data' => $apiProgress
        ], 201);
    }

    public function updateApiProgress(Request $request, $id): JsonResponse
    {
        $apiProgress = ApiptApiProgress::findOrFail($id);

        $request->validate([
            'method' => 'required|string',
            'endpoint' => 'required|string',
            'group_name' => 'nullable|string',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_completion_time' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,issue,not_needed,complete',
        ]);

        $apiProgress->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'API Progress updated successfully',
            'data' => $apiProgress
        ]);
    }

    public function deleteApiProgress($id): JsonResponse
    {
        $apiProgress = ApiptApiProgress::findOrFail($id);
        $apiProgress->delete();

        return response()->json([
            'success' => true,
            'message' => 'API Progress deleted successfully'
        ]);
    }

    // Developer Assignment for API Progress
    public function assignDevelopers(Request $request, $id): JsonResponse
    {
        $apiProgress = ApiptApiProgress::findOrFail($id);

        $request->validate([
            'developer_ids' => 'required|array',
            'developer_ids.*' => 'exists:apipt.apipt_developers,id',
            'assigned_by' => 'required|exists:apipt.apipt_developers,id',
            'estimated_completion_time' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        // Clear existing assignments
        $apiProgress->developers()->detach();

        // Assign new developers
        $syncData = [];
        foreach ($request->developer_ids as $developerId) {
            $syncData[$developerId] = [
                'assigned_by' => $request->assigned_by,
                'viewed_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $apiProgress->developers()->sync($syncData);

        // Update estimated completion time if provided
        if ($request->filled('estimated_completion_time')) {
            $apiProgress->update([
                'estimated_completion_time' => $request->estimated_completion_time
            ]);
        }

        // Load the updated API with developers
        $apiProgress->load(['developers', 'assignedBy']);

        return response()->json([
            'success' => true,
            'message' => 'Developers assigned successfully',
            'data' => $apiProgress
        ]);
    }

    public function unassignDeveloper(Request $request, $id, $developerId): JsonResponse
    {
        $apiProgress = ApiptApiProgress::findOrFail($id);

        $apiProgress->developers()->detach($developerId);

        return response()->json([
            'success' => true,
            'message' => 'Developer unassigned successfully'
        ]);
    }

    public function updateAssignment(Request $request, $id, $developerId): JsonResponse
    {
        $apiProgress = ApiptApiProgress::findOrFail($id);

        $request->validate([
            'estimated_completion_time' => 'nullable|date_format:Y-m-d H:i:s',
            'viewed_at' => 'nullable|date_format:Y-m-d H:i:s'
        ]);

        $updateData = [];
        if ($request->filled('estimated_completion_time')) {
            $updateData['estimated_completion_time'] = $request->estimated_completion_time;
            // Also update the main API record
            $apiProgress->update(['estimated_completion_time' => $request->estimated_completion_time]);
        }

        if ($request->filled('viewed_at')) {
            $updateData['viewed_at'] = $request->viewed_at;
        }

        if (!empty($updateData)) {
            $apiProgress->developers()->updateExistingPivot($developerId, $updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Assignment updated successfully'
        ]);
    }

    // Task Management
    public function getTasks(Request $request): JsonResponse
    {
        $query = ApiptTask::with(['developers', 'assignedBy']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->has('assigned_by')) {
            $query->where('assigned_by', $request->get('assigned_by'));
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->latest()->paginate(15);

        return response()->json($tasks);
    }

    public function storeTask(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_by' => 'required|exists:apipt.apipt_developers,id',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_completion_time' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,issue,not_needed,complete',
        ]);

        $task = ApiptTask::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    public function updateTask(Request $request, $id): JsonResponse
    {
        $task = ApiptTask::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_by' => 'required|exists:apipt.apipt_developers,id',
            'priority' => 'required|in:low,normal,high,urgent',
            'estimated_completion_time' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,issue,not_needed,complete',
        ]);

        $task->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    public function deleteTask($id): JsonResponse
    {
        $task = ApiptTask::findOrFail($id);
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    // Comment Management
    public function getComments(Request $request, $type, $id): JsonResponse
    {
        $modelClass = $type === 'api-progress'
            ? ApiptApiProgress::class
            : ApiptTask::class;

        $model = $modelClass::findOrFail($id);

        $comments = $model->comments()
            ->with(['developer', 'replies' => function ($query) {
                $query->with('developer')->orderBy('created_at', 'asc');
            }])
            ->whereNull('parent_id')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function storeComment(Request $request): JsonResponse
    {
        $request->validate([
            'description' => 'required|string',
            'attachments' => 'nullable|array',
            'mentions' => 'nullable|array',
            'parent_id' => 'nullable|exists:apipt.apipt_comments,id',
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'developer_id' => 'required|exists:apipt.apipt_developers,id',
        ]);

        $comment = ApiptComment::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Comment created successfully',
            'data' => $comment->load('developer')
        ], 201);
    }

    public function updateComment(Request $request, $id): JsonResponse
    {
        $comment = ApiptComment::findOrFail($id);

        $request->validate([
            'description' => 'required|string',
            'attachments' => 'nullable|array',
            'mentions' => 'nullable|array',
        ]);

        $comment->update($request->only(['description', 'attachments', 'mentions']));

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => $comment
        ]);
    }

    public function deleteComment($id): JsonResponse
    {
        $comment = ApiptComment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }

    // File Upload
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:' . config('api-progress-tracker.uploads.max_size', 10240),
        ]);

        $file = $request->file('file');
        $path = Storage::disk(config('api-progress-tracker.uploads.disk', 'local'))
            ->putFile(
                config('api-progress-tracker.uploads.path', 'api-progress-tracker/attachments'),
                $file
            );

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]
        ]);
    }

    // Statistics
    public function getStatistics(): JsonResponse
    {
        $totalApis = ApiptApiProgress::count();
        $completedApis = ApiptApiProgress::where('status', 'complete')->count();
        $inProgressApis = ApiptApiProgress::where('status', 'in_progress')->count();
        $activeTasks = ApiptTask::whereIn('status', ['todo', 'in_progress'])->count();

        $stats = [
            'totalApis' => $totalApis,
            'completedApis' => $completedApis,
            'inProgressApis' => $inProgressApis,
            'activeTasks' => $activeTasks,
            'completionPercentage' => $totalApis > 0 ? round(($completedApis / $totalApis) * 100) : 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Sync API routes
     */
    public function syncRoutes(): JsonResponse
    {
        try {
            Artisan::call('api-progress:sync-routes');
            return response()->json([
                'success' => true,
                'message' => 'Routes synced successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing routes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get simplified statistics for dashboard
     */
    public function getDashboardStats(): JsonResponse
    {
        $stats = [
            'developers' => ApiptDeveloper::count(),
            'apis' => ApiptApiProgress::count(),
            'tasks' => ApiptTask::count(),
            'completion' => $this->calculateCompletionPercentage(),
            'api_status' => ApiptApiProgress::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'api_priority' => ApiptApiProgress::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
        ];

        return response()->json($stats);
    }

    /**
     * Calculate overall completion percentage
     */
    private function calculateCompletionPercentage(): int
    {
        $totalApis = ApiptApiProgress::count();
        if ($totalApis === 0) return 0;

        $completedApis = ApiptApiProgress::where('status', 'complete')->count();
        return (int) round(($completedApis / $totalApis) * 100);
    }
}
