#!/bin/sh

#fail fast
set -e

#validate that we have all required mappings
echo "Validating mappings..."
for i in $(php each-project.php); do
    echo "> $i"
    php validate.php $i
done