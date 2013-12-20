<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="zchat">
    <meta name="author" content="shenzhe">
    <link rel="shortcut icon" href="<?php echo $static_url;?>ico/favicon.png">
    <title>ZChat聊天室</title>
    <!-- Bootstrap core CSS -->
    <link href="<?php echo $static_url;?>css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo $static_url;?>css/sign.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="<?php echo $static_url;?>ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo $static_url;?>js/html5shiv.min.js"></script>
    <script src="<?php echo $static_url;?>js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <a href="https://github.com/shenzhe/zchat" target="_blank">
    <img style="position: absolute; top: 0; right: 0; border: 0; z-index:9999;" src="http://www.swoole.com/static/image/forkme_right_orange_ff7600.png" alt="Fork me on GitHub" /></a>
<div class="navbar navbar-inverse navbar-fixed-top">
<div class="container">
    <div>
        <div class="user_list">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">在线用户</h3>
                </div>
                <div class="panel-body h500" id="ollist">

                </div>
            </div>
        </div>
        <div class="chat_list">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">聊天区</h3>
                </div>
                <div id="chat_content" class="panel-body h500">
                    <p>zchat demo~~</p>
                </div>
            </div>
        </div>
    </div>
    <div class="chat_send">
        <div class="col-lg-6">
            <div class="input-group">
                <input type="text" id="msgContent" class="form-control">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button" id="sendBtn" onclick="sendMsg()">发言</button>
      </span>
            </div><!-- /input-group -->
        </div><!-- /.col-lg-6 -->
    </div>


</div> <!-- /container -->
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<div id="chat_swf"></div>
<script src="<?php echo $static_url;?>js/jquery-1.9.1.js"></script>
<script src="<?php echo $static_url;?>js/jsocket.js"></script>
<script src="<?php echo $static_url;?>js/swfobject.js"></script>
<script src="<?php echo $static_url;?>js/chat.js"></script>
<script type="text/javascript">
    chatClient.init('192.168.0.106',
        8991,
        'chat_swf',
        '<?php echo $static_url;?>js/jsocket.swf',
        [<?php echo $uid;?>, '<?php echo $token;?>']
    );
</script>
</body>
</html>