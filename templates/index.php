<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>baidu_pcs_ui</title>
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="public/bootstrap/css/bootstrap.min.css">

    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="public/bootstrap/css/bootstrap-theme.min.css">

    <link rel="stylesheet" href="public/css/head_and_footer.css">

    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="public/js/jquery-1.11.3.js"></script>

    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="public/bootstrap/js/bootstrap.min.js"></script>

    <script src="public/js/jquery.isloading.js"></script>
</head>
<body>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Project name</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<div class="container">
    <div class="page-header">
        <h1>Sticky footer with fixed navbar</h1>
    </div>
    <div class="row">
        <!--左侧导航栏-->
        <div class="col-md-2">
            <ul>
                <li><a href="submit.html">提交更新</a></li>
                <li><a href="records.html">所有更新</a></li>
                <li><a href="rollback.html">回滚记录</a></li>
                <li><a href="release.html">审核发布</a></li>
            </ul>
        </div>
        <div class="col-md-10" id="page_content">
        </div>
    </div>
</div>
<footer>
    <div class="container">
        <p>页脚</p>
    </div>
</footer>
<script>
    var base_path = '/';
    $(document).ajaxSend(function(event, jqxhr, settings) {
        $.isLoading({ text: "Loading" });
    });
    $(document).ajaxComplete(function() {
        $.isLoading( "hide" );
    });


    function get_list(path){
        $.ajax({
            url:'http://test.centos65.home/baidu_pcs_ui/index.php',
            data:{action:'get_list',path:path},
            success:function(data){
                $('#page_content').html(data);
                $('#base_path').html(base_path);
            },
            dataType:'html'
        });
    }

    function download(file,path){
        $.ajax({
            url:'http://test.centos65.home/baidu_pcs_ui/index.php',
            data:{action:'download',path:path,'file':file},
            success:function(data){
                console.log(data);
            },
            dataType:'json'
        });
    }


    $(document).ready(function(){
        get_list();
    });
</script>
</body>
</html>