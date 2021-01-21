<?php
/**
 * Fill JSON file with dummy issues to keep original numbering
 */
if ($argc < 2) {
    file_put_contents('php://stderr', "Pass JSON file name\n");
    exit(1);
}
require 'config.php';

$file = $argv[1];
$json = json_decode(file_get_contents($file));

if ($json === false) {
    file_put_contents('php://stderr', "Passed JSON file is not valid\n");
    exit(2);    
}

$dummyAuthor = reset($usermap);
$dummyDate   = '2010-01-01T00:00:00+00:00';

foreach ($json->projects as $project) {
    $ids = array();
    foreach ($project->issues as $issue) {
        list(, $num) = explode('-', $issue->externalId);
        $ids[] = $num;
    }
    natsort($ids);
    $keys = array_flip($ids);
    $last = end($ids);
    for ($id = 1; $id <= $last; $id++) {
        if (!array_key_exists($id, $keys)) {
            //echo "... Missing: $id\n";
            $project->issues[] = (object) array(
                'summary'     => 'Importdummy',
                'description' => 'Import dummy issue to keep original numbering',
                'externalId'  => $project->key . '-' . $id,
                'resolution'  => 'Done',
                'status'      => 'Closed',
                'reporter'    => $dummyAuthor,
                'assignee'    => $dummyAuthor,
                'created'     => $dummyDate,
                'updated'     => $dummyDate,
            );
        }
    }
    usort(
        $project->issues,
        function ($issueA, $issueB) {
            list(, $a) = explode('-', $issueA->externalId);
            list(, $b) = explode('-', $issueB->externalId);
            return $a - $b;
        }
    );
}

if (!defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
    //PHP < 7.2 Define it as 0 so it does nothing
    define('JSON_INVALID_UTF8_SUBSTITUTE', 0);
}

$json = json_encode($json, JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);

if (!$json) {
    file_put_contents('php://stderr', "JSON could not be encoded\n");
    exit(2);    
}

echo $json;

?>