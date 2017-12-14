<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>vicR</title>
</head>
<body>
<div class="except <?php echo $type;?>">
    <div class="pure-g">
        <div class="pure-u-1-4">
            <i class="iconfont icon-<?php echo $type;?>"></i>
        </div>
        <div class="pure-u-2-3">
            <h1><?php echo $msg;?></h1>
            <p><b><?php echo $time; ?></b>秒后自动<?php echo $back?'返回':'跳转';?></p>
        </div>
    </div>
</div>
<script src="//cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
<script>
(function () {
    var t = <?php echo $time;?>,
        url = '<?php echo $url;?>',
        back = <?php echo $back;?>;
    setInterval(function () {
        t = t -1;
        if(t < 0){
            if(back && history.length > 1){
                window.history.back();
            }else if(url){
                window.location.href = '<?php echo $url;?>';
            }else{
                window.location.href = '/';
            }
            return ;
        }
        $('.except b').text(t);
    },1000)
})();
</script>
</body>
</html>