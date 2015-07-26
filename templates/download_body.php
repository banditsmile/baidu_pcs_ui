<div class="col-md-10" id="page_content">
	<div ><span style="padding-right: 5px"></span><span id="base_path"></span><span id="tips" style="float:right">aaa</span></div>
	<table class="table table-striped table-bordered" id="list_table">
		<thead>
		<th><input type="checkbox" name="check_all"></th><th>文件名称</th><th>进度</th><th>速度</th><th>剩余时间</th><th>操作</th>
		</thead>
		<tbody>

		<?php foreach($list as $key=>$item):?>
			<tr file_key="<?php echo $key;?>">
				<td><input type="checkbox" name="item" value="<?php echo $key;?>"></td>
				<td>
					<?php if($item['status']==0):?>
					<div class="progress">
						<div class="progress-bar  progress-bar-striped active"
							 role="progressbar"
							 aria-valuenow="40"
							 aria-valuemin="0"
							 aria-valuemax="100"
							 style="min-width: 2em;width: <?php echo get_progress_rate($item['info'][0]);?>%">
							<span ></span>
						</div>
					</div>
					<?php endif;?>
					<div><?php echo $item['from'];?></div>

				</td>
                <?php if($item['status']==0):?><!--正在下载-->
					<td><?php echo $item['info'][0];?></td>
					<td><?php echo $item['info'][1];?></td>
					<td><?php echo $item['info'][2];?></td>
                <?php elseif($item['status']==1):?><!--暂停-->
					<td><?php echo $item['info'][0];?></td>
					<td>暂停中</td>
					<td><?php echo $item['info'][2];?></td>
				<?php else:?><!--下载完成-->
					<td class="alert alert-success">已完成</td><td></td><td></td>
                <?php endif;?>
				<td >
					<?php if($item['status']==0):?>
					<!--<button type="button" class="list-group-item download" data="<?php /*echo $key;*/?>">暂停</button>-->
						<button class="glyphicon glyphicon-pause pause" aria-hidden="true"></button>
					<?php endif;?>
					<!--<button type="button" class="list-group-item download" data="<?php /*echo $key;*/?>">删除</button>-->
						<button class="glyphicon glyphicon-remove remove" aria-hidden="true"></button>
					<?php if($item['status']==1):?>
						<!--<button type="button" class="list-group-item download" data="<?php /*echo $key;*/?>">继续</button>-->
						<button class="glyphicon glyphicon-play continue" aria-hidden="true"></button>
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
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

	function pause(file_key){
		$.ajax({
			url:'http://test.centos65.home/baidu_pcs_ui/index.php',
			data:{action:'pause',file_key:file_key},
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
	function continue_download(file_key){
		$.ajax({
			url:'http://test.centos65.home/baidu_pcs_ui/index.php',
			data:{action:'continue_download',file_key:file_key},
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

	function remove(file_key){
		$.ajax({
			url:'http://test.centos65.home/baidu_pcs_ui/index.php',
			data:{action:'remove',file_key:file_key},
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
		$('.pause').click(function(){
			var file_key = $(this).parents('tr').attr('file_key');
			pause(file_key);
		});
		$('.continue').click(function(){
			var file_key = $(this).parents('tr').attr('file_key');
			continue_download(file_key);
		});
		$('.remove').click(function(){
			var file_key = $(this).parents('tr').attr('file_key');
			remove(file_key);
		});
	});
</script>