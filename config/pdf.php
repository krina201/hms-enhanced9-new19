<?php
return [
    // Allowed remote hosts for images inside PDFs (branding/signatures)
    'allowed_hosts' => array_filter(array_map('trim', explode(',', env('PDF_ALLOWED_HOSTS', '')))),
];
