<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 16px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #333; padding: 4px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>{{ $data['title'] ?? 'Report' }}</h1>
    <p>{{ $settings['barangay_name'] ?? '' }}, {{ $settings['city'] ?? '' }}</p>
    <p>Generated: {{ $generated_at->format('Y-m-d H:i') }}</p>
    @if(!empty($data['summary']))
        <p><strong>Total:</strong> {{ $data['summary']['total'] ?? '' }}
        · Pending: {{ $data['summary']['pending'] ?? '—' }}
        · Approved: {{ $data['summary']['approved'] ?? '—' }}
        · Rejected: {{ $data['summary']['rejected'] ?? '—' }}</p>
    @endif
    @if(!empty($data['rows']) && count($data['rows']))
    <table>
        <thead><tr>@foreach(array_keys($data['rows'][0] ?? []) as $col)<th>{{ $col }}</th>@endforeach</tr></thead>
        <tbody>
        @foreach($data['rows'] as $row)
            <tr>@foreach((array) $row as $cell)<td>{{ is_array($cell) ? json_encode($cell) : $cell }}</td>@endforeach</tr>
        @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>
