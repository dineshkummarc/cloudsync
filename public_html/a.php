<?php
require("../includes/config.php");
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
?>