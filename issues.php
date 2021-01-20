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

$pos = 0;
$res = new DOMDocument('1.0', 'UTF-8');
$iss = $res->createElement("issues");

//youtrack's internal max value is 500; you won't get around this
//lets fetch by parts of 500 until the remaining count is not equal 500 elements
do {
    $xml = file_get_contents(
        $url . 'rest/issue/byproject/' . rawurlencode($proj)
        . '?wikifyDescription=true'
        . '&after=' . $pos
        . '&max=500',
        false, $context
    );

    $dom = new DOMDocument();
    $dom->loadXML($xml);

    $count = count($dom->firstChild->childNodes);

    foreach($dom->firstChild->childNodes as $node) {
        $iss->appendChild($res->importNode($node, true));
    }

    $pos += $count;
} while($count == 500);

$res->appendChild($iss);
$res->formatOutput = true;
$res->save('php://output');

?>
