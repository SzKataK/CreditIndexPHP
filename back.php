<?php
    session_start();
    if (isset($_SESSION[$_GET["id"]]))
    {
        if (file_exists("downloads/" . $_GET["id"] . ".json"))
        {
            unlink("downloads/" . $_GET["id"] . ".json");
        }
    }
    header("Location: index.php");
?>