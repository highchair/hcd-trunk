<?php
function pagePath($relative_path)
{
    if(file_exists(LOCAL_PATH . 'content/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'content/' . $relative_path . ".php";
    }
    if(file_exists(LOCAL_PATH . 'framework/content/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'framework/content/' . $relative_path . ".php";
    }
    display_error("Content page {$relative_path} could not be properly loaded");
}

function layoutPath($relative_path)
{
    if(file_exists(LOCAL_PATH . 'content/layouts/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'content/layouts/' . $relative_path . ".php";
    }
    if(file_exists(LOCAL_PATH . 'framework/content/layouts/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'framework/content/layouts/' . $relative_path . ".php";
    }
    display_error("Layout {$relative_path} could not be properly loaded");
}

function snippetPath($relative_path)
{
    if(file_exists(LOCAL_PATH . 'content/snippets/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'content/snippets/' . $relative_path . ".php";
    }
    if(file_exists(LOCAL_PATH . 'framework/content/snippets/' . $relative_path . ".php"))
    {
        return LOCAL_PATH . 'framework/content/snippets/' . $relative_path . ".php";
    }
    display_error("Snippet {$relative_path} could not be properly loaded");
}

function adminCssPath($relative_path)
{
    if(file_exists(LOCAL_PATH . 'content/admincss/' . $relative_path . ".css"))
    {
        return LOCAL_PATH . 'content/admincss/' . $relative_path . ".css";
    }
    if(file_exists(LOCAL_PATH . 'framework/content/admincss/' . $relative_path . ".css"))
    {
        return LOCAL_PATH . 'framework/content/admincss/' . $relative_path . ".css";
    }
    display_error("Css {$relative_path} could not be properly loaded");
}
?>