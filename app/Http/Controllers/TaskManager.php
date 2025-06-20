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

        $task = Tasks::create($data);

        // Αν είναι API request
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task
            ], 201);
        }

        // Αν είναι web (Blade)
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
}
