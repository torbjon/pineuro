<?php
define("CACHE_PATH", $_SERVER['DOCUMENT_ROOT']."/cache/");
define("CACHE_URL_PATH", "/cache/");
function getjson(){
	$params = http_build_query(array_merge($_GET, array("wskey"=>"HTMQFSCKKB"));
	echo $params;
}
getjson();