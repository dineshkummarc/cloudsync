<?php

    /***********************************************************************
     * config.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Configures pages.
     **********************************************************************/

    // display errors, warnings, and notices
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    // requirements
    require("constants.php");
    require("functions.php");

    // enable sessions
    //session_save_path("../unused");
    session_start();

    date_default_timezone_set(TIMEZONE);

    // require authentication for most pages
    if (!preg_match("{(?:login|logout|register|forgot|sync|a)\.php$}", $_SERVER["PHP_SELF"]) && empty($_SESSION["id"]))
    {
        redirect("login.php");
    }

