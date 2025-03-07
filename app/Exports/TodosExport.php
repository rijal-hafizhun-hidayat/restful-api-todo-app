<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TodosExport implements FromView, WithStyles
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
        return view('export.todo', [
            'todos' => $this->todos,
            'sum_tracked_time_todos' => $this->sumTimeTrackedTodos,
            'count_todos' => $this->countTodos
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = count($this->todos) + 1;

        $sheet->getStyle("A1:F$totalRows")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    }
}
