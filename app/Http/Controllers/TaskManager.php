<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tasks;

class TaskManager extends Controller
{
    function listTask()
    {
        $tasks = Tasks::where("status", NULL)->get();
        return view("welcome", compact('tasks'));
    }

    function addTask()
    {
        return view('tasks.add');
    }

    function addTaskPost(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'deadline' => 'required'
        ]);

        $task = new Tasks();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->deadline = $request->deadline;

        if ($task->save()) {
            return redirect(route("home"))->with("success", "Add Task Successful");
        }

        return redirect(route("task.add"))->with("error", "Task Not Added");
    }

    function updateTaskStatus($id)
    {
        if (Tasks::where('id', $id)->update(['status' => "completed"])) {
            return redirect(route("home"))->with("success", "Task completed");
        }

        return redirect(route("home"))->with("error", "Task not completed");
    }

    function deleteTask($id)
    {
        if (Tasks::where('id', $id)->delete()) {
            return redirect(route("home"))->with("success", "Task deleted");
        }

        return redirect(route("home"))->with("error", "Task not deleted");
    }
}
