<?php

namespace App\Library;


class TwoCheckout {

protected $params = [];
protected $_url;


function __construct()
{
    $this->_url = 'https://www.2checkout.com/checkout/purchase';
    $this->param('paypal_direct', 'Y');
    $this->param('mode', '2CO');
}

public function param($param, $value)
{
    $this->params["$param"] = $value;
}

public function gw_submit() {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Please wait while you're redirected</title>
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <style type="text/css">
        #redirect {
            background: #f1f1f1;
            font-family: Helvetica, Arial, sans-serif
        }

        #redirect-container {
            width: 410px;
            margin: 130px auto 0;
            background: #fff;
            border: 1px solid #b5b5b5;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border-radius: 5px;
            text-align: center
        }

        #redirect-container h1 {
            font-size: 22px;
            color: #5f5f5f;
            font-weight: normal;
            margin: 22px 0 26px 0;
            padding: 0
        }

        #redirect-container p {
            font-size: 13px;
            color: #454545;
            margin: 0 0 12px 0;
            padding: 0
        }

        #redirect-container img {
            margin: 0 0 35px 0;
            padding: 0
        }

        .ajaxLoader {
            margin: 80px 153px
        }
    </style>
    <script type="text/javascript">
        function timedText() {
            setTimeout('msg1()', 2000);
            setTimeout('msg2()', 4000);
            setTimeout('document.MetaRefreshForm.submit()', 4000);
        }

        function msg1() {
            document.getElementById('redirect-message').firstChild.nodeValue = "Preparing Data...";
        }

        function msg2() {
            document.getElementById('redirect-message').firstChild.nodeValue = "Redirecting...";
        }
    </script>
</head>
<?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
<div id="redirect-container">
    <h1>Please wait while you&rsquo;re redirected</h1>
    <p class="redirect-message" id="redirect-message">Loading Data...</p>
    <script type="text/javascript">timedText()</script>
</div>
<form name="gw" action="<?php echo $this->_url; ?>" method="POST">
    <?php
    foreach ($this->params as $name => $value) {
        echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
    }
    ?>
</form>
</body>
</html>
<?php }
}
