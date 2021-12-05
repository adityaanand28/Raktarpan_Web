<?php
	$url = "http://hrishirt.cse.iitk.ac.in/raktarpan/live/index.php";
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$output = curl_exec($curl);
	curl_close($curl);

	echo str_replace('./', 'http://hrishirt.cse.iitk.ac.in/raktarpan/live/', $output);
?>
