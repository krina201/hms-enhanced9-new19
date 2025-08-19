<?php
namespace App\Support;

class Branding
{
    private static function sanitizeUrl(?string $url): ?string
    {
        if (!$url) return null;
        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) return $url; // local path
        $allowed = config('pdf.allowed_hosts', []);
        return in_array($parts['host'], $allowed, true) ? $url : null;
    }

    public static function data(): array
    {
        $name = config('app.name', 'Hospital HMS');
        $logo = null;
        $address = null;

        // If you have a Setting model (key/value), use it.
        if (class_exists(\App\Models\Setting::class)) {
            try {
                $name = \App\Models\Setting::value('hospital_name') ?: $name;
                $logo = \App\Models\Setting::value(\1
                $logo = self::sanitizeUrl($logo);
                $address = \App\Models\Setting::value('hospital_address') ?: null;
            } catch (\Throwable $e) {}
        }

        return [
            'name' => $name,
            'logo' => $logo,
            'address' => $address,
        ];
    }
}
