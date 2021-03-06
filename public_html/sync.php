<?php
/***********************************************************************
 * Custom script for syncing files between two cloud storage providers.
 *
 * In this case, it's a one-way sync(mirror) from Dav to Google Drive
 **********************************************************************/

// todo: Gdrive transfer mime type octet check

//configuration
require"../includes/config.php";
ini_set("max_execution_time", "0"); // Allow infinite execution time
ini_set("upload_max_filesize", "50M"); // Probably doesn't work but it doesn't matter for remote downloads anyway

// Initial setup
if (!isset($_SESSION["starttime"])) $_SESSION["starttime"] = time();
require("../includes/gclient.php");
$client->setAccessToken(TOKEN);
$service = new Google_DriveService($client);

// Functions
function parsedrivefolder($folderid, $service){
    try {
        $children = $service->children->listChildren($folderid, array("q"=>"trashed != true"));
        foreach ($children->items as $child) {
            $item = $service->files->get((string)$child->id);
            $array[] = array(
                "type" => $item->getMimeType(),
                "folder" => ((string)$item->getMimeType()=="application/vnd.google-apps.folder")?"t":"f",
                "name" => $item->getTitle(),
                "size" => (strpos($item->getMimeType(), "application/vnd.google-apps") !== false)?"f":$item->getFileSize(),
                "modified" => strtotime($item->getModifiedDate()),
                "id" => $item->getId(),
                "md5" => $item->getMd5Checksum()
            );
        }
        return $array;
    } catch (Exception $e) {
        print("An error occurred: " . $e->getMessage());
    }
    return false;
}

function parsedavfolder($url){
    $prop = davPROP($url, DAVUSERNAME, DAVPASS);
    $status = $prop["info"]["http_code"];
    if($status<400){
        if (strpos($prop["result"], '<?xml') !== false) {
            $xml = xmlstr_to_array(strstr($prop["result"], '<?xml'));
            $xml = $xml['d:response'];
            foreach ($xml as $row){
                $array[] = array(
                    "folder" => (isset($row['d:propstat']['d:prop']['d:isFolder']))?"t":"f",
                    "name" => $row['d:propstat']['d:prop']['d:displayname'],
                    "size" => (isset($row['d:propstat']['d:prop']['d:getcontentlength']))?$row['d:propstat']['d:prop']['d:getcontentlength']:"f",
                    "modified" => strtotime($row['d:propstat']['d:prop']['d:getlastmodified'])+LOCALTIME,
                    "href" => $row['d:href']
                );
            }
            array_shift($array);
            return $array;
        }else print("No xml data returned.");
    }else print("Error $status - An unexpected error occurred.");
    return false;
}

////////////////////////////////  Main Sync Function(recursive)  ////////////////////////////////////////
function sync($davlink, $drivelink, $service){
    $davarray = parsedavfolder($davlink);
    $drivearray = parsedrivefolder($drivelink, $service);

    function f1($a,$b){ // Same in Dav
        return strcmp($a['name'], $b['name']);
    }$same=array_values(array_uintersect($davarray, $drivearray, "f1"));

    function f2($a,$b){ // Same in Drive
        return strcmp($a['name'], $b['name']);
    }$sameindrive=array_values(array_uintersect($drivearray, $davarray, "f2"));

    function f3($a, $b) { // Sort by name
        return strcmp($a['name'],$b['name']);
    }usort($same, "f3");

    function f4($a, $b) { // Sort by name
        return strcmp($a['name'],$b['name']);
    }usort($sameindrive, "f4");

    // For Same Filename
    for ($i = 0; $i < count($same); $i++) {
        if($same[$i]['folder']=="t"){
            sync($same[$i]["href"], $sameindrive[$i]["id"], $service);
            echo $same[$i]["name"]." is same folder\n";
        }elseif($same[$i]["modified"]>$sameindrive[$i]["modified"]) {
            echo $same[$i]["name"]." is same file\n";
            $file = davGET($same[$i]["url"], DAVUSERNAME, DAVPASS);
            if(md5($file)!=$sameindrive[$i]["md5"]){
                // todo: Update file in Drive
            }else{
                // todo: update modtime in Drive
            }
        }
    }

    // For Dav file not in Drive
    function f5($a,$b){
        return strcmp($a['name'], $b['name']);
    }$davfilesnotindrive = array_udiff($davarray, $drivearray, "f5");

    foreach($davfilesnotindrive as $keydav){
        if($keydav["folder"]=="t"){
            echo $keydav["name"]."is dav folder not in drive\n";
            // todo: check for moves, renames, both and then:
                // Insert folder into Drive,
                // write function to recursively insert contents(folders/files) into drive
        }else{
            echo $keydav["name"]."is dav file not in drive\n";
            // todo: check for moves, renames, both and then Insert new file into Drive
        }
    }

    // For Drive file not in Dav
    function f6($a,$b){
        return strcmp($a['name'], $b['name']);
    }$drivefilesnotindav = array_udiff($drivearray, $davarray, "f6");

    foreach($drivefilesnotindav as $keydrive){
        if($keydrive["folder"]=="t"){
            echo $keydrive["name"]."is drive folder not in dav\n";
            // todo: check for moves, renames, both and then delete from Drive
        }else{
            echo $keydrive["name"]."is drive file not in dav\n";
            // todo: check for moves, renames, both and then delete from Drive
        }
    }
}
// Start syncing
//sync(DAVURL, SYNCID, $service);
print_r(parsedavfolder(DAVURL));

// todo: create sql table to log last sync time
// todo: Uncomment line when done scripting; include "repeat.php";