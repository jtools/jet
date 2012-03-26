Joomla! Extension Tools
=======================
The Joomla! Extension Tools are a set of tools to help developers in creating, packaging and distributing Joomla! extensions.

The builder that you can find in the /builder directory, requires the Joomla! Platform in the root directory of this repository. This code is tested on version 11.4 of the platform. You can get the required code <a href="https://github.com/joomla/joomla-platform/zipball/11.4">here</a>.

There are a few command line options for the builder. The usage is as follows:

php build.php <config file> -j <joomla folder> [-b <build folder> -n -v]

The commands in brackets are optional.

<config file> This is a file like the build.jet found in this repository

-j <joomla folder> The absolute path to the Joomla folder that we are building this stuff from.

-b <build folder> The folder to copy all the temporary files to and save the completed packages in.

-n Create the database schemas by using the MySQL Exporter found in the platform. ATTENTION: There is a bug in 11.4 of the platform, where this exporter does not work properly. Go to /libraries/joomla/database/database/mysqlexporter.php line 168 and add "false" in the method parameters. This has been fixed in 12.1.

-v Verbose output. This is mostly not used right now.