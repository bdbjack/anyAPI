<?php
/**
 * Example file for AnyAPI
 * Shows an example of requesting data from a website. In this case we will use sample data.
 */
error_reporting(E_ALL); ini_set('display_errors', '1');
require_once('./ElephantIO/Client.php');
use ElephantIO\Client as Elephant;
require_once('./AnyAPI.php');
?><html>
    <head>
        <title>AnyAPI Example Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>AnyAPI Framework</h1>
                    <p>Example Page</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <h3>Code</h3>
                        </div>
                        <div class="col-lg-6">
                            <h3>Return</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="codeSection" id="checkRequirements"><<!-- break -->?php 
print_r(anyapi::checkRequirements());
?<!-- break -->></div>
                        </div>
                        <div class="col-lg-6">
                            <div class="codeResult" id="checkRequirementsResult"><?php
                            print_r(anyapi::checkRequirements());
                            ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="codeSection" id="installInfo" style="height:500px;"><<!-- break -->?php 
print_r(anyapi::installInfo());
?<!-- break -->></div>
                        </div>
                        <div class="col-lg-6">
                            <div class="codeResult" id="installInfoResult" style="height:500px;"><?php
                            print_r(anyapi::installInfo());
                            ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.01/ace.js" type="text/javascript" charset="utf-8"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.01/mode-php.js" type="text/javascript" charset="utf-8"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.1.01/theme-chrome.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
        var codeSections = {};
        jQuery('.codeSection').each(function() {
            var id = jQuery(this).attr('id');
            codeSections['"' + id + '"'] = ace.edit(id);
            codeSections['"' + id + '"'].getSession().setUseWorker(false);
            codeSections['"' + id + '"'].setTheme("ace/theme/chrome");
            codeSections['"' + id + '"'].getSession().setMode("ace/mode/php");
            codeSections['"' + id + '"'].setReadOnly(true); 
        });
        jQuery('.codeResult').each(function() {
            var id = jQuery(this).attr('id');
            codeSections['"' + id + '"'] = ace.edit(id);
            codeSections['"' + id + '"'].getSession().setUseWorker(false);
            codeSections['"' + id + '"'].setTheme("ace/theme/chrome");
            codeSections['"' + id + '"'].getSession().setMode("ace/mode/php");
            codeSections['"' + id + '"'].setReadOnly(true); 
        });
        </script>
        <style type="text/css" media="screen">
            .codeSection {
                min-height: 100px;
            }
            .codeResult {
                min-height: 100px;
            }

            .row {
                border-bottom: solid 5px #000;
            }
        </style>
    </body>
</html>