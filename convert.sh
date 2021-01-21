#!/bin/sh

#fail fast
set -e

#create directory json if missing
mkdir -p json

#convert data to jira import json
echo "Convert XML to JSON..."
for i in $(php each-project.php); do
    if [ ! -f json/$i.json ]; then
        echo "> $i"
        php json-project.php $i > json/$i.json
    fi
done

#create directory json if missing
mkdir -p json/fixed

#fill missing issue numbers to keep numbering in JIRA
echo "Fill issue numbering blanks in JSON..."
for i in $(php each-project.php); do
    if [ ! -f json/fixed/$i.json ]; then
        echo "> $i"
        php fill.php json/$i.json > json/fixed/$i.json
    fi
done