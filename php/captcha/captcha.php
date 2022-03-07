<html>
<head>
<title>Captcha Test</title>
</head>
<body>
<form action="/captcha.php" method="post">
<img src="/code.php?r=<?php echo mt_rand() / mt_getrandmax() ?>" onClick="this.src='/code.php?r='+Math.random();" />
<br />
<input type="text" name="code" />
<br />
<input type="submit" value="Verify" />
</form>
<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    session_start();
    if ($_SESSION['secret'] == md5(strtolower($_REQUEST['code']))) {
        echo "<h1>GOOD</h1>";
    } else {
        echo "<h1>BAD</h1>";
    }
}
?>
</body>
</html>
