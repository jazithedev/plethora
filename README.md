# plethora
Plethora is an open-source PHP framework which gives a set of tools to create web-applications.

application - directory used to store all main files related to applications (like config files, classes, views etc.)
framework   - all core files of Framework
modules     - directory used to store all of the framework modules
public_html - in here you store all public files
vendor      - stores main framework subsystems written by other authors (used for composer [https://getcomposer.org])
              for example: Doctrine 2 ORM

Use .htaccess or redirect your domain to one of applications located in `public_html` directory if You want to run app on this framework.

To run INSTALLATION process, just run application and ensure that there will be `install.php` file in the main public directory of particular application.