<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="zchat">
    <meta name="author" content="shenzhe">
    <link rel="shortcut icon" href="<?php echo $static_url;?>ico/favicon.png">
    <title>ZChat提示页</title>
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
    <div class="alert alert-warning"><?php echo $msg; ?></div>
    <a href="javascript:void(0)" onclick="history.back();" class="btn btn-primary btn-lg active" role="button">返回</a>

</div> <!-- /container -->
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
</body>
</html>