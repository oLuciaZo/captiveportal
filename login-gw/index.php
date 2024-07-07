<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>AccessShield Captive Portal</title>
    <link rel="shortcut icon" href="assets/images/fav.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/fav.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/plugins/slider/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/plugins/slider/css/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
</head>
<?php
require('routeros_api.class.php');
date_default_timezone_set('Asia/Bangkok');
$agent = $_SERVER['HTTP_USER_AGENT'];
if(preg_match('/Android/',$agent)) $os = 'Android';
          elseif(preg_match('/Linux/',$agent)) $os = 'Linux';
          elseif(preg_match('/Win/',$agent)) $os = 'Windows';
          elseif(preg_match('/Mac/',$agent)) $os = 'Mac';
          else $os = 'UnKnown';
//echo $os;
$ipaddr = $_SERVER['REMOTE_ADDR']; 
$mac = '';
$host = '';


    $API = new RouterosAPI();
if ($API->connect('2.2.2.2', 'api', 'password')) {

    //$ip = '192.168.88.234'; // Replace with your target IP

    // Query ARP table for specific IP
    $API->write('/ip/arp/print', false);
    $API->write('?address=' . $ipaddr, true);
    
    $read = $API->read();
    
    foreach ($read as $entry) {
        if (isset($entry['mac-address'])) {
            //echo "MAC Address: " . $entry['mac-address'] . "\n";
            $mac = $entry['mac-address'];
        } else {
            echo "MAC Address not found for IP: " . $ipaddr . "\n";
        }
    }

    $API->write('/ip/hotspot/host/print', false);
    $API->write('?address=' . $ipaddr, true);
        
        $read1 = $API->read();
        
        foreach ($read1 as $entry) {
            if (isset($entry['server'])) {
                //echo "MAC Address: " . $entry['mac-address'] . "\n";
                $host = $entry['server'];
                //echo $host;
            } else {
                echo "Host not Found "."\n";
            }
        }
    $info = $API->comm("/ip/hotspot/user/print", array(
        //".proplist" => ".id",
        "?name" => $mac,
        ));
    if($info){
    $API->comm('/ip/hotspot/user/remove', array(".id"=>$info[0]['.id'],));
    }else{

    }
    

    $API->disconnect();
} else {
    echo "Failed to connect to MikroTik API.";
}



?>
    <body class="form-login-body"> 
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 mx-auto login-desk">
                       <div class="row">
                            <div class="col-md-7 detail-box">
                                <img class="logo" src="assets/images/AccessShield.png" alt=""> 
                                <div class="detailsh">
                                     <img class="help" src="assets/images/help.png" alt="">
                                    <h3>24 x 7 Premium Chat Support</h3>
                                    <p>Authentication Gateway</p>
                                </div>
                            </div>
                            
                            <div class="col-md-5 loginform">
                                 <h4>Welcome Back</h4>                   
                                 <p>Signin to your Account</p>
                                 <div class="login-det">
                                 <form action="/login-gw/ldap.php" method="post">
                                    <div class="form-row">
                                         <label for="">Username</label>
                                             <div class="input-group mb-3">
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="far fa-user"></i>
                                                </span>
                                              </div>
                                              <input type="text" class="form-control" placeholder="Enter Username" aria-label="Username" name="username" aria-describedby="basic-addon1">
                                         </div>
                                    </div>
                                     <div class="form-row">
                                         <label for="">Password</label>
                                             <div class="input-group mb-3">
                                              <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                              </div>
                                              <input type="password" class="form-control" placeholder="Enter Password" aria-label="Username" name="password" aria-describedby="basic-addon1">
                                         </div>
                                    </div>
                                    
                                    
                                    <input type="hidden" name="devicemac" value=<?php echo $mac;?> >
                                    <input type="hidden" name="ipaddr" value=<?php echo $ipaddr;?> >
                                    <input type="hidden" name="os" value=<?php echo $os;?> >
                                    <input type="hidden" name="host" value=<?php echo $host;?> >
                                    
                                    <button class="btn btn-sm btn-danger">Login</button>
                                    </form>

                                    <form action="http://dev-captive.proexpertent.com:8000/deviceregister.php" method="post">
                                    <div class="form-row">
                                        
                                        <label for="">Register with mobile phone number</label>
                                            <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                               <span class="input-group-text" id="basic-addon1">
                                                   <i class="fas fa-lock"></i>
                                               </span>
                                             </div>
                                             <input type="tel" class="form-control" placeholder="Mobile Number" name="mobilenumber" aria-label="mobilenumber" aria-describedby="basic-addon1" pattern="[0-9]{3}[0-9]{3}[0-9]{4}">
                                             <input type="hidden" name="devicemac" value=<?php echo $mac;?> >
                                             <input type="hidden" name="ipaddr" value=<?php echo $ipaddr;?> >
                                             <input type="hidden" name="os" value=<?php echo $os;?> >
                                             <input type="hidden" name="host" value=<?php echo $host;?> >
                                        </div>
                                        <button class="btn btn-sm btn-danger">Send OTP</button>
                                    
                                   </div>
                                   </form>
                                   <p class="forget"><a href="">Register by Thai ID Card</a></p>
                                    <div class="social-link">
                                        <ul class="socil-icon">
                                            <li>
                                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                            </li>
                                            <li>
                                                <a href="#"><i class="fab fa-twitter"></i></a>
                                            </li>
                                            <li>
                                                <a href="#"><i class="fab fa-instagram"></i></a> 
                                            </li>
                                            <li>
                                                <a href="#"><i class="fab fa-pinterest-p"></i></a>
                                            </li>
                                            <li>
                                                <a href="#"><i class="fab fa-dribbble"></i></a>
                                            </li>
                                            <li>
                                                <a href="#"><i class="fab fa-behance"></i></a>
                                            </li>
                                       </ul>
                                    </div>
                                    
                                 </div>
                            </div>
                       </div>
                      
                    </div>
                </div>
            </div>
    </body>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/plugins/scroll-fixed/jquery-scrolltofixed-min.js"></script>
    <script src="assets/plugins/slider/js/owl.carousel.min.js"></script>
    <script src="assets/js/script.js"></script>
</html>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Free Tour and Travel Website Tempalte | Smarteyeapps.com</title>
    <link rel="shortcut icon" href="assets/images/fav.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/fav.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/plugins/slider/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/plugins/slider/css/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
</head>

    <body>
             
             
             
    </body>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/plugins/scroll-fixed/jquery-scrolltofixed-min.js"></script>
    <script src="assets/plugins/slider/js/owl.carousel.min.js"></script>
    <script src="assets/js/script.js"></script>
</html>
