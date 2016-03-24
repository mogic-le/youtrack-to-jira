<?php
if ($argc < 2) {
    echo "Project key missing\n";
    exit(1);
}
$proj = $argv[1];

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
    $url . 'rest/issue/byproject/' . rawurlencode($proj)
    . '?wikifyDescription=true'
    . '&max=10000',
    false, $context
);
?>
