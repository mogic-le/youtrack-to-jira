<?php
if ($argc < 2) {
    echo "Project key missing\n";
    exit(1);
}
$proj = $argv[1];

require_once 'config.php';
require_once 'functions.php';

$projectsfile = __DIR__ . '/restdata/projects.xml';
if (!file_exists($projectsfile)) {
    echo "File does not exist: $projectsfile\n";
    exit(1);
}

$issuesfile = __DIR__ . '/restdata/issues-' . $proj . '.xml';
if (!file_exists($issuesfile)) {
    echo "File does not exist: $issuesfile\n";
    exit(1);
}
$xp = simplexml_load_file($projectsfile);
$xproj = $xp->xpath('/projects/project[@shortName="' . $proj . '"]')[0];

$xi = simplexml_load_file($issuesfile);

$missingUsermap = [];
$missingTypemap = [];
$missingPriomap = [];
$missingStatemap = [];

foreach ($xi->issue as $xissue) {
    if (!isset($usermap[(string) $xissue->xpath('field[@name="reporterName"]/value')[0]])) {
        $missingUsermap[] = (string) $xissue->xpath('field[@name="reporterName"]/value')[0];
    }

    $xassignees = $xissue->xpath('field[@name="Assignee"]/value');
    if (count($xassignees)) {
        if(!isset($usermap[(string) $xassignees[0]])) {
            $missingUsermap[] = (string) $xassignees[0];
        }
    }

    $xtypes = $xissue->xpath('field[@name="Type"]/valueId');
    if (count($xtypes)) {
        if (!isset($typemap[(string) $xtypes[0]])) {
            $missingTypemap[] = (string) $xtypes[0];
        }
    }

    if (!isset($priomap[(string) $xissue->xpath('field[@name="Priority"]/valueId')[0]])) {
        $missingPriomap[] = (string) $xissue->xpath('field[@name="Priority"]/valueId')[0];
    }

    $xstatuses = $xissue->xpath('field[@name="State"]/valueId');
    if (count($xstatuses)) {
        if (!isset($statemap[(string) $xstatuses[0]])) {
            $missingStatemap[] = (string) $xstatuses[0];
        }
    }

    $xcomments = $xissue->xpath('comment');
    if (count($xcomments)) {
        foreach ($xcomments as $xcomment) {
            if (!isset($usermap[(string) $xcomment['author']])) {
                $missingUsermap[] = (string) $xcomment['author'];
            }
        }
    }
}

$missingUsermap = array_unique($missingUsermap);
$missingTypemap = array_unique($missingTypemap);
$missingPriomap = array_unique($missingPriomap);
$missingStatemap = array_unique($missingStatemap);

if (count($missingUsermap)) {
    echo "Missing in \$usermap:" . PHP_EOL;
    foreach($missingUsermap as $user) {
        echo "> $user" . PHP_EOL;
    }
}

if (count($missingTypemap)) {
    echo "Missing in \$typemap:" . PHP_EOL;
    foreach($missingTypemap as $type) {
        echo "> $type" . PHP_EOL;
    }
}

if (count($missingPriomap)) {
    echo "Missing in \$priomap:" . PHP_EOL;
    foreach($missingPriomap as $prio) {
        echo "> $prio" . PHP_EOL;
    }
}

if (count($missingStatemap)) {
    echo "Missing in \$statemap:" . PHP_EOL;
    foreach($missingStatemap as $state) {
        echo "> $state" . PHP_EOL;
    }
}

//fail with exit code to make the validation error detectable
if (count($missingUsermap) || count($missingTypemap) || count($missingPriomap) || count($missingStatemap)) {
    exit(1);
}

?>