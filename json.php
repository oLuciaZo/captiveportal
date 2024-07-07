<?php
error_reporting(0);
$token = $_POST['token'];
$pin = $_POST['pin'];
//echo $pin;
//$token = test();
if ( $pin == '1234' ) {
echo $pinn;
}?>
<table align='center'>
<FORM ACTION='json.php' METHOD='POST'>
<input type=hidden name=mobilenumber value=>
<input type=hidden name=devicemac value=>
<input type=hidden name=token value=>
<tr>
<td align='right'>
MAC of your device is 
</td>
<td>
</td>
</tr>
<tr>
<TD ALIGN='RIGHT'>
Your mobile phone number is 
</TD>
<TD ALIGN='LEFT'>
</TD>
</TR>
<TR>
<TD ALIGN='RIGHT'>
Enter OTP
</TD>
<TD ALIGN='LEFT'>
Name: <input type="text" name="pin" value="<?php echo $pin;?>">
</TD>
</TR>
<TR>
<TD ALIGN='CENTER' COLSPAN='2'>
<INPUT TYPE='SUBMIT' VALUE='REGISTER'>
</TD>
</TR>
</FORM>
</table>
<?php
function test(){
$json = '{"status":"success","message":"Code is correct."}';

$obj = json_decode($json);
return $obj->{'status'}; // 12345
}


?>
