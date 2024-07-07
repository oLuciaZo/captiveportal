<?php
require('routeros_api.class.php');
$devicemac = $_POST['devicemac'];
$c_user = $_POST['username'];
$c_pass = $_POST['password'];
$ipaddr = $_POST['ipaddr'];
$host = $_POST['host'];
$os = $_POST['os'];
/*echo "<br>";
echo $devicemac;
echo "<br>";
echo $c_user;
echo "<br>";
echo $os;
echo "<br>";
echo $host;*/
$line_token = "NBjihKJRKG9IafFDySiQjaOiaFsQZnaowY1RE30SSEU";
$user_profile = "default";
$timestamp = date('Y-m-d H:i:s');

function linenotify($token, $message) {
    $authen="Authorization: Bearer " . $token;
    $timestamp = date('Y-m-d H:i:s');
    $curlcode = curl_init();
    curl_setopt( $curlcode, CURLOPT_URL, "https://notify-api.line.me/api/notify");
    curl_setopt( $curlcode, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt( $curlcode, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt( $curlcode, CURLOPT_POST, 1);
    curl_setopt( $curlcode, CURLOPT_POSTFIELDS, "message=" . $message . "\nDATE : " . $timestamp);
    curl_setopt( $curlcode, CURLOPT_FOLLOWLOCATION, 1);
    $headers = array( 'Content-type: application/x-www-form-urlencoded', $authen, );
    curl_setopt($curlcode, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $curlcode, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec( $curlcode );
    if (curl_error($curlcode)) {
      echo 'error:' . curl_error($curlcode);
    }
    curl_close( $curlcode );
  }

function ntlmHash($password,$ntlmHash) {
    $encode = strtoupper(hash('md4', iconv('UTF-8', 'UTF-16LE', $password)));
    if($ntlmHash===$encode){
        return "pass";
    }else{
        return "no pass";
    }

}


function checkuser($c_user){
    $servername = "10.20.22.220";
    $username = "root";
    $password = "p@ck3tf3nc3";
    $dbname = "pf";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    
    //echo "SELECT * FROM password WHERE pid = {$user}";
    $stmt = $conn->prepare("SELECT * FROM password WHERE pid = ?");
    $stmt->bind_param("s", $c_user);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            //echo "Name: " . $row["pid"] . " - Pass: " . substr($row["password"],6) . "<br>";
            return substr($row["password"],6);
        }
    } else {
        echo "0 results";
    }
    
    $stmt->close();
    $conn->close();
    }


$ntlm = checkuser($c_user);
$return_status = ntlmHash($c_pass, $ntlm);

//echo $comment = "User Authen  : " . $c_user . ", Register Date : " . $timestamp;



if ( $return_status == 'pass' ) {
    $API = new RouterosAPI();
    $API->debug = false;
    $comment = "User Authen  : " . $c_user . ", Register Date : " . $timestamp;
    if ($API->connect( '2.2.2.2', 'api', 'password')) {
      $ARRAY = $API->comm("/ip/hotspot/user/add", array(
        "name" => $devicemac,
        "password" => $devicemac,
        "mac-address" => $devicemac,
        "profile" => $user_profile,
        "limit-uptime" => '1m',
        "comment" => $comment
      ));
      //linenotify($line_token, $comment);

      // ค้นหาและ Remove MAC Address ออกจาก /ip hotspot host ของระบบ
      $arrID = $API->comm("/ip/hotspot/host/print", array(
        ".proplist"=> ".id",
        "?mac-address" => $devicemac,
      ));
      
      //linenotify($line_token, $arrID[0]);
      $API->comm("/ip/hotspot/host/remove", array(
        ".id" => $arrID[0][".id"],
      ));

      // ยกเลิกการเชื่อมต่อกับ API
      $API->disconnect();

      $message = "Register success for MAC : " . $devicemac . "\n OS :" . $os;
      //linenotify($line_token, $message);
      //updatedb($devicemac, $ipaddr, $mobilenumber, $os, $idcard, $timestamp, $host);
      
    } else {
      print "ERROR connect to Mikrotik";
    }
    print "Register success.<BR>";
    echo "<script> location.href='https://www.google.co.th'; </script>";
    exit;
  } else {
    echo "<script> location.href='http://1.2.3.4'; </script>";
    exit;
    //echo "Username Password Incorrect";
  }


?>