<?php
// Sanitize input (if needed)
//$ldaprdn = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$ldapuser = $_POST['username'].'@arubathailand.xyz';
$ldappass = $_POST['password']; // Password should not be sanitized
$ldapconn = ldap_connect("ldap://10.5.255.252") or die("Could not connect to LDAP server.");
//echo $ldaprdn;
$pos = strpos($ldapuser, '@');

// Extract the substring from the start to the position of '@'
$username = substr($ldapuser, 0, $pos);
if (!$ldapconn) {
die("Could not connect to LDAP server.");
}

ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

$ldapbind = ldap_bind($ldapconn, $ldapuser, $ldappass);

if ($ldapbind) {
echo "LDAP bind successful...<br>";

    // Perform LDAP search
$result = ldap_search($ldapconn, "dc=arubathailand,dc=xyz", "(samaccountname=$username)", array("dc"));

		if ($result) {
			$data = ldap_get_entries($ldapconn, $result);

		if ($data['count'] > 0) {
			echo "Search successful:<br>";
			print_r($data);
		} else {
			echo "User not found.";
		}
		} else {
			echo "LDAP search failed.";
		}
		} else {
			echo "LDAP bind failed...";
		}
?>