<?php

$projectsfile = __DIR__ . '/restdata/projects.xml';
if (!file_exists($projectsfile)) {
    file_put_contents('php://stderr', "File does not exist: $projectsfile\n");
    exit(1);
}

$xp = simplexml_load_file($projectsfile);
foreach($xp->xpath('/projects/project/@shortName') as $xpsn) {
    echo $xpsn . " ";
}

?>