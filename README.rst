*************************
Youtrack-to-JIRA exporter
*************************
Helps migrating projects and issues from your `Youtrack`__ bug tracking instance
into `JIRA`__.

It downloads all projects and issues from `Youtrack's REST API`__ and generates
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
#. Grab a copy of `html2confluence`__
   (or our `own fork`__ with the neccessary script)::

       $ git clone git@github.com:mogic-le/html2confluence.git

#. Copy ``config.php.dist`` to ``config.php`` and adjust youtrack
   username, password and URL.
#. Follow the instructions in ``fetch.sh``.
   I would not recommend running ``fetch.sh`` itself, as there are too many
   things that can go wrong.
#. After downloading all REST data from youtrack, adjust ``config.php``
   again by adding all the user names, issue types, priorities and
   issue states you can extract from the XML files.

   Use ``info-rest.sh`` to extract these information from XML files.
#. Start a web server that allows access to ``restdata/files/`` so that
   JIRA can fetch the attachments from there
   (adjust ``$fileUriBase`` in ``config.php``)
#. Generate the ``.json`` files (see ``fetch.sh``)
#. Import each project's JSON file into JIRA at
   ``System`` -> ``External System Import`` -> ``JSON``.

   Do that on a copy of your JIRA instance; many things can go wrong that you
   will have to fix before you an import with no errors.

__ https://github.com/k1w1/html2confluence
__ https://github.com/mogic-le/html2confluence


Known problems
==============
- If issues were deleted in Youtrack, JIRA will assign new keys to the following
  issues.

  Use ``fill.php project.json > project-fix.json`` to add dummy issues that fill
  the void.
- Workflows are a huge issue. It's best to use the standard workflow and adjust
  the ``$statemap`` to it.
  Newly created (and imported) projects always get the standard Jira issue
  workflow.
- Since per-project JSON files are generated, cross-project links will
  not be imported.

Dependencies
============
* `git <https://git-scm.com/>`_
* `PHP <https://php.net/>`_ 5.4+
* `xmlstarlet <http://xmlstar.sourceforge.net/>`_
* `xmllint <http://xmlsoft.org/xmllint.html>`_


Author
======
Written by Christian Weiske, `Mogic GmbH`__

__ http://www.mogic.com/
