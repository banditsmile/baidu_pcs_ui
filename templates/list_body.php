<div class="col-md-10" id="page_content">
	<div ><span style="padding-right: 5px">当前位置:</span><span id="base_path"></span><span id="tips" style="float:right">aaa</span></div>
</div>
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
				if(data.status==0){
					var msg = '<span class="label label-success">'+data.msg+'</span>';
				}else{
					var msg = '<span class="label label-warning">'+data.msg+'</span>';
				}
				$('#tips').html(msg);
			},
			dataType:'json'
		});
	}


	$(document).ready(function(){
		get_list();
	});
</script>