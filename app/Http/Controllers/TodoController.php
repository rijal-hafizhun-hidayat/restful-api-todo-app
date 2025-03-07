<?php

namespace App\Http\Controllers;

use App\Exports\TodosExport;
use App\Http\Requests\TodoRequest;
use App\Models\Todo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TodoRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            $todo = Todo::create($data);
            DB::commit();
            return response()->json($todo);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
    }

    public function exportExcell(Request $request)
    {
        $queryTodo = Todo::query();

        if ($request->filled('title')) {
            $queryTodo->where('title', 'like', '%' . $request->query('title') . '%');
        }

        if ($request->filled('assignee')) {
            $assignees = array_map('trim', explode(',', $request->query('assignee')));
            $queryTodo->whereIn('assignee', $assignees);
        }

        if ($request->filled('start')) {
            $queryTodo->where('due_date', '>=', $request->query('start'));
        }

        if ($request->filled('end')) {
            $queryTodo->where('due_date', '<=', $request->query('end'));
        }

        if ($request->filled('min')) {
            $queryTodo->where('time_tracked', '>=', $request->query('min'));
        }

        if ($request->filled('max')) {
            $queryTodo->where('time_tracked', '<=', $request->query('max'));
        }

        if ($request->filled('status')) {
            $status = array_map('trim', explode(',', $request->query('status')));
            $queryTodo->whereIn('difficulty', $status);
        }

        if ($request->filled('priority')) {
            $priorities = array_map('trim', explode(',', $request->query('priority')));
            $queryTodo->whereIn('priority', $priorities);
        }

        $dataTodos = $queryTodo->get();
        $countTodos = $queryTodo->count();
        $sumTimeTrackedTodos = $queryTodo->sum('time_tracked');

        return Excel::download(new TodosExport($dataTodos, $sumTimeTrackedTodos, $countTodos), 'todos.xlsx');
    }

    public function chartTodo(Request $request)
    {
        if ($request->filled('type')) {
            $type = $request->query('type');

            switch ($type) {
                case 'status':
                    return $this->chartTodoByTypeStatus();
                    break;

                case 'priority':
                    return $this->chartTodoByTypePriority();
                    break;

                case 'assignee':
                    return $this->chartTodoByTypeAssignee();
                    break;
                default:
                    return response()->json([
                        'message' => "invalid type value",
                        'errors' => "invalida type value"
                    ], 404);
                    break;
            }
        } else {
            return response()->json([
                'message' => 'wrong params name',
                'errors' => "wrong params name"
            ], 400);
        }
    }

    public function chartTodoByTypeStatus()
    {
        $todo = Todo::query();
        $sumPending = $todo->where('status', 'pending')->count();
        $sumOpen = $todo->where('status', 'open')->count();
        $sumInProgress = $todo->where('status', 'in_progress')->count();
        $sumCompleted = $todo->where('status', 'completed')->count();

        return response()->json([
            "status_summary" => [
                "pending" => $sumPending,
                'open' => $sumOpen,
                'in_progress' => $sumInProgress,
                'completed' => $sumCompleted
            ]
        ], 200);
    }

    public function chartTodoByTypePriority()
    {
        $todo = Todo::query();
        $sumLow = $todo->where('priority', 'low')->count();
        $sumMedium = $todo->where('priority', 'medium')->count();
        $sumHigh = $todo->where('priority', 'high')->count();

        return response()->json([
            "priority_summary" => [
                "low" => $sumLow,
                'medium' => $sumMedium,
                'high' => $sumHigh,
            ]
        ], 200);
    }

    public function chartTodoByTypeAssignee()
    {
        $todos = Todo::all();

        $summary = $todos->groupBy('assignee')->map(function ($items, $assignee) {
            return [
                'assignee' => $assignee != '' ? $assignee : 'Unassigned',
                'total_todos' => $items->count(),
                'total_time_tracked' => $items->sum('time_tracked'),
                'total_pending_todos' => $items->where('status', 'pending')->count(),
            ];
        })->values();

        return response()->json(["assignee_summary" => $summary], 200);
    }
}
