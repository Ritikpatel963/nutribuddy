<?php
$file = 'e:/website-project/Nutribuddy/resources/views/admin/access/index.blade.php';
$content = file_get_contents($file);

preg_match('/<style>(.*?)<\/style>/s', $content, $cssMatch);
if (!empty($cssMatch[1])) {
    file_put_contents('e:/website-project/Nutribuddy/public/assets/css/access-control.css', trim($cssMatch[1]));
    $content = str_replace($cssMatch[0], '<link rel="stylesheet" href="{{ asset(\'assets/css/access-control.css\') }}">', $content);
}

preg_match('/<script>(.*?)<\/script>/s', $content, $jsMatch);
if (!empty($jsMatch[1])) {
    file_put_contents('e:/website-project/Nutribuddy/public/assets/js/access-control.js', trim($jsMatch[1]));
    $content = str_replace($jsMatch[0], '<script src="{{ asset(\'assets/js/access-control.js\') }}"></script>', $content);
}

file_put_contents($file, $content);
echo "Extracted successfully\n";
