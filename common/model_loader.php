<?php
function modelPath($relative_path)
{
    if(file_exists(LOCAL_PATH . 'models/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'models/' . $relative_path . ".php";
    }
    if(file_exists(LOCAL_PATH . 'framework/models/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'framework/models/' . $relative_path . ".php";
    }
    display_error("Model '{$relative_path}' could not be found");
}
?>