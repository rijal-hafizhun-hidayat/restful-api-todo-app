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
}
