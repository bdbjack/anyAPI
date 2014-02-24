<?php
#error_reporting(E_ALL); ini_set('display_errors', '1');
require_once('./ElephantIO/Client.php');
use ElephantIO\Client as Elephant;
require_once('./AnyAPI.php');

$AnyAPI = new anyapi( 'GET' , array('url' => 'http://www.maxmind.com/geoip/v2.0/city_isp_org/8.8.8.8' ) , array('demo' => true ) , 'array' , TRUE );

$AnyAPI->setOptions(array('url' => 'http://www.maxmind.com/geoip/v2.0/city_isp_org/8.8.8.8' ));

$AnyAPI->execute();

#print('<pre>');
print_r($AnyAPI->results('json'));
#print('</pre>');

#print('<pre>');
#print_r($AnyAPI);
#print('</pre>');

#print('<pre>');
#print_r($AnyAPI->returnDebug());
#print('</pre>');
#header('Content-type: text/csv');
?>