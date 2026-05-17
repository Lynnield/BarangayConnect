<x-mail::message>
# Scheduled Report Ready

The scheduled report **{{ $report->report_name }}** has been generated successfully.

**Report Summary:**
- **Type:** {{ ucfirst(str_replace('_', ' ', $report->report_type)) }}
- **Generated At:** {{ $report->created_at->format('M d, Y H:i:s') }}
- **Format:** {{ strtoupper($report->file_format) }}

You can find the report attached to this email or download it directly from the dashboard.

<x-mail::button :url="route('admin.reports.index')">
View All Reports
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
