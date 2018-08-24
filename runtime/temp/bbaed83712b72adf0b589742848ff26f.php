<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"F:\wamp64\www\fastadmin\public/../application/api\view\upload\index.html";i:1534730805;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<form action="<?php echo url('api/upload/upimg'); ?>" enctype="multipart/form-data" method="post">
    <input type="file" name="image" /> <br>
    <input type="submit" value="上传" />
</form>
</body>
</html>