*************************
YouTrack-to-JIRA exporter
*************************
Helps migrating projects and issues from your `YouTrack`__ bug tracking instance
into `JIRA`__.

It downloads all projects and issues from `YouTrack's REST API`__ and generates
``.json`` files that can be loaded with JIRA's JSON importer.

__ http://www.jetbrains.com/youtrack/
__ https://www.atlassian.com/software/jira/
__ http://confluence.jetbrains.com/display/YTD65/YouTrack+REST+API+Reference


Supported data
==============
* Project meta data (Title, description, key)
* Issue meta data

  * Description HTML is converted to confluence/jira wiki markup
  * Comments
  * Attachments
  * Links between issues


Usage
=====
#. Clone this repository
#. Grab a copy of `html2confluence`::

       $ git clone https://github.com/aha-app/html2confluence

#. Copy ``config.php.dist`` to ``config.php`` and adjust youtrack
   username, password and URL.
#. Run ``fetch.sh``. Things might go wrong - be aware and fix them!
#. Run ``validate.sh``. Ensure to update ``config.php`` until all mappings
   for user names, issue types, priorities and issue states are complete!
   To get JIRA user name after their GDPR update the easiest way is to download
   a backup, open the entities.xml and search for the users e-mail address. 
   The username will be something in the format ug:0000000-0000-0000-0000-000000000000.
#. Start a web server that allows access to ``restdata/files/`` so that
   JIRA can fetch the attachments from there.

       $ php -S 0.0.0.0:8080

#. Make it available online and adjust the ``$fileUriBase``
   to the public address in ``config.php`` before conversion!
   You can use a service like `ngrok <https://ngrok.com/>`_ which will make this quite easy.

       $ ngrok http 8080

#. Generate the ``.json`` files using ``convert.sh``
#. Import each project's JSON file into JIRA at
   ``System`` -> ``External System Import`` -> ``JSON``.

   Do that on a copy of your JIRA instance; many things can go wrong that you
   will have to fix before you an import with no errors. On JIRA Cloud instances
   you are able to create a backup and import the backup over and over again until
   your the imports work as expected.


Known problems
==============
- If issues were deleted in YouTrack, JIRA will assign new keys to the following
  issues. The last step in convert.sh will fill those numbers with dummy issues
  automatically. Remove the code if you don't need matching keys.
- Workflows are a huge issue. It's best to use the standard workflow and adjust
  the ``$statemap`` to it. Newly created (and imported) projects always get the
  standard Jira issue workflow.
- Since per-project JSON files are generated, cross-project links will
  not be imported.

Dependencies
============
* `git <https://git-scm.com/>`_
* `curl <https://curl.se/>`_
* `PHP <https://php.net/>`_ 5.4+


Authors
======
Written by Christian Weiske, `Mogic GmbH`__
Improved by Michael Maroszek, `Symcon GmbH`__

__ https://www.mogic.com/
__ https://www.symcon.de/
