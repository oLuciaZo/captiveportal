<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<body>
<?php
error_reporting(0);
  require('routeros_api.class.php');
  date_default_timezone_set('Asia/Bangkok');
  $agent = $_SERVER['HTTP_USER_AGENT'];
  $timestamp = date('Y-m-d H:i:s');
  $devicemac = $_POST['devicemac'];
  $mobilenumber = $_POST['mobilenumber'];
  $host = $_POST['host'];
  $ipaddr = $_POST['ipaddr'];
  $idcard = '1';
  //$return_status = "success";
  $token = $_POST['token'];
  //$token = 1;
  //$pin = 1234;
  $pin = $_POST['pin'];
  $invalid_otp = 0;
      $key = "1802024959584782";
      $secret = "1045b1a9b56775a2ca83ebd6bab6a48b";
      $line_token = "NBjihKJRKG9IafFDySiQjaOiaFsQZnaowY1RE30SSEU";
      $user_profile = "default";
  if ( empty ( $devicemac ) || empty( $mobilenumber) ) {
    print "No device mac address or mobile phone number.";
    echo "<script> location.href='http://1.2.3.4'; </script>";
    exit;
  } else {
    if ( empty($token) ) {
      $token = sendotp($key, $secret, $mobilenumber);
      $message = "Mobile Number " . $mobilenumber . "\nRequest OTP for MAC : " . $devicemac;
      //linenotify($line_token, $message);
      
    } else {
//        echo "Checktoken: ".$token;
//  echo"<br>";
//  echo "CheckPIN: ".$pin;
      $return_status = checkotp($key, $secret, $token, $pin);
      if ( $return_status == 'success' ) {
        $API = new RouterosAPI();
        $API->debug = false;
        $comment = "Register Tel  : " . $mobilenumber . ", Register Date : " . $timestamp;
        if ($API->connect( '2.2.2.2', 'api', 'password')) {
          $ARRAY = $API->comm("/ip/hotspot/user/add", array(
            "name" => $devicemac,
            "password" => $devicemac,
            "mac-address" => $devicemac,
            "profile" => $user_profile,
            "limit-uptime" => '5m',
            "comment" => $comment
          ));
          //linenotify($line_token, $comment);

          // ค้นหาและ Remove MAC Address ออกจาก /ip hotspot host ของระบบ
          $arrID = $API->comm("/ip/hotspot/host/getall", array(
            ".proplist"=> ".id",
            "?mac-address" => $devicemac,
          ));
          //linenotify($line_token, $arrID[0]);
          $API->comm("/ip/hotspot/host/remove", array(
            ".id" => $arrID[0][".id"],
          ));

          // ยกเลิกการเชื่อมต่อกับ API
          $API->disconnect();
          if(preg_match('/Android/',$agent)) $os = 'Android';
          elseif(preg_match('/Linux/',$agent)) $os = 'Linux';
          elseif(preg_match('/Win/',$agent)) $os = 'Windows';
          elseif(preg_match('/Mac/',$agent)) $os = 'Mac';
          else $os = 'UnKnown';

          $message = "Register success for MAC : " . $devicemac . "\nFrom : " . $mobilenumber . "OS :" . $os;
          linenotify($line_token, $message);
          updatedb($devicemac, $ipaddr, $mobilenumber, $os, $idcard, $timestamp, $host);
          echo "<script> location.href='https://www.google.co.th'; </script>";
          exit;
          
        } else {
          print "ERROR connect to Mikrotik";
        }
        print "Register success.<BR>";
        echo "<script> location.href='https://www.google.co.th'; </script>";
        exit;
      } else {
        $invalid_otp = 1;
      }
    }
    print "<table align='center'>";
      print "<FORM ACTION='deviceregister.php' METHOD='POST'>";
        print "<input type=hidden name=mobilenumber value=$mobilenumber>";
        print "<input type=hidden name=devicemac value=$devicemac>";
        print "<INPUT TYPE=hidden NAME=token VALUE=$token>";
        if ( $invalid_otp == 1) {
          print "<TR>";
            print "<TD ALIGN='CENTER' COLSPAN='2'>";
              print "<FONT COLOR='RED'>Invalid OTP please enter again.</FONT>";
            print "</TD>";
          print "</TR>";
          print "<TR>";
            print "<TD ALIGN='CENTER' COLSPAN='2'>";
              print "<FONT COLOR='RED'>หมายเลข OTP ไม่ถูกต้อง กรุณาใส่ใหม่อีกครั้ง.</FONT>";
            print "</TD>";
          print "</TR>";
        }
        print "<tr>";
          print "<td align='right'>";
            print "MAC of your device is ";
          print "</td>";
          print "<td>";
            print $devicemac;
          print "</td>";
        print "</tr>";
        print "<tr>";
          print "<TD ALIGN='RIGHT'>";
            print "Your mobile phone number is ";
          print "</TD>";
          print "<TD ALIGN='LEFT'>";
            print $mobilenumber;
          print "</TD>";
        print "</TR>";
        print "<TR>";
            print "<TD ALIGN='RIGHT'>";
                print "Enter OTP";
            print "</TD>";
            print "<TD ALIGN='LEFT'>";
                print "<INPUT TYPE='TEXT' NAME='pin' PLACEHOLDER='OTP number' AUTOFOCUS>";
            print "</TD>";
        print "</TR>";
        print "<TR>";
          print "<TD ALIGN='CENTER' COLSPAN='2'>";
            print "<INPUT TYPE='SUBMIT' VALUE='REGISTER'>";
          print "</TD>";
        print "</TR>";
      print "</FORM>";
    print "</table>";
  }

  function sendotp($key, $secret, $phone) {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://otp.thaibulksms.com/v2/otp/request",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array('key' => $key,'secret' => $secret,'msisdn' => $phone),
        CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache"
        ),
      ));
      $response = curl_exec($curl);
  //echo $response;
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        $jsondata = json_decode($response);
        return $jsondata->{'token'};
      }
  }

    function checkotp ($key, $secret, $token, $pin) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://otp.thaibulksms.com/v2/otp/verify",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => array('key' => $key,'secret' => $secret,'token' => $token,'pin' => $pin),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
      ),
    ));

    $response = curl_exec($curl);
  echo $response;
    $err = curl_error($curl);

    curl_close($curl);
    
    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      $jsondata = json_decode($response);
        return $jsondata->{'status'};

    }
  }

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

  function updatedb($mac, $ipaddr, $mobilenumber, $os, $idcard, $timestamp, $host){
    $servername = '192.168.48.2';
    $username = 'root';
    $password = 'radiusrootdbpw';
    $dbname = 'backend';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Data to be inserted
    $c_ip = $ipaddr;
    $c_mac = $mac;
    $c_os = $os;
    $c_idcard = $idcard;
    $c_phone = $mobilenumber;
    $c_time = $timestamp;
    $c_tennant = $host;

    // SQL query to insert data
    $sql = "INSERT INTO be_client (c_ip, c_mac, c_os, c_idcard, c_phone, c_time, c_tennant) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare statement
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssss", $c_ip, $c_mac, $c_os, $c_idcard, $c_phone, $c_time, $c_tennant);

    // Execute statement
    if ($stmt->execute()) {
        echo "Record inserted successfully!";
    } else {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
  }
?>
</body>
</html>
