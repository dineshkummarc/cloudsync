<?php

    /***********************************************************************
     * functions.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Helper functions.
     **********************************************************************/

    require_once("constants.php");

    // Generates a random alpha-numeric string of given length
    function Random($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    

    //Send mail through http
    function mailgun($to,$subject,$htmlmsg,$textmsg){
        date_default_timezone_set(TIMEZONE);
        $mg_version = 'api.mailgun.net/v2/';
        $mg_domain = MGDOMAIN;
        $mg_message_url = "https://".$mg_version.$mg_domain."/messages";

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => false,
            CURLOPT_HEADER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERPWD => 'api:' . APIKEY,
            CURLOPT_POST => true,
            CURLOPT_URL => $mg_message_url,
            CURLOPT_POSTFIELDS => array(
                'from'      => 'CloudSync Inc. <no-reply@cloudsync.site90.net>',
                'to'        => $to,
                'h:Reply-To'=>  '<no-reply@cloudsync.site90.net>',
                'subject'   => $subject,
                'text'    => $textmsg,
                'html'    => $htmlmsg
            )
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result,TRUE);
        print_r($res);
    }


    // Webdav PROPFIND request
    function davPROP($url, $user, $password){
        $curl = curl_init(str_replace(' ', '%20',$url));
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $user.":".$password,
            CURLOPT_HEADER => true,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_CUSTOMREQUEST => 'PROPFIND',
            CURLOPT_HTTPHEADER => array('Depth:1','PROPFIND  /file HTTP/1.1','Content-Type: application/xml')//'Depth:infinity'
        ));
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $array = array("result" => $result, "info" => $info);
        return $array;
    }


    // Webdav GET request
    function davGET($url, $user, $password){
        $curlget = curl_init(str_replace(' ', '%20',$url));
        curl_setopt_array($curlget, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $user.":".$password,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_HTTPHEADER => array("GET  /file HTTP/1.1")
        ));
        $result = curl_exec($curlget);
        curl_close($curlget);
        return $result;
    }

/**
 * convert xml string to php array - useful to get a serializable value
 *
 * @param string $xmlstr
 * @return array
 * @author Adrien aka Gaarf
 */
function xmlstr_to_array($xmlstr) {
    $xmlstr = str_replace('"DAV:"','"DAV"',$xmlstr);
    $doc = new DOMDocument();
    $doc->loadXML($xmlstr);
    return domnode_to_array($doc->documentElement);
}
function domnode_to_array($node) {
    $output = array();
    switch ($node->nodeType) {
        case XML_CDATA_SECTION_NODE:
        case XML_TEXT_NODE:
            $output = trim($node->textContent);
            break;
        case XML_ELEMENT_NODE:
            for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                $child = $node->childNodes->item($i);
                $v = domnode_to_array($child);
                if(isset($child->tagName)) {
                    $t = $child->tagName;
                    if(!isset($output[$t])) {
                        $output[$t] = array();
                    }
                    $output[$t][] = $v;
                }
                elseif($v) {
                    $output = (string) $v;
                }
            }
            if(is_array($output)) {
                if($node->attributes->length) {
                    $a = array();
                    foreach($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    $output['@attributes'] = $a;
                }
                foreach ($output as $t => $v) {
                    if(is_array($v) && count($v)==1 && $t!='@attributes') {
                        $output[$t] = $v[0];
                    }
                }
            }
            break;
    }
    return $output;
}

//


    //Apologizes to user with message.

    function apologize($message=null,$page='apology',$arg=array())
    {
        render("apology.php", array("message" => $message, "page" => $page, "arg" => $arg));
        exit;
    }

    
    //Logs out current user, if any.  Based on Example #1 at
    //http://us.php.net/manual/en/function.session-destroy.php.
    function logout()
    {
        // unset any session variables
        $_SESSION = array();

        // expire cookie
        if (!empty($_COOKIE[session_name()]))
        {
            setcookie(session_name(), "", time() - 42000);
        }

        // destroy session
        session_destroy();
    }


    
    //Executes SQL statement, possibly with parameters, returning
    //an array of all rows in result set or false on (non-fatal) error.
    
    function query(/* $sql [, ... ] */)
    {
        // SQL statement
        $sql = func_get_arg(0);

        // parameters, if any
        $parameters = array_slice(func_get_args(), 1);

        // try to connect to database
        static $handle;
        if (!isset($handle))
        {
            try
            {
                // connect to database
                $handle = new PDO("mysql:dbname=" . DATABASE . ";host=" . SERVER, USERNAME, PASSWORD);

                // ensure that PDO::prepare returns false when passed invalid SQL
                $handle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 
            }
            catch (Exception $e)
            {
                // trigger (big, orange) error
                trigger_error($e->getMessage(), E_USER_ERROR);
                exit;
            }
        }

        // prepare SQL statement
        $statement = $handle->prepare($sql);
        if ($statement === false)
        {
            // trigger (big, orange) error
            $eI = $handle->errorInfo();
            $thirdElement = $eI[2];
            trigger_error($thirdElement, E_USER_ERROR);
            exit;
        }

        // execute SQL statement
        $results = $statement->execute($parameters);

        // return result set's rows, if any
        if ($results !== false)
        {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            return false;
        }
    }

    /**
     * Redirects user to destination, which can be
     * a URL or a relative path on the local host.
     *
     * Because this function outputs an HTTP header, it
     * must be called before caller outputs any HTML.
     */
    function redirect($destination)
    {
        // handle URL
        if (preg_match("/^https?:\/\//", $destination))
        {
            header("Location: " . $destination);
        }

        // handle absolute path
        else if (preg_match("/^\//", $destination))
        {
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            header("Location: $protocol://$host$destination");
        }

        // handle relative path
        else
        {
            // adapted from http://www.php.net/header
            $protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
            $host = $_SERVER["HTTP_HOST"];
            $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
            header("Location: $protocol://$host$path/$destination");
        }

        // exit immediately since we're redirecting anyway
        exit;
    }

    /**
     * Renders template, passing in values.
     */
    function render($template, $values = array())
    {
        // if template exists, render it
        if (file_exists("../templates/$template"))
        {
            // extract variables into local scope
            extract($values);

            // render header
            require("../templates/header.php");

            // render template
            require("../templates/$template");

            // render footer
            require("../templates/footer.php");
        }

        // else err
        else
        {
            trigger_error("Invalid template: $template", E_USER_ERROR);
        }
    }

?>
