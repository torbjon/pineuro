<?php
define("CACHE_PATH", $_SERVER['DOCUMENT_ROOT']."/cache/");
function getjson(){
	$data 		= "";
	$params 	= http_build_query(array_merge($_GET, array("wskey"=>"HTMQFSCKKB")));
	$filename = md5($params).".json";
	if(file_exists(CACHE_PATH.$filename)){
		$data 	= file_get_contents(CACHE_PATH.$filename);
	} else {
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL 						=> "http://api.europeana.eu/api/opensearch.json?".$params,
			CURLOPT_HEADER 					=> 0,
			CURLOPT_RETURNTRANSFER 	=> 1,
			CURLOPT_TIMEOUT 				=> 40,
			CURLOPT_FOLLOWLOCATION 	=> 1
		));
		$data 		= curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($httpcode >= 200 && $httpcode < 400):
			file_put_contents(CACHE_PATH.$filename, $data);
		endif;
	}
	echo $data;

	//(isset($_GET['callback'])?$_GET['callback']:"").$data.(isset($_GET['callback'])?")":"");
}
getjson();