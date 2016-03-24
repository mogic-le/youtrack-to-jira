#!/bin/sh
echo Extracting data from REST XML:
echo Issue types:
xmlstarlet sel -t -v '/issues/issue/field[@name="Type"]/valueId' -n restdata/issues-*.xml |sort|uniq -c

echo Issue statuses:
xmlstarlet sel -t -v '/issues/issue/field[@name="State"]/valueId' -n restdata/issues-*.xml |sort|uniq -c

echo Issue priorities:
xmlstarlet sel -t -v '/issues/issue/field[@name="Priority"]/valueId' -n restdata/issues-*.xml |sort|uniq -c

echo Users:
xmlstarlet sel -t -v '/issues/issue/field[@name="Assignee" or @name="updaterName" or @name="reporterName"]/value' -n restdata/issues-*.xml |sort|uniq -c
