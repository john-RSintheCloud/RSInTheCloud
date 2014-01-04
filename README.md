RSintheClouds
=============

ResourceSpace V2 dev repo

Testing / dev environment.  You are welcome to pull this, but don't expect it to work!

Route Plan

Ideally we would move RS into an object-oriented framework and then start
incorporating it into the Cloud.  Unfortunately the needs of commerce cannot
wait that long, so some bits will have to wait.

The Big Picture

Break RS down into a number of loosely coupled, cohesive modules of procedural code.

Introduce a framework to avoid re-inventing the wheel

Use the framework and good MVC practice to rework each module into a set of classes.

Update classes as required to work in the Cloud.

In more detail:

1)  Create a basic object-oriented structure - loosely coupled, cohesive components
2)  Move procedural code from global files (db.php and general.php) into wrapper
    files within each component.
3)  Create a bootstrap  to 'include' these wrappers.
4)  Move code from the /include directory to the relevant component module.
5)  Create proper sessions, to prevent excessive reliance on cookies
6)  Change the configuration from a set of globals to a function (object method)
7)  Combine all the language / wording / i18n into a single manageable unit,
    further eliminating globals and simplifying customisation.
8)  Introduce a Dependency Injection Container to manage class dependency.
9)  Move page code into view and controller / model  (MVC framework)
10) Eliminate PHP4 and 5.1 / 5.2 code - target system is PHP 5.4
11) Create  behat regression tests to ensure the system still 'works'.  Fix any
    legacy errors thrown up.
12) Work through the modules, converting existing procedural code into OO classes.
    The existing hooks will be removed, but they identify simple short functional
    blocks which can be converted into class methods.  This will allow plugins to
    overload existing methods using object inheritance.
13) Introduce the Zend Framework (v1).  Zend will be used because it is a library
    of loosely-coupled classes which can be introduced gradually; we will probably
    start with Zend Session and Zend DB
14) Start work towards moving into the Cloud

1, 2 and 3 are largely complete.  We now need to jump directly to 14.

So, new plan

1)  Create a structure, move global methods and functions into modules, create
    bootstrap.
2)  For each module:
    Move all code into the module
    Create behat regression tests and ensure module still works as expected.
    If relevant, create controllers, views and models to handle the functionality.
    Rework module to use classes, framework, etc.  Rerun tests to ensure still working
    Rework tests to provide acceptance testing for the Cloud
    Rework module as required for the Cloud.  Test against new tests.


Migration order for modules:

1)  url

    A miscellaneous collection of URL generators and resolvers and code to return
    the visitor's IP, browser and OS details.
    This is addressed first as it is injected into the session.

2)  Session

    The session has two dependencies - url and log.  Currently RS relies on lots of cookies to retain
    state, which is bad.  By using a Zend Session object, we can store all required
    information without worrying about cookies.

3)  Database

    The database access within RS is so distributed, we need a simple, central module
    with a well defined interface.  Ultimately we want to remove all SQL queries
    from other modules, but the DB module needs to provide legacy access till all
    other  modules are upgraded.
    Here we find the advantage of Zend DB - it totally encapsulates the database,
    and by using a DIC (Dependency Injection Container) we can minimise the DB module.

4)  Files and Resources

    The major challenge of the Cloud is making resources available, and this means modifying
    resources to store file location information and status.  We then decide a naming
    convention for S3 files and start uploading!

5)  Configuration and Languages

    These both rely on global variables - the configuration file creates about 500
    globals and the lanuages creates one array with 1500 elements - plus text and i18n
    values. By pulling these into classes we can increase encapsulation and start
    looking at simplifying the structure and making the content relevant to the page
    being viewed.


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

