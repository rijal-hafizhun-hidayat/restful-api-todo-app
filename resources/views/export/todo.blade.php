<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Assignee</th>
                <th>Due Date</th>
                <th>Time Tracked</th>
                <th>Status</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todos as $todo)
                <tr>
                    <td>{{ $todo->title }}</td>
                    <td>{{ $todo->assignee }}</td>
                    <td>{{ $todo->due_date }}</td>
                    <td>{{ $todo->time_tracked }}</td>
                    <td>{{ $todo->status }}</td>
                    <td>{{ $todo->priority }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <th>Total Todos</th>
            <th>{{ $count_todos }}</th>

        </tr>
        <tr>
            <th>Total Time Tracked</th>
            <th>{{ $sum_tracked_time_todos }}</th>
        </tr>
    </table>

</body>

</html>
