<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TodosExport implements FromView
{
    protected $todos;
    protected $sumTimeTrackedTodos;
    protected $countTodos;

    public function __construct($todos, $sumTimeTrackedTodos, $countTodos)
    {
        $this->todos = $todos;
        $this->sumTimeTrackedTodos = $sumTimeTrackedTodos;
        $this->countTodos = $countTodos;
    }

    public function view(): View
    {
        return view('exports.todo', [
            'todos' => $this->todos,
            'sum_tracked_time_todos' => $this->sumTimeTrackedTodos,
            'count_todos' => $this->countTodos
        ]);
    }
}
