<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(?string $phone, string $message): bool
    {
        if (! $phone || ! SystemSetting::bool('sms_enabled', false)) {
            return false;
        }

        $provider = (string) SystemSetting::get('sms_provider', 'log');

        if ($provider === 'log') {
            Log::info('SMS delivery', ['to' => $phone, 'message' => $message]);

            return true;
        }

        if ($provider === 'webhook') {
            $url = (string) SystemSetting::get('sms_webhook_url', '');
            if (! $url) {
                return false;
            }

            $response = Http::withToken((string) SystemSetting::get('sms_api_key', ''))
                ->post($url, [
                    'to' => $phone,
                    'from' => SystemSetting::get('sms_from', config('app.name')),
                    'message' => $message,
                ]);

            return $response->successful();
        }

        Log::warning('Unknown SMS provider configured.', ['provider' => $provider]);

        return false;
    }
}
