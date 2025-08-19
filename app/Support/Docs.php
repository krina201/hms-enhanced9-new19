<?php
namespace App\Support;

class Docs
{
    private static function sanitizeUrl(?string $url): ?string
    {
        if (!$url) return null;
        $parts = parse_url($url);
        if (!$parts || empty($parts['host'])) return $url; // local
        $allowed = config('pdf.allowed_hosts', []);
        return in_array($parts['host'], $allowed, true) ? $url : null;
    }

    public static function terms(string $doc): ?string
    {
        // doc = 'po' or 'grn'
        if (class_exists(\App\Models\Setting::class)) {
            $key = $doc . '_terms';
            return \App\Models\Setting::value($key);
        }
        return null;
    }

    public static function signatures(): array
    {
        $prepared = null; $approved = null;
        if (class_exists(\App\Models\Setting::class)) {
            $prepared = \App\Models\Setting::value('signature_prepared_by_url');
            $approved = \App\Models\Setting::value('signature_approved_by_url');
        }
        $prepared = self::sanitizeUrl($prepared);
        $approved = self::sanitizeUrl($approved);
        return ['prepared'=>$prepared,'approved'=>$approved];
    }
}
