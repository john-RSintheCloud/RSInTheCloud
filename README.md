RSintheClouds
=============

ResourceSpace V2 dev repo

Testing / dev environment.  You are welcome to pull this, but don't expect it to work!

History
ResourceSpace is a DAM system originally developed for Oxfam in 2006
and released as open source ; since then it has grown, but nobody has ever tamed it
or brought it up to date.

This project is attempting to rewrite RS to make it more compliant with the
concepts of Object Orientation and distributed processing, so we can run
it in the Cloud.  In doing this, we will try to move towards some standards and
processes which will avoid the problems RS has encountered.

Aims:
To migrate the code into a more structured, modular form.  The structure chosen
is based loosely on Zend Framework 1, but at this stage we are preserving much
of the procedural code.

Working towards an MVC layout, with the aim of total separation between the
business model and the view scripts.  This will allow easier reskinning and
simplify embedded viewers and e-commerce solutions as these will be separate
modules - RS will be a DAM running as a web service / API / SOAP or Rest server
to external clients.

Standards:

Coding Standards:
We are working towards good quality code, so any new code should be well commented and
self-documenting using standard doc tags.  Code should be modular and
self-contained, and ideally object oriented.
Code should be testable and ideally should include PHPUnit and / or Behat test cases.

This system is to run on Linux using PHP 5.4 (eventually).  If you wish to include
code which runs on both Linux and Windows, this is not a problem, but code which
has not been tested on Linux will not be accepted.

The basic layout will use an application folder for app-specific code and
a library folder for generic classes.  The application folder contains a bootstrap
and module folders for related classes - eg the modules/database folder will contain
all database related classes.

Each module holds a wrapper.php which converts legacy procedural code into OO
class dialogues.

TO BE Continued

JBB
22/12/13

