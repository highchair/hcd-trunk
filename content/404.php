<?php
    @header("HTTP/1.1 404 Not Found",1);
    @header("Status: 404 Not Found",1);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Page Not Found</title>
    </head>
    <body>
        <h1>The requested page /<?php echo $_GET["id"]; ?> could not be found.</h1>
        <p>Return to <a href="<?php echo get_link("/"); ?>"><?php echo SITE_NAME ?></a></p>
    </body>
</html>
