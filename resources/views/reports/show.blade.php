<x-app-layout title="{{ $title }} · Fleet Manager">
    <div class="page-header">
        <div><span class="page-eyebrow">Reports</span><h1 class="page-title">{{ $title }}</h1></div>
        <a class="btn btn-primary" href="{{ request()->fullUrlWithQuery(['format' => 'csv']) }}">Export CSV</a>
    </div>
    <div class="panel">
        <table>
            <thead><tr>@foreach ($headers as $header)<th>{{ $header }}</th>@endforeach</tr></thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>@foreach ($row as $value)<td>{{ $value ?? '—' }}</td>@endforeach</tr>
                @empty
                    <tr><td colspan="{{ count($headers) }}">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
