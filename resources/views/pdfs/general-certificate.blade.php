<!DOCTYPE html>
<html><head><meta charset="utf-8"><style>body{font-family:sans-serif;font-size:12px;text-align:center;padding:40px;} .hdr{border-bottom:2px solid #000;padding-bottom:8px;margin-bottom:24px;}</style></head>
<body>
<div class="hdr"><strong>{{ $settings['barangay_name'] ?? 'Barangay' }}</strong><br>{{ $settings['city'] ?? '' }}</div>
<h2 style="text-transform:uppercase;">{{ $request->documentType->name }}</h2>
<p>This is to certify that <strong>{{ $request->resident->full_name }}</strong> is a bona fide resident of this barangay.</p>
<p>Purpose: {{ $request->purpose }}</p>
<p style="margin-top:40px;">Issued this {{ $generated_at->format('jS \of F Y') }} at {{ $settings['barangay_name'] ?? '' }}.</p>
<p><br><br>_________________________<br>{{ $settings['captain_name'] ?? 'Punong Barangay' }}</p>
</body></html>
