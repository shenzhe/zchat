<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="zchat">
    <meta name="author" content="shenzhe">
    <link rel="shortcut icon" href="<?php echo $static_url;?>ico/favicon.png">
    <title>ZChat注册页</title>
    <!-- Bootstrap core CSS -->
    <link href="<?php echo $static_url;?>css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo $static_url;?>css/sign.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="<?php echo $static_url;?>ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="<?php echo $static_url;?>html5shiv.min.js"></script>
    <script src="<?php echo $static_url;?>respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">

    <form class="form-signin" role="form" method="post" action="<?php echo \common\Utils::makeUrl('main/main', 'savereg');?>" />
        <h2 class="form-signin-heading">请注册</h2>
        <input type="text" name="username" class="form-control" placeholder="用户名" required autofocus>
        <input type="password" name="password" class="form-control" placeholder="密码" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">注册</button>
    </form>

</div> <!-- /container -->
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
</body>
</html>