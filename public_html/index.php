<?php
// configuration
require("../includes/config.php");

// todo: upgrade mcrypt_ecb()
$clouds=query("select * from clouds where id = ?", $_SESSION["id"]);
if(!empty($clouds) && isset($_SESSION["cloud"])){
    foreach($clouds as $key){
        if($key["number"]==$_SESSION["cloud"]) $thiscloud=$key; // Define $thiscloud
    }

    if($thiscloud["cloudnum"]==1){ // If Webdav
        $url = (isset($_GET["url"]))?$_GET["url"]:$thiscloud["url"]; // rawurlencode();

        if(isset($_GET["file"])){ // If Webdav File
            $prop = davPROP($url, $thiscloud["username"], mcrypt_ecb(MCRYPT_3DES, MCRYPTKEY, $thiscloud["password"], MCRYPT_DECRYPT));
            $status = $prop["info"]["http_code"];
            if($status<400){
                $xml = xmlstr_to_array(strstr($prop["result"], '<?xml'));
                $xml = $xml['d:response'];
                $fname = $xml["d:propstat"]["d:prop"]["d:displayname"];
                $type = $xml["d:propstat"]["d:prop"]["d:getcontenttype"];

                $get = davGET($url, $thiscloud["username"], mcrypt_ecb(MCRYPT_3DES, MCRYPTKEY, $thiscloud["password"], MCRYPT_DECRYPT));
                header("Content-disposition: attachment; filename=$fname");
                header("Content-type: $type");
                echo $get;
                exit;
            }
            else apologize("Error $status - An unexpected error occurred.");


        }else{ // If Webdav Folder
            $prop = davPROP($url, $thiscloud["username"], mcrypt_ecb(MCRYPT_3DES, MCRYPTKEY, $thiscloud["password"], MCRYPT_DECRYPT));
            $status = $prop["info"]["http_code"];
            if($status<400){
                if (strpos($prop["result"], '<?xml') !== false) {
                    $xml = xmlstr_to_array(strstr($prop["result"], '<?xml'));
                    $xml = $xml['d:response'];
                    foreach ($xml as $row){
                        $positions[] = array(
                            "folder" => (isset($row['d:propstat']['d:prop']['d:isFolder']))?"t":"f",
                            "name" => $row['d:propstat']['d:prop']['d:displayname'],
                            "size" => (isset($row['d:propstat']['d:prop']['d:getcontentlength']))?$row['d:propstat']['d:prop']['d:getcontentlength']:"f",
                            "modified" => strtotime($row['d:propstat']['d:prop']['d:getlastmodified'])+LOCALTIME,
                            "href" => $row['d:href']
                        );
                    }if(!isset($_GET["url"])) $positions[0]["name"] = "Home";
    
                    render("home.php", array("title"=>(isset($positions[0]["name"]))?$positions[0]["name"]:"", "positions"=>$positions, "clouds"=>$clouds, "thiscloud"=>$thiscloud));
                }
                else apologize("No xml data returned.");
            }
            else apologize("Error $status - An unexpected error occurred.");
        }
    }


    elseif($thiscloud["cloudnum"]==2){ // If Google Drive
        require("../includes/gclient.php");
        $client->setAccessToken($_SESSION['token']);
        $service = new Google_DriveService($client);
        $urlid=(isset($_GET["id"]))?$_GET["id"]:"root";

        if(isset($_GET["file"])){  // If Google Drive File
            $file = $service->files->get($urlid);
            try{
                if($file->getMimeType() == "application/vnd.google-apps.document"){ // Google Document
                    $link = $file->getExportLinks();
                    $link = $link["application/vnd.openxmlformats-officedocument.wordprocessingml.document"];
                    redirect($link);
                }elseif($file->getMimeType() == "application/vnd.google-apps.spreadsheet"){ // Google Spreadsheet
                    $link = $file->getExportLinks();
                    $link = $link["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
                    redirect($link);
                }elseif($file->getMimeType() == "application/vnd.google-apps.presentation"){ // Google Slide
                    $link = $file->getExportLinks();
                    $link = $link["application/vnd.openxmlformats-officedocument.presentationml.presentation"];
                    redirect($link);
                }elseif($file->getMimeType() == "application/vnd.google-apps.drawing"){ // Google Drawing
                    $link = $file->getExportLinks();
                    $link = $link["image/png"];
                    redirect($link);
                }elseif(strpos($file->getMimeType(), "application/vnd.google-apps") !== false){ // Other Google File
                    $filetypes=array(
                        "application/vnd.google-apps.audio" => "Audio",
                        "application/vnd.google-apps.document" => "Document",
                        "application/vnd.google-apps.drawing" => "Drawing",
                        "application/vnd.google-apps.file" => "Google",
                        "application/vnd.google-apps.folder" => "Google Folder",
                        "application/vnd.google-apps.form" => "Google Form",
                        "application/vnd.google-apps.fusiontable" => "Fusion Table",
                        "application/vnd.google-apps.photo" => "Photo",
                        "application/vnd.google-apps.presentation" => "Presentation",
                        "application/vnd.google-apps.script" => "Script",
                        "application/vnd.google-apps.sites" => "Google Site",
                        "application/vnd.google-apps.spreadsheet" => "Spreadsheet",
                        "application/vnd.google-apps.unknown" => "Unknown",
                        "application/vnd.google-apps.video" => "Video"
                    );
                    apologize($filetypes[$file->mimeType]." files cannot be downloaded.");
                }else{ // Normal File
                    $link = $file->getWebContentLink();
                    redirect($link);
                }
            }catch (Exception $e){
                apologize("An error occurred: " . $e->getMessage());
            }
        }
        else{ // If Google Drive Folder
            try {
                $children = $service->children->listChildren($urlid, array("q"=>"trashed != true"));
                foreach ($children->items as $child) {
                    $item = $service->files->get((string)$child->id);
                    $positions[] = array(
                        "folder" => ((string)$item->getMimeType()=="application/vnd.google-apps.folder")?"t":"f",
                        "name" => $item->getTitle(),
                        "size" => (strpos($item->getMimeType(), "application/vnd.google-apps") !== false)?"f":$item->getFileSize(),
                        "modified" => strtotime($item->getModifiedDate()),
                        "id" => $item->getId(),
                        "icon" => $item->getIconLink()
                    );
                }
                $title = $service->files->get($urlid)->title;

                $tmpurlid=$urlid; // Breadcrumbs
                do{
                    $folder = $service->files->get($tmpurlid);
                    $crumbs[] = array("title" => $folder->getTitle(), "id" => $folder->getId());
                    if(!empty($folder->parents)) {
                        $tmpurlid = $folder->getParents();
                        $tmpurlid = (string)$tmpurlid[0]->id;
                    }
                }while(!empty($folder->parents));
                $crumbs = array_reverse($crumbs);
            } catch (Exception $e) {
                apologize("An error occurred: " . $e->getMessage());
            }

            function cmp($a, $b) { // Sort by folder/file
                return strcmp($b['folder'],$a['folder']);
            } usort($positions, "cmp");

            render("home.php", array("title"=>(isset($title))?$title:"", "positions"=>$positions, "clouds"=>$clouds, "thiscloud"=>$thiscloud, "crumbs"=>$crumbs));
        }
    }
}
else {
    render("menu.php", array("title"=>"Home", "clouds"=>$clouds));
}
?>