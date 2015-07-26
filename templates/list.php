<!--网页主体-->

<table class="table table-striped table-bordered" id="list_table">
	<thead>
	<th><input type="checkbox" name="check_all"/></th><th>文件名</th><th>大小</th><th>修改日期</th><th>操作</th>
	</thead>
	<tbody>

	<?php foreach($list as $key=>$item):?>
	<tr class="success">
		<td><input type="checkbox" name="item" value="<?php echo $key;?>"></td>
		<td>
			<?php if($item['type']=='d'):?>
				<span class="glyphicon glyphicon-folder-close" aria-hidden="true" ></span>
			<?php endif;?>
			<span class="<?php echo $item['type'];?>"><?php echo $item['name'];?></span>

		</td>
		<td><?php echo $item['size'];?></td>
		<td><?php echo $item['date'];?></td>
		<td>
			<?php if($item['type']!='d'):?>
				<button type="button" class="list-group-item download" data="<?php echo $item['name'];?>">下载</button>
			<?php endif;?>
		</td>
	</tr>
	<?php endforeach;?>
	</tbody>
</table>
<script>
	$(document).ready(function(){
		$('.d').dblclick(function(){
			var path=$(this).html();
			if(base_path.charAt(0) !='/'){
				base_path = '/'+base_path;
			}
			if(base_path.length>1 && base_path.charAt(base_path.length-1) =='/'){
				base_path = base_path.slice(0,base_path.length-1);
			}

			//回到上层目录
			if(path=='..'){
				if(base_path=='/'){
					return get_list(base_path);
				}else{
					base_path = base_path.slice(0,base_path.lastIndexOf('/'));
					return get_list(base_path);
				}
			}

			//进入子目录
			if(path.charAt(0)=='/'){
				path = path.slice(1,path.length);
			}
			if(path.charAt(path.length-1)=='/'){
				path = path.slice(0,path.length-1);
			}
			if(base_path.length==1){
				base_path = path = base_path+path;
			}else{
				base_path = path = base_path+'/'+path;
			}
			get_list(path);
		});

		$('.download').click(function(){
			var file = $(this).attr('data');
			file = base_path+'/'+file;
			download(file,'');
		});
	})
</script>