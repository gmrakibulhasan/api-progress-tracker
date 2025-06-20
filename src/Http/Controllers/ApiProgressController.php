<?php

namespace Gmrakibulhasan\ApiProgressTracker\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:apipt_developers,email',
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
            'email' => 'required|email|unique:apipt_developers,email,' . $id,
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
        $query = ApiptApiProgress::with(['developers', 'assignedBy']);

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

        $apiProgress = $query->latest()->paginate(15);

        return response()->json($apiProgress);
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
            'assigned_by' => 'required|exists:apipt_developers,id',
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
            'assigned_by' => 'required|exists:apipt_developers,id',
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
            ->with(['developer', 'replies.developer'])
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
            'parent_id' => 'nullable|exists:apipt_comments,id',
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer',
            'developer_id' => 'required|exists:apipt_developers,id',
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
        $stats = [
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
                    ->get(),
            ],
        ];

        return response()->json($stats);
    }
}
