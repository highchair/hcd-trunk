<?php
    $local_path = "../";
    
    /* shouldn't have to touch anything below here */
    session_start();
    require_once("{$local_path}framework/bootstrap.php");
    setConfValue('LOCAL_PATH', $local_path);
    go();
?>