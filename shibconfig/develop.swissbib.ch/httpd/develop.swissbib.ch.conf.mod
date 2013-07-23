LoadModule ssl_module modules/mod_ssl.so

<VirtualHost 131.152.158.253:443>
    ServerAdmin guenter.hipler@unibas.ch
    ServerName develop.swissbib.ch
    DocumentRoot /usr/local/vufind/httpd/public/

    ErrorLog /var/log/httpd/develop.swissbib.error.log
    CustomLog /var/log/httpd/develop.swissbib.ch.access.log combined
    LogLevel warn
    <Files ~ "^\.ht">
        Order allow,deny
        Deny from all
        Satisfy all
    </Files>

    SSLEngine on
    SSLCertificateFile /etc/httpd/cert/testvf.swissbib.ch.crt.pem
    SSLCertificateKeyFile /etc/httpd/cert/testvf.key

    php_flag engine on

   # Configuration for theme-specific resources:
   AliasMatch ^/themes/([0-9a-zA-Z-_]*)/css/(.*)$ /usr/local/vufind/httpd/themes/$1/css/$2
   AliasMatch ^/themes/([0-9a-zA-Z-_]*)/images/(.*)$ /usr/local/vufind/httpd/themes/$1/images/$2
   AliasMatch ^/themes/([0-9a-zA-Z-_]*)/js/(.*)$ /usr/local/vufind/httpd/themes/$1/js/$2
   <Directory ~ "^/usr/local/vufind/httpd/themes/([0-9a-zA-Z-_]*)/(css|images|js)/">
        Order allow,deny
        allow from all
        AllowOverride All
   </Directory>

   AliasMatch ^/shibtest/(.*)$ /usr/local/vufind/httpd/shibtest/$1

   <Directory ~ "^/usr/local/vufind/httpd/shibtest/">
        Order allow,deny
        allow from all
        AllowOverride All
        RewriteEngine on
   </Directory>


   # Configuration for general VuFind base:
   #Alias /vufind /usr/local/vufind/httpd/public
   <Directory /usr/local/vufind/httpd/public/>
        Order allow,deny
        allow from all
        AllowOverride All

        # Uncomment the following lines, if you wish to use the Shibboleth authentication
        #AuthType shibboleth
        #require shibboleth

        RewriteEngine On
        RewriteBase /
        RewriteCond %{REQUEST_FILENAME} -s [OR]
        RewriteCond %{REQUEST_FILENAME} -l [OR]
        RewriteCond %{REQUEST_FILENAME} -d
        RewriteRule ^.*$ - [NC,L]
        RewriteRule ^.*$ index.php [NC,L]

        php_value short_open_tag On

        # Uncomment this line to put VuFind into development mode in order to see more detailed messages:
        SetEnv VUFIND_ENV development

        # Uncomment this line if you want to use the XHProf profiler; this is a developer-oriented option
        # that most users will not need.  Make sure the XHProf PHP libraries are available on your include
        # path.  See http://vufind.org/jira/browse/VUFIND-419 for more details.
        #SetEnv VUFIND_PROFILER_XHPROF http://url/to/your/xhprof/web/interface

        # This line points to the local override directory where you should place your customized files
        # to override VuFind core features/settings.  Set to blank string ("") to disable.
        SetEnv VUFIND_LOCAL_DIR /usr/local/vufind/httpd/local

        # This line specifies additional Zend Framework 2 modules to load after the standard VuFind module.
        # Multiple modules may be specified separated by commas.  This mechanism can be used to override
        # core VuFind functionality without modifying core code.
       SetEnv VUFIND_LOCAL_MODULES Swissbib
   </Directory>

</VirtualHost>


<VirtualHost *:80>

    ServerName develop.swissbib.ch

    Redirect permanent / https://develop.swissbib.ch/

</VirtualHost>
