<?php
#error_reporting(E_ALL); ini_set('display_errors', '1');
require_once('./ElephantIO/Client.php');
use ElephantIO\Client as Elephant;
require_once('./AnyAPI.php');

$query = array(
	//'api_username' => 'iamanalyst.com',
	//'api_password' => '5300314e037a4',
	//'MODULE' => 'Customer',
	//'COMMAND' => 'view',
	//'FILTER[id]' => '10032389'
);

$AnyAPI = new anyapi( 'GET' , array('url' => 'http://trade.spotoption.com/PlatformAjax/getJsonFile/graph/regular/binary/-1440minutes/now/asset/2/graphData.spotgraph' ) , $query , 'array' , TRUE );

$AnyAPI->execute();
#header('Content-type: text/xml');
print('<pre>');
print_r($AnyAPI->results('url'));
print('</pre>');

#print('<pre>');
#print_r($AnyAPI);
#print('</pre>');

print('<pre>');
print_r($AnyAPI->returnDebug());
print('</pre>');
?>