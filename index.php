<?php
// ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7);
// ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// echo phpinfo();die;
//echo json_encode(array('hi1','ocean2'));die;
require_once ( __DIR__ . '/config.php' ) ;
?>

<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo BASE_URL;?></title>
    <?php
        echo JS_GLOBALS;
    ?>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <link rel="manifest" href="manifest.json">
    <!-- stylesheet -->
    <link rel="stylesheet" href="<?php echo BASE_CSS_URL;?>" type="text/css">
    <script language="JavaScript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>
    <script language="JavaScript" type="text/javascript" src="<?php echo BASE_JS_URL;?>header.js"></script>
    <script language="JavaScript" type="text/javascript" src="<?php echo BASE_JS_URL;?>game.js"></script>
</head>
<body>
<?php
    include 'header.php';
    include BASE_VIEW_PATH . CURRENT_VIEW;
    include 'footer.php';
?>

</body>
</html>
