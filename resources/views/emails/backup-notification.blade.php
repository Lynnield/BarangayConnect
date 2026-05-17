<x-mail::message>
# Backup {{ $success ? 'Successful' : 'Failed' }}

The system backup process for **{{ config('app.name') }}** has completed with status: **{{ $success ? 'SUCCESS' : 'FAILURE' }}**.

**Backup Details:**
- **Name:** {{ $backup->backup_name }}
- **Type:** {{ ucfirst($backup->backup_type) }}
- **Date:** {{ $backup->created_at->format('M d, Y H:i:s') }}
@if($success)
- **Size:** {{ number_format($backup->file_size / 1024 / 1024, 2) }} MB
@endif

@if(!$success)
**Error Message:**
{{ $errorMessage }}
@endif

@if($success && $backup->id)
<x-mail::button :url="route('admin.backups.download', $backup)">
Download Backup
</x-mail::button>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
