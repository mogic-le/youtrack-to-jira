<?php
if ($argc < 2) {
    file_put_contents('php://stderr', "Project key missing\n");
    exit(1);
}
$proj = $argv[1];

$issuesfile = __DIR__ . '/restdata/issues-' . $proj . '.xml';
if (!file_exists($issuesfile)) {
    file_put_contents('php://stderr', "File does not exist: $issuesfile\n");
    exit(1);
}

$xi = simplexml_load_file($issuesfile);
foreach($xi->xpath('/issues/issue/@id') as $xiid) {
    echo $xiid . " ";
}

?>