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

    // Φιλτράρισμα κατά κατάσταση (status)
    if ($request->filled('status') && in_array($request->status, ['pending', 'completed'])) {
        if ($request->status === 'pending') {
            $query->whereNull('status');  // Pending = status null
        } else {
            $query->where('status', $request->status);
        }
    }

    // Φιλτράρισμα με βάση αναζήτηση σε title και description
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $tasks = $query->orderBy('created_at', 'desc')->get();


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
       // $request->validate([
         //   'title' => 'required',
        //    'description' => 'required',
         //   'deadline' => 'required',
       // ]);
         try {
       // Tasks::create($request->validated());
        $data = $request->validated();
        $data['status'] = 'pending'; // default status
        Tasks::create($data);

        return redirect()->route('tasks.index')->with("success", "Task added successfully");
    } catch (\Exception $e) {
        // Προαιρετικά, μπορείς να κάνεις log το σφάλμα
        // Log::error($e->getMessage());

        return redirect()->route('tasks.create')->with("error", "Failed to add task");
    }
    }

    // DELETE /tasks/{id}
    public function destroy($id)
    {
        if (Tasks::destroy($id)) {
            return redirect()->route('tasks.index')->with("success", "Task deleted");
        }

        return redirect()->route('tasks.index')->with("error", "Task not deleted");
    }

    // PUT /tasks/{id} → μπορεί να γίνει για ολοκλήρωση task
    public function update(Request $request, $id)
    {
        $task = Tasks::findOrFail($id);
        $task->status = 'completed';

        if ($task->save()) {
            return redirect()->route('tasks.index')->with("success", "Task completed");
        }

        return redirect()->route('tasks.index')->with("error", "Task not completed");
    }
}
