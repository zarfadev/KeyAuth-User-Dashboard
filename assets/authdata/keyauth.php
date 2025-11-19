<?php

namespace KeyAuth;

session_start();

class api
{
    public $name, $OwnerId, $version;

    public $numUsers, $numKeys, $numOnlineUsers, $customerPanelLink;

    function __construct($name, $OwnerId, $version)
    {
        $this->name = $name;
        $this->OwnerId = $OwnerId;
        $this->version = $version;
    }

    function init()
    {
        if ($this->name == "" || $this->OwnerId == "" || $this->version == "") {
            die("Please set your application name, ownerid and version in credentials.php");
        }

        $data = array(
            "type" => "init",
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $response = $this->req($data);

        if ($response == "KeyAuth_Invalid") {
            die("Application not found");
        }

        $json = json_decode($response);

        if ($json->message == "This program hash does not match, make sure you're using latest version") {
            $message = "This Application have hash check enabled, Please Disable it so you can access this.";
            die($message); // Dies + Prints Error Message.
        }

        if (!$json->success)
            die($json->message);
        else if ($json->success) {
            $_SESSION['sessionid'] = $json->sessionid;
            $this->numUsers = $json->appinfo->numUsers;
            $this->numKeys = $json->appinfo->numKeys;
            $this->numOnlineUsers = $json->appinfo->numOnlineUsers;
            $this->customerPanelLink = $json->appinfo->customerPanelLink;
        }
    }

    function login($username, $password)
    {
        $data = array(
            "type" => "login",
            "username" => $username,
            "pass" => $password,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        } else if ($json->success)
            $_SESSION["user_data"] = (array) $json->info;

        return $json->success;
    }

    function register($username, $password, $key)
    {
        $data = array(
            "type" => "register",
            "username" => $username,
            "pass" => $password,
            "key" => $key,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        } else if ($json->success)
            $_SESSION["user_data"] = (array) $json->info;

        return $json->success;
    }

    function license($key)
    {
        $data = array(
            "type" => "license",
            "key" => $key,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        } else if ($json->success)
            $_SESSION["user_data"] = (array) $json->info;

        return $json->success;
    }

    function upgrade($username, $key)
    {
        $data = array(
            "type" => "upgrade",
            "username" => $username,
            "key" => $key,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        }

        // don't allow them to dashboard yet, upgrade doesn't require password so they need to login after register

        return $json->success;
    }

    function var ($varid)
    {
        $data = array(
            "type" => "var",
            "varid" => $varid,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            unset($_SESSION['sessionid']);
            $this->error($json->message);
        } else if ($json->success)
            return $json->message;
    }

    function log($message)
    {
        $User = gethostname();

        $data = array(
            "type" => "log",
            "pcuser" => $User,
            "message" => $message,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $this->req($data);
    }

    function setvar($varname, $data)
    {
        $data = array(
            "type" => "setvar",
            "var" => $varname,
            "data" => $data,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $this->req($data);
    }

    function getvar($varid)
    {
        $data = array(
            "type" => "getvar",
            "var" => $varid,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            return null;
        } else if ($json->success)
            return $json->response;
    }

    function webhook($webid, $param, $body = "", $conttype = "")
    {
        $data = array(
            "type" => "webhook",
            "webid" => $webid,
            "params" => $param,
            "body" => $body,
            "conttype" => $conttype,
            "sessionid" => $_SESSION['sessionid'],
            "name" => $this->name,
            "ownerid" => $this->OwnerId
        );

        $response = $this->req($data);

        $json = json_decode($response);

        if (!$json->success) {
            return null;
        } else if ($json->success)
            return $json->response;
    }

    private function req($data)
    {
        $curl = curl_init("https://keyauth.win/api/1.2/");
        curl_setopt($curl, CURLOPT_USERAGENT, "KeyAuth");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function error($msg)
    {
        echo '
                <script type=\'text/javascript\'>
                
                const notyf = new Notyf();
                notyf
                  .error({
                    message: \'' . addslashes($msg) . '\',
                    duration: 3500,
                    dismissible: true
                  });                
                
                </script>
                ';
    }
}
?>