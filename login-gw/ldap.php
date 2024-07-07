<?php
	
	$ldap_dn = "cn=sitita,dc=arubathailand,dc=xyz";
	$ldap_password = "password";
	
	$ldap_con = ldap_connect("10.5.255.252");
	
	ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
	
	if(ldap_bind($ldap_con, $ldap_dn, $ldap_password)) {

		$filter = "(cn=sitita)";
		$result = ldap_search($ldap_con,"dc=arubathailand,dc=xyz",$filter) or exit("Unable to search");
		$entries = ldap_get_entries($ldap_con, $result);
		
		print "<pre>";
		print_r ($entries);
		print "</pre>";
	} else {
		echo "Invalid user/pass or other errors!";
	}
	
	
?>