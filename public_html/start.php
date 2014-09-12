<?php
require"../includes/config.php";
$id = "0B2nhApKeIKmVWDUzQlB2SVVwakk"; // "0B2nhApKeIKmVc1ljZEM2blZZYm8";
$url = "http://webdav.cubby.com:443/Web Archive"; // "http://webdav.cubby.com:443/"
file_put_contents("../unused/sync/log_".date("Y-m-d").".txt", "\nSync started...\n", LOCK_EX);
redirect("sync.php?id=".$id."&url=".str_replace(' ', '%20',$url));
?>