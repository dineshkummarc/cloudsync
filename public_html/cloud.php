<?php
// configuration
require("../includes/config.php");

if (isset($_GET["snum"])) { //IF SELECT CLOUD
    $selcloud = query("SELECT * FROM clouds WHERE number = ? AND id = ?", $_GET["snum"], $_SESSION["id"]);
    if (empty($selcloud)) {
        apologize("Couldn't access notebook.");
    }else{
        if(isset($_SESSION["token"]))unset($_SESSION["token"]);
        $_SESSION["cloud"] = $_GET["snum"];
        if($selcloud[0]["cloudnum"]==2){ // Google Drive
            $_SESSION["token"] = $selcloud[0]["url"];
        }
    }
}
elseif(isset($_GET["rnum"]) && isset($_GET['nam'])) { //IF RENAME CLOUD
    $renbook = query("SELECT * FROM clouds WHERE number = ? AND id = ?", $_GET["rnum"], $_SESSION["id"]);
    if (empty($renbook)) {
        apologize("Couldn't access notebook.");
    }else{
        query("UPDATE clouds SET cloudname = ? WHERE number = ? AND id = ?", $_GET["nam"], $_GET["rnum"], $_SESSION["id"]);
    }
}
elseif(isset($_GET["dnum"])) { //IF DELETE CLOUD
    $delcloud = query("SELECT * FROM clouds WHERE number = ? AND id = ?", $_GET["dnum"], $_SESSION["id"]);
    if (empty($delcloud)) {
        apologize("Couldn't access notebook.");
    }else{
        if($delcloud[0]["cloudnum"]==2){ // Google Drive
            require("../includes/gclient.php");
            $client->revokeToken();
            unset($_SESSION["token"]);
        }
        query("DELETE FROM clouds WHERE number = ? AND id = ?", $_GET["dnum"], $_SESSION["id"]);
        unset($_SESSION["cloud"]);
    }
}
elseif($_SERVER["REQUEST_METHOD"] == "POST") { //IF NEW CLOUD
    if($_POST["provider"]==1){ // if DAV
        // todo: Check if server is a webdav compliant server.
        $prop = davPROP($_POST["url"], $_POST["username"], $_POST["password"]);
        $status=$prop["info"]["http_code"];
        if($status>399){
            apologize("Error $status - An unexpected error occurred.");
        }elseif(strpos($prop["info"]["content_type"],'xml') === false){ // if content type isn't xml
            apologize("Improper data returned. Check your input and try again.");
        }else{
            query("INSERT INTO clouds (id, cloudnum, username, password, cloudname, url) VALUES (?, ?, ?, ?, ?, ?)", $_SESSION["id"], $_POST["provider"], $_POST["username"], mcrypt_ecb(MCRYPT_3DES, MCRYPTKEY, $_POST["password"], MCRYPT_ENCRYPT), $_POST["name"], $_POST["url"]);
            $bokk = query("SELECT LAST_INSERT_ID() AS number");
            $bokk = $bokk[0]["number"];
            $_SESSION["cloud"] = $bokk[0]["number"];
        }
    }elseif($_POST["provider"]==2){ // If Google OAuth
        require("../includes/gclient.php");
        query("INSERT INTO clouds (id, cloudnum, cloudname) VALUES (?, ?, ?)", $_SESSION["id"], $_POST["provider"],$_POST["name"]);
        $lasttmp = query("SELECT LAST_INSERT_ID() AS number");
        $_SESSION["tmp"] = $lasttmp[0]["number"];
        $authUrl = $client->createAuthUrl();
        redirect("$authUrl");
    }
}
elseif (isset($_GET['code'])) { // From AuthUrl - Accepted
    require("../includes/gclient.php");
    $client->authenticate($_GET['code']);
    $_SESSION["token"] = $client->getAccessToken();
    query("UPDATE clouds SET url = ? WHERE number = ? AND id = ?", $_SESSION["token"], $_SESSION["tmp"], $_SESSION["id"]);
    $_SESSION["cloud"] = $_SESSION["tmp"];
    unset($_SESSION["tmp"]);
}
elseif(isset($_GET["error"])){ // From AuthUrl - Access denied
    query("DELETE FROM clouds WHERE number = ? AND id = ?", $_SESSION["tmp"], $_SESSION["id"]);
    unset($_SESSION["tmp"]);
}

redirect("/");
?>