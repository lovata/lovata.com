<?php

/**
 * @see https://codeburst.io/the-only-way-to-detect-touch-with-javascript-7791a3346685
 * The cookie is is set in backend.js
 */
$b = [];

if (isset($_COOKIE['oc-user-touch'])) {
    $b[] = 'user-touch';
}

if (Backend\Models\BrandSetting::get('color_mode') === 'auto') {
    $b[] = 'color-mode-auto';
}

echo join(' ', $b);
