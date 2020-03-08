<div id="dataTable" class="card px-4">
    <table id="{{ $tableName }}" class="display w-100">
        <thead class="border-bottom border-secondary">
            <tr>
                @foreach ($columns as $col)
                    <th class="text-muted font-weight-normal">{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>