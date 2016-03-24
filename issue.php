<?php
if ($argc < 2) {
    echo "issue key missing\n";
    exit(1);
}
$issue = $argv[1];

require_once 'config.php';
require_once 'functions.php';
$cookieheader = login();
$context = stream_context_create(
    [
        'http' => [
            'header' => $cookieheader
        ]
    ]
);
echo file_get_contents(
    $url . 'rest/issue/' . rawurlencode($issue) . '?wikifyDescription=true',
    false, $context
);
?>
