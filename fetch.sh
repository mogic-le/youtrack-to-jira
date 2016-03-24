#!/bin/sh
#fetch project list
php projects.php | xmllint --format - > restdata/projects.xml

#fetch issue list for each project
for i in `xmlstarlet sel -t -v '/projects/project/@shortName' restdata/projects.xml`; do
    echo $i
    php issues.php $i | xmllint --format - > restdata/issues-$i.xml
done

# download all files
php files.php

# download all issues into single files
# not really needed, only for backup purposes
mkdir restdata/issues
for i in `xmlstarlet sel -t -v '/issues/issue/@id' -n restdata/issues-*.xml`; do
    echo $i
    php issue.php $i | xmllint --format - > restdata/issues/$i.xml
done

# convert data to jira import json
for i in `xmlstarlet sel -t -v '/projects/project/@shortName' restdata/projects.xml`; do
    echo $i
    php json-project.php $i | jq . > json/$i.json
done
