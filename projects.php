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

$xml = file_get_contents($url . 'rest/project/all?verbose=true', false, $context);

$res = new DOMDocument('1.0', 'UTF-8');
$res->loadXML($xml);
$res->formatOutput = true;
$res->save('php://output');

?>
