<?php
// Backup Database
if(date('D')=='Fri'){ // every friday
    require "../library/Backup_Database.php";
    $MessageHTML="
            <html><body>
                <p>Database backed up.</p>
            </body></html>";
    $MessageText="Database backed up.";
    mailgun('shazvi_544@hotmail.com',"Database Backed up",$MessageHTML, $MessageText);
}

//Delete db backups older than one month
$files = glob("../unused/sqldmp/*.sql", GLOB_BRACE);
foreach($files as $file){
    if(filemtime($file)<(time()-2592000)){
        unlink($file);
    }
}
?>