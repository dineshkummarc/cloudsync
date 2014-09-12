<?php
if(empty($_SERVER['QUERY_STRING'])){
    header("Location: http://cloudsync.6te.net/wapp/");
}else{
    header("Location: http://cloudsync.6te.net/wapp/?" . $_SERVER['QUERY_STRING']);
}
?>