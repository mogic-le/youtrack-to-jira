#!/bin/sh
echo '<?xml version="1.0" encoding="utf-8"?>
<issues>'

for i in $@; do
    xmlstarlet sel -t -c '/issues/issue' -n "$i"
done
echo '</issues>'
