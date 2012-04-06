Joomla! Extension Tools
=======================
The Joomla! Extension Tools are a set of tools to help developers in creating, packaging and distributing Joomla! extensions.

The builder that you can find in the /builder directory, requires the Joomla! Platform.

Requirements
------------

- PHP 5.3
- Joomla! 2.5+
- Joomla! platform 11.4
- ...

Installation
------------

1. Download the current version of the builder <a href="https://github.com/jtools/jet/zipball/master">here</a>
2. Download the current version of the Joomla! platform <a href="https://github.com/joomla/joomla-platform/zipball/11.4">here</a>
3. Unzip both packages in a folder. You need at least from the JET the folder "builder" and from the platform the folder "libraries"
4. Last but not least do you need a working Joomla! 2.5+ environment

Execute
-------

The builder is a command line script, so you need a console or a similar environment.
There are a few command line options for the builder. The usage is as follows:

php build.php &lt;config file&gt; -j &lt;joomla folder&gt; [-b &lt;build folder&gt; -n -v]

The commands in brackets are optional.

-h --help Get a help screen

&lt;config file&gt; This is a file like the build.jet found in this repository

-j &lt;joomla folder&gt; The absolute path to the Joomla folder that we are building this stuff from.

-b &lt;build folder&gt; The folder to copy all the temporary files to and save the completed packages in.

-n Create the database schemas by using the MySQL Exporter found in the platform. ATTENTION: There is a bug in 11.4 of the platform, where this exporter does not work properly. Go to /libraries/joomla/database/database/mysqlexporter.php line 168 and add "false" in the method parameters. This has been fixed in 12.1.

-v Verbose output. This is mostly not used right now.