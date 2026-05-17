<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>body{font-family:sans-serif;font-size:12px;padding:24px;}</style></head>
<body>
<h2>Acknowledgement</h2>
<p>Request {{ $request->request_number }} received by {{ $settings['barangay_name'] ?? '' }}.</p>
<p>Date: {{ $generated_at->format('Y-m-d H:i') }}</p>
</body></html>
