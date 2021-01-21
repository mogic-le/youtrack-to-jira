#!/bin/sh

#fail fast
set -e

#create directory restdata if missing
mkdir -p restdata

#fetch project list
echo "Fetching projects..."
if [ ! -f restdata/projects.xml ]; then
    php projects.php > restdata/projects.xml
fi

#fetch issue list for each project
echo "Fetching issue list..."
for i in $(php each-project.php); do
    if [ ! -f restdata/issues-$i.xml ]; then
        echo "> $i"
        php issues.php $i > restdata/issues-$i.xml
    fi
done

#create directory restdata/files if missing
mkdir -p restdata/files

# download all files
echo "Download attachments..."
php files.php

#create directory restdata/issues if missing
mkdir -p restdata/issues

#download all issues into single files. not really needed, only for backup purposes
echo "Download issues..."
for i in $(php each-project.php); do
    for j in $(php each-issue.php $i); do
        if [ ! -f restdata/issues/$j.xml ]; then
            echo "> $j"
            php issue.php $j > restdata/issues/$j.xml
        fi
    done
done