New Hotness:
1. Set up new server or directory
2. Check out the latest version of the unfuddle repository: 

svn co http://highchairdesign.unfuddle.com/svn/highchairdesign_hcdcms2-0/ /htdocs/framework

This will check out a copy of Framework, so it is important to use the name "framework" for the parent folder of this checkout. 

3. Copy the contents of the default_www directory from the framework directory to the publicly visible directory, or, use SVN to make a copy:

svn export http://highchairdesign.unfuddle.com/svn/highchairdesign_hcdcms2-0/ /htdocs/www_or_similar

4. In www/index.php, update the $local_path variable to point to the full local path (no relative directories!)
5. Edit the conf.php file with the connection info for this new DB setup and check off the appropriate modules. 
6. Miller Time



Old, but still useful:

1: php 5 is installed
(1.1: make sure mysql module is enabled)
(1.2: check for GD support)
2: Database is created
3: DB Connection updated and formatted correctly in application
4: If on modwest install .htaccess files in all folders except "www" to limit subdomain access

Htaccess should look like this at the very least, but the new one has more from html5boilerplate

RewriteEngine on
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^(.+) - [PT,L]
RewriteRule ^(.*) /index.php [L]

5: if using a temp site on modwest change the .htaccess in www on line

RewriteRule ^(.*) /index.php?id=$1 [L]

to

RewriteRule ^(.*) / YOUR CURRENT SITE /index.php?id=$1 [L]
