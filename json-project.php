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
    "File does not exist: $projectsfile\n";
    exit(1);
}

$issuesfile = __DIR__ . '/restdata/issues-' . $proj . '.xml';
if (!file_exists($issuesfile)) {
    "File does not exist: $issuesfile\n";
    exit(1);
}
$xp = simplexml_load_file($projectsfile);
$xproj = $xp->xpath('/projects/project[@shortName="' . $proj . '"]')[0];

$xi = simplexml_load_file($issuesfile);

$json = new stdClass();

$jproject = new stdClass();
$jproject->name = (string) $xproj['name'];
$jproject->key  = substr(
    preg_replace(
        '#[^A-Z]#', '',
        strtoupper((string) $xproj['shortName'])
    ),
    0, 10
);
$jproject->description = (string) $xproj['description'];
$jproject->type = 'software';

$components = array();
$users      = array();
$versions   = array();

foreach ($xproj->subsystems->sub as $subsystem) {
    $jproject->components[] = (string) $subsystem['value'];
}

foreach ($xi->issue as $xissue) {
    $issue = new stdClass();
    $issue->summary = (string) $xissue->xpath('field[@name="summary"]/value')[0];
    $issue->description = convertHtml2Confluence(
        (string) $xissue->xpath('field[@name="description"]/value')[0]
    );
    $issue->reporter = $usermap[(string) $xissue->xpath('field[@name="reporterName"]/value')[0]];
    $xassignees = $xissue->xpath('field[@name="Assignee"]/value');
    if (count($xassignees)) {
        $issue->assignee = $usermap[(string) $xassignees[0]];
        $users[$issue->assignee] = (string) $xissue->xpath('field[@name="Assignee"]/value/@fullName')[0];
    }

    $users[$issue->reporter] = (string) $xissue->xpath('field[@name="reporterFullName"]/value')[0];

    $issue->issueType = 'Bug';
    $xtypes = $xissue->xpath('field[@name="Type"]/valueId');
    if (count($xtypes)) {
        $issue->issueType = $typemap[(string) $xtypes[0]];
    }
    $issue->priority  = $priomap[(string) $xissue->xpath('field[@name="Priority"]/valueId')[0]];

    $issue->created = date('c', (string) $xissue->xpath('field[@name="created"]/value')[0] / 1000);
    $issue->updated = date('c', (string) $xissue->xpath('field[@name="updated"]/value')[0] / 1000);

    $issue->externalId = (string) $xissue->xpath('field[@name="projectShortName"]/value')[0]
        . '-' . (string) $xissue->xpath('field[@name="numberInProject"]/value')[0];

    $issue->components = array();
    foreach ($xissue->xpath('field[@name="Subsystem"]/valueId') as $xval) {
        $issue->components[] = (string) $xval;
        $components[(string) $xval] = true;
    }
    $issue->fixedVersions = array();
    foreach ($xissue->xpath('field[@name="Fix versions"]/valueId') as $xval) {
        $issue->fixedVersions[] = (string) $xval;
        $versions[(string) $xval] = true;
    }

    //$issue->attachments = array();
    foreach ($xissue->xpath('field[@name="attachments"]/value') as $xval) {
        $issue->attachments[] = array(
            'name' => (string) $xval,
            'uri'  => $fileUriBase . ((string) $xval['id'])
                . '/' . rawurlencode((string) $xval),
        );
    }

    $xstatuses = $xissue->xpath('field[@name="State"]/valueId');
    if (count($xstatuses)) {
        $status = (string) $xstatuses[0];
        if (!isset($statemap[$status])) {
            file_put_contents('php://stderr', "Unknown issue state $status\n");
            exit(1);
        }
        $issue->status     = $statemap[$status]['status'];
        $issue->resolution = $statemap[$status]['resolution'];
    }

    $xcomments = $xissue->xpath('comment');
    if (count($xcomments)) {
        foreach ($xcomments as $xcomment) {
            $issue->comments[] = array(
                'author'  => $usermap[(string) $xcomment['author']],
                'created' => date('c', ((int) $xcomment['created']) / 1000),
                'body'    => convertHtml2Confluence((string) $xcomment['text']),
            );
            $users[$usermap[(string) $xcomment['author']]] = (string) $xcomment['authorFullName'];
        }
    }

    $xlinks = $xissue->xpath('field[@name="links"]/value');
    if (count($xlinks)) {
        foreach ($xlinks as $xlink) {
            $json->links[] = array(
                'sourceId'      => $issue->externalId,
                'destinationId' => (string) $xlink,
                'name'          => (string) $xlink['type'],
            );
        }
    }

    $jproject->issues[] = $issue;
}

$jproject->components = array_keys($components);

foreach ($versions as $version => $dummy) {
    $jproject->versions[] = ['name' => $version];
}

$json->projects[] = $jproject;

foreach ($users as $user => $fullName) {
    $json->users[] = ['name' => $user, 'fullname' => $fullName];
}

echo json_encode($json, JSON_PRETTY_PRINT) . "\n";
?>
