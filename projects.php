<?php
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
echo file_get_contents($url . 'rest/project/all?verbose=true', false, $context);
?>
