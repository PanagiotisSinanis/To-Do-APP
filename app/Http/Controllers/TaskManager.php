<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Http\Request;
use App\Models\Tasks;

class TaskManager extends Controller
{
    // GET /tasks
public function index(Request $request)
{
    $query = Tasks::query();

    // Φιλτράρισμα με βάση status (pending/completed)
    if ($request->filled('status') && in_array($request->status, ['pending', 'completed'])) {
        if ($request->status === 'pending') {
            $query->whereNull('status');
        } else {
            $query->where('status', $request->status);
        }
    }

    // Φιλτράρισμα με βάση αναζήτηση σε title ή description
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Νέο: Φιλτράρισμα με βάση τίτλο (ξεχωριστό)
    if ($request->filled('title')) {
        $title = $request->title;
        $query->where('title', 'like', "%{$title}%");
    }

    // Νέο: Φιλτράρισμα με βάση ημερομηνία (created_at)
    // Περιμένουμε την ημερομηνία σε μορφή yyyy-mm-dd
    if ($request->filled('date')) {
        $date = $request->date;
        // Φιλτράρουμε tasks που δημιουργήθηκαν ακριβώς αυτή την ημέρα
        $query->whereDate('created_at', $date);
    }

    $tasks = $query->orderBy('created_at', 'desc')->get();

    if ($request->wantsJson()) {
        return response()->json($tasks);
    }

    return view("welcome", compact('tasks'));
}




    // GET /tasks/create
    public function create()
    {
        return view('tasks.add');
    }

    // POST /tasks
  public function store(StoreTaskRequest $request)
{
    try {
        $data = $request->validated();
        $data['status'] = 'pending';

        $user = $request->user();

        // Έλεγχος αν ο χρήστης ανήκει στο project
        $belongs = $user->projects()->where('projects.id', $data['project_id'])->exists();

        if (!$belongs) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized: You do not belong to this project'], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to add tasks to this project');
        }

        $task = Tasks::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task
            ], 201);
        }

        return redirect()->route('tasks.index')->with("success", "Task added successfully");

    } catch (\Exception $e) {
        if ($request->wantsJson()) {
            return response()->json(['error' => 'Failed to create task'], 500);
        }

        return redirect()->route('tasks.create')->with("error", "Failed to add task");
    }
}



    // DELETE /tasks/{id}
    public function destroy(Request $request, $id)
{
    if (Tasks::destroy($id)) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Task deleted'], 200);
        }
        return redirect()->route('tasks.index')->with("success", "Task deleted");
    }

    if ($request->wantsJson()) {
        return response()->json(['error' => 'Task not deleted'], 500);
    }
    return redirect()->route('tasks.index')->with("error", "Task not deleted");
}

    // PUT /tasks/{id} → μπορεί να γίνει για ολοκλήρωση task
    public function update(Request $request, $id)
{
    $task = Tasks::findOrFail($id);
    $task->status = 'completed';

    if ($task->save()) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Task completed', 'task' => $task], 200);
        }
        return redirect()->route('tasks.index')->with("success", "Task completed");
    }

    if ($request->wantsJson()) {
        return response()->json(['error' => 'Task not completed'], 500);
    }
    return redirect()->route('tasks.index')->with("error", "Task not completed");
}
public function getTasksByProject($id)
{
    try {
        $tasks = Tasks::where('project_id', $id)->get();
        return response()->json(['tasks' => $tasks]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to fetch tasks',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
