<?php
if ($argc < 2) {
    echo "Project key missing\n";
    exit(1);
}
$proj = $argv[1];

$issuesfile = __DIR__ . '/restdata/issues-' . $proj . '.xml';
if (!file_exists($issuesfile)) {
    echo "File does not exist: $issuesfile\n";
    exit(1);
}

$xi = simplexml_load_file($issuesfile);
foreach($xi->xpath('/issues/issue/@id') as $xiid) {
    echo $xiid . " ";
}

?>