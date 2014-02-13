<?php
/**
 * Example file for AnyAPI
 * Shows an example of requesting data from a website. In this case we will use sample data.
 */
error_reporting(E_ALL); ini_set('display_errors', '1');
?><html>
    <head>
        <title>AnyAPI Example Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php 
            require_once('./AnyAPI.php');
        ?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    </body>
</html>