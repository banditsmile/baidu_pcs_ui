<?php
/**
 * Created by PhpStorm.
 * User: xubandit
 * Date: 15/7/6
 * Time: 上午11:13
 */


function get_post($key){
	return isset($_GET[$key])?$_GET[$key]:(isset($_POST[$key]) ?$_POST[$key]:'');
}

function json_output($status,$message='',$data=array()){
	header('Content-type: application/json');
	echo json_encode(array('errno'=>$status,'msg'=>$message,'data'=>$data));
	return true;
}

function human_filesize($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

if(!function_exists('array_column')){
	function array_column($array,$col_name){
		$result = array();
		if(empty($array)){return $result;}
		foreach($array as $item){
			$result = isset($item[$col_name]) ? $item[$col_name] :null;
		}
		return $result;
	}
}

function log_debug($message){
	is_string($message) or $message=json_encode($message);
	$message = date('H:i:s').' --> '.$message.PHP_EOL;
	$log_file = LOG_PATH.'/'.date('Ymd').'.php';
	return file_put_contents($log_file,$message,FILE_APPEND);
}

function site_url($param){
	return 'http://test.centos65.home/baidu_pcs_ui/index.php?'.http_build_query($param);
}

/**
 * @param $progress
 * @return float|int
 * @desc 获取下载进度百分比
 */
function get_progress_rate($progress){
	$progress = explode('/',trim($progress));
	if(count($progress) !=2){
		return 0;
	}
	$down_size = human_filesize_to_number($progress[0]);
	$full_size = human_filesize_to_number($progress[1]);
	if($full_size){
		$rate = round($down_size/$full_size,2)*100;
	}else{
		$rate=0;
	}
	return $rate;
}

function human_filesize_to_number($size){
	$units = array('B'=>0, 'KB'=>1, 'MB'=>2, 'GB'=>3, 'TB'=>4);
	$is_match = preg_match('/[a-zA-Z]+/',$size,$match);
	if($is_match){
		$size = ((int)$size)*pow(1024,$units[strtoupper($match[0])]);
	}else{
		$size = 0;
	}
	return $size;
}