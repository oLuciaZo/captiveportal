<?php
  require('routeros_api.class.php');
$API = new RouterosAPI();
        $API->debug = false;
        if ($API->connect( '2.2.2.2', 'api', 'password')) {
          $ARRAY = $API->comm("/ip/hotspot/user/add", array(
            "name" => 'test01',
            "password" => 'password',
            "profile" => 'default',
	    "limit-uptime" => '5m',
            "comment" => 'add by api'
          ));
          // ยกเลิกการเชื่อมต่อกับ API
          $API->disconnect();
        }
