<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=$title;?></title>
</head>
<body>
    <form method="post" action="<?=Response::getUrl('login.into');?>">
        name: <input type="text" name="name"><br>
        password: <input type="password" name="password"><br>
        <input type="submit" value="submit">
    </form>
</body>
</html>