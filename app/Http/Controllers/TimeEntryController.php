<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class TimeEntryController extends Controller
{
public function start(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id',
    ]);

    // Σταματάμε οποιαδήποτε άλλη ενεργή καταγραφή
    $activeEntry = TimeEntry::where('user_id', $user->id)
        ->whereNull('end_time')
        ->latest()
        ->first();

    if ($activeEntry) {
        $activeEntry->end_time = now();
        $activeEntry->duration = now()->diffInSeconds($activeEntry->start_time);
        $activeEntry->save();
    }

    // Δημιουργούμε νέα καταγραφή
    $entry = TimeEntry::create([
        'user_id'    => $user->id,
        'project_id' => $validated['project_id'],
        'start_time' => now(),
        'duration'   => 0,
    ]);

    return response()->json(['entry' => $entry], 201);
}




     public function stop(Request $request)
    {
        $request->validate([
            'duration'   => 'required|integer',
            'start_time' => 'required|string',
            'end_time'   => 'required|string',
        ]);

        $userId = Auth::id();
        $entry = TimeEntry::where('user_id', $userId)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if (!$entry) {
            return response()->json(['error' => 'No active entry found'], 404);
        }

        try {
            $entry->end_time = Carbon::parse($request->end_time);
            $entry->duration = $request->duration;
            $entry->save();

            return response()->json(['message' => 'Time entry stopped!', 'entry' => $entry], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    public function quickEntry(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'description' => 'nullable|string|max:255',
            'start_time' => 'required|string', // Παράδειγμα: "14:00"
            'end_time' => 'required|string',   // Παράδειγμα: "15:30"
        ]);

        try {
            $user = Auth::user();
            $today = Carbon::today()->toDateString();

            $start = Carbon::parse("{$today} {$request->start_time}");
            $end = Carbon::parse("{$today} {$request->end_time}");

            if ($end->lt($start)) {
                return response()->json(['error' => 'End time must be after start time'], 422);
            }

            $duration = $end->diffInSeconds($start);

            $entry = TimeEntry::create([
                'user_id' => $user->id,
                'project_id' => $request->project_id,
                'description' => $request->description,
                'start_time' => $start,
                'end_time' => $end,
                'duration' => $duration,
            ]);

            return response()->json([
                'message' => 'Quick time entry saved!',
                'entry' => $entry,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function index(Request $request)
    {
        $entries = TimeEntry::where('user_id', Auth::id())
            ->with('project')
            ->orderByDesc('start_time')
            ->get();

        return response()->json($entries);
    }

    public function totalTimePerProject($projectId)
    {
        $userId = Auth::id();

        $total = TimeEntry::where('user_id', $userId)
            ->where('project_id', $projectId)
            ->sum('duration');

        return response()->json([
            'project_id' => $projectId,
            'total_seconds' => $total,
            'formatted' => gmdate('H:i:s', $total),
        ]);
    }

    public function active() 
    {
        $userId = Auth::id();

        $entry = TimeEntry::where('user_id', $userId)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if (!$entry) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active' => true,
            'entry' => $entry
        ]);
    }
}
