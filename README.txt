/*
    xrowCDN for eZ publish
    Copyright (C) 2009  xrow GmbH, Hannover Germany

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
*/

Developed by
Björnn Dieding   ( bjoern@xrow.de )
Sören Meyer     ( soeren@xrow.de )

This extension delivers a tool to handle AWS S3.


Description of xrowCDN

----------------
1. General / Installation
----------------
xrowCDN handles the rewriting of any internal URL to images / CSS / JS. It rewrites the internal location to any mapped bucket from AWS.
A Shell script and a cronjob care about getting the according files into the S3.
http://aws.amazon.com/ Amazon Web Service

  Amazon S3 is storage for the Internet. It is designed to make web-scale computing easier for developers.
  Amazon S3 provides a simple web services interface that can be used to store and retrieve any amount of data,
  at any time, from anywhere on the web. It gives any developer access to the same highly scalable, reliable,
  fast, inexpensive data storage infrastructure that Amazon uses to run its own global network of web sites.
  The service aims to maximize benefits of scale and to pass those benefits on to developers.

(Source: Amazon Web Service Website)

Check out the xrowCDN extension and enable the extension in the site.ini.
Enable the outputfilter after the initial bucket update (see 4. for this).

xrowCDN is based on the Zend library's S3 module. So please include Zend via autoloads in the config.php

You might run into memory exhausted scenarios. In this case increase the memory. Bigger installation need more ram. 

----------------
2. CronJob
----------------
The cronjob updates distribution files and database files each time and sets the date of the last update in the database.
Command: php -d memory_limit=2048M -d safe_mode=0 runcronjobs.php xrowcdn

----------------
3. ShellScript
----------------
The shell script handles according to the rewriting defined in the xrowcdn.ini the upload of the files to the bucket / namespace.
Command: php extension\xrowcdn\bin\xrowcdn.php

Usage: extension\xrowcdn\bin\xrowcdn.php [OPTION]...
xrow CDN Shell script
Allows to handle a Cloud Distribution Network

./extension/xrowcdn/bin/xrowcdn.php -d memory_limit=2048M -d safe_mode=0 --update=distribution|database|all --clear=namespace --since=1970-01-01T00:00:00 --clear-all

General options:
  -h,--help        display this help and exit
  -q,--quiet       do not give any output except when errors occur
  -s,--siteaccess  selected siteaccess for operations,
                     if not specified default siteaccess is used
  -d,--debug...    display debug output at end of execution,
                     the following debug items can be controlled:
                     all, accumulator, include, timing, error, warning, debug, notice or strict.
  -c,--colors      display output using ANSI colors (default)
  --no-colors      do not use ANSI coloring
  --logfiles       create log files
  --no-logfiles    do not create log files (default)
  -v,--verbose...  display more information,
                     used multiple times will increase amount of information

Options:
  --update=VALUE  Updates either distributionfiles, databasefiles or all
  --clear=VALUE   Clears the bucket if provided
  --clear-all     Clears all available buckets
  --since=VALUE   Allows to update since a special time, format: YYYY-MM-DDTHH:MM:SS


----------------
4. OutputFilter
----------------
- Install the outputfilter for the front or even also the backend:
- Add this to the site.ini:

[OutputSettings]
# You can define here an ouput filter class for your website, all rendered output will be
# pass to the function filter of that class. If left empty no filter will be used.
OutputFilterName=xrowCDNFilter