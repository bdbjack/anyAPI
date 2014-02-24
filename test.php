<?php
#error_reporting(E_ALL); ini_set('display_errors', '1');
require_once('./ElephantIO/Client.php');
use ElephantIO\Client as Elephant;
require_once('./AnyAPI.php');

$AnyAPI = new anyapi( 'POST' , array('url' => 'http://127.0.0.1/postTest.php' ) , array('demo' => true ) , 'array' , TRUE );

$AnyAPI->execute();

print('<pre>');
print_r($AnyAPI->results('raw'));
print('</pre>');

#print('<pre>');
#print_r($AnyAPI);
#print('</pre>');

#print('<pre>');
#print_r($AnyAPI->returnDebug());
#print('</pre>');
#header('Content-type: text/csv');
?>