<?php
require_once 'config.php';
require_once 'functions.php';
$cookieheader = login();
foreach (glob(__DIR__ . '/restdata/issues-*.xml') as $issuesfile) {
    $sx = simplexml_load_file($issuesfile);
    foreach ($sx->xpath('/issues/issue/field[@name="attachments"]/value') as $xFile) {
        $fileDir = __DIR__ . '/restdata/files/' . $xFile['id'];
        $fileName = $fileDir . '/' . (string) $xFile;
        if (!is_dir($fileDir)) {
            mkdir($fileDir);
        }
        if (file_exists($fileName)) {
            continue;
        }
        echo "> Fetching " . (string) $xFile . " (" . $xFile['id'] . ")\n";
        exec(
            'curl'
            . ' --silent'
            . ' --header ' . escapeshellarg($cookieheader)
            . ' --output ' . escapeshellarg($fileName)
            . ' ' . escapeshellarg($xFile['url']),
            $output,
            $retval
        );
        if ($retval !== 0) {
            file_put_contents('php://stderr', "Error loading file:\n" . $xFile->asXML() . "\n");
            exit(2);
        }
    }
}
?>