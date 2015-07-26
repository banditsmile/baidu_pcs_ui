<?php
/**
 * Created by PhpStorm.
 * User: xubandit
 * Date: 15/6/30
 * Time: 下午11:11
 */
class base_controller{
	public function __construct(){

	}
}

class baidu_pcs_controller extends base_controller{

	var $baidu_pcs;
	var $default_download_path = DOWN_PATH;//默认下载目录
	var $down_status=array();
	var $down_status_file = BASE_PATH.'/data/download.php';
	public function __construct(){
		parent::__construct();
		$this->baidu_pcs = baidu_pcs::instance();
	}

	public function index(){
		return $this->render('list_body');
	}

	public function get_list(){
		$path = get_post('path');
		$path = rtrim($path,'/');

		$result = $this->baidu_pcs->get_list($path);
		$list = array();
		foreach($result as $item){
			$item['name'] = str_replace($path.'/','',$item['name']);
			$item['size'] = human_filesize($item['size'],0);
			$list[]=$item;
		}
		unset($result);
		if($path !='/' && $path !=''){
			$parent_dir = array('name'=>'..','size'=>'0B','date'=>max(array_column($list,'date')),'type'=>'d');
			array_unshift($list,$parent_dir);
		}
		return include BASE_PATH.'/templates/list.php';
	}

	public function download(){
		$file = rtrim(get_post('file'),'/ ');
		$path = trim(get_post('path'));

		//检查网盘是否存在该文件
		$file_info = $this->baidu_pcs->meta($file);
		if(empty($file_info) ){
			return json_output(101,'文件不存在');
		}
		if($file_info['Is Dir']=='Yes'){
			return json_output(101,'不支持目录下载');
		}

		//设置下载在路径
		$file_name = substr($file,strrpos($file,'/')+1);
		if(empty($path)){
			$path = $this->default_download_path.'/';
		}
		if(is_dir($path)){
			$path = rtrim($path,'/').'/'.$file_name;
		}

		if(file_exists($path)){
			return json_output(103,'下载位置已存在文件'.$path);
		}

		$down_info = $this->baidu_pcs->download($file,$path);
		if($down_info){
			$this->save_download_info($down_info);
			return json_output(0,'success',$down_info);
		}else{
			return json_output(104,'下载失败请重试');
		}
	}

	/**
	 * @param $data
	 * @return int
	 * @param $full
	 * @desc 记录下载信息
	 */
	private function save_download_info($data,$full=false){
        $info_file = $this->down_status_file;
        if(!$full){//只修改部分
            if(!is_writable(dirname($info_file))){
                return false;
            }
            if(is_file($info_file)){
                $info = include $info_file;
            }else{
                $info = array();
            }
            $data['status']=0;//开始下载
            $info[md5($data['from'])]=$data;
        }else{
            $info = $data;
        }
		$info_str = '<?php'.PHP_EOL;
		$info_str .= 'return '.PHP_EOL;
		$info_str .=var_export($info,true);
		$info_str .=';';
		if(file_put_contents($info_file,$info_str)){
			$this->down_status = array();
			return true;
		}else{
			return false;
		}
	}

	public function download_status(){
		$down_status = $this->get_down_status();
		$list = array();
        $modified = false;
		foreach($down_status as $key=>$status){

            //下载状态文件被清除，那就也清除下载信息
            if(!is_file($status['progress'])){
                unset($down_status[$key]);
                $modified = true;
                continue;
            }

            //已下载完成和暂停的下载不作处理
            if(isset($status['status']) and $status['status']==2){
                $list[$key]=$status;
                continue;
            }
			if(is_file($status['to'])){
				$status['status'] = 2;//下载完成
                $down_status[$key]['status']=2;
                $modified=true;
			}else{
				$progress_info = $this->get_download_info($status['progress']);
				if($progress_info===false){//无法获取下载信息
					unset($down_status[$key]);
					$modified = true;
					continue;
				}
				$status['info'] = $progress_info;
				//$status['status']=0;//正在下载
			}
			$list[$key]=$status;
		}

        //对下载状态做过修正，重新写回去
        if($modified){$this->save_download_info($down_status,true);}

        $status_array =array_column($list,'status');
        //按照下载状态排序0正在下载 1暂停 2下载完成
        array_multisort($status_array,$list);
		$this->render('download_body',array('list'=>$list));
	}

	/**
	 * @desc 从下载状态文件解析下载进度
	 * @param $progress_file
	 * @return array|bool
	 *@todo 可能出现文件刚刚创建就获取文件信息fseek失败的情况
	 */
	protected function get_download_info($progress_file){
        if(false === ($fh = fopen($progress_file,'r'))){
			return false;
		}
        if(fseek($fh,-100,SEEK_END) !==0){
            return false;
        }
		$str = fgets($fh,100);
		fclose($fh);
		$last = substr($str,strrpos($str,chr(13))+1);//取最后一行
		$info=array_filter(explode("\t",$last));
		return $info;
    }

	public function get_down_info(){
		$file_key = get_post('file_key');
		$down_status = $this->get_down_status();
		if(!isset($down_status[$file_key])){
			return json_output(101,'file not exist');
		}
		$info = $this->get_download_info($down_status[$file_key]['progress']);
		if($info){
			return json_output(0,'success',$info);
		}else{
			return json_output(101,'file not exist');
		}
	}

	protected function get_down_status(){
		if(empty($this->down_status)){
			$this->down_status = include $this->down_status_file;
		}
		return $this->down_status;
	}

	public function render($template,$data=array()){
		include BASE_PATH.'/templates/header.php';
		extract($data);
		include BASE_PATH.'/templates/'.$template.'.php';
		include BASE_PATH.'/templates/footer.php';
		return true;
	}

    public function pause(){
        $file_key = get_post('file_key');
        $down_status = $this->get_down_status();
        if(empty($down_status) || !isset($down_status[$file_key])){
            return json_output('101','参数错误');
        }
        if(empty($down_status[$file_key]['pid'])){
            return json_output('102','系统错误');
        }
        if($this->baidu_pcs->kill($down_status[$file_key]['pid'])){
            $down_status[$file_key]['status']=1;
            $down_status[$file_key]['pid']=0;
            $this->save_download_info($down_status,true);
            return json_output(0,'success');
        }else{
            return json_output(103,'暂停失败请重试');
        }
    }

    /**
     * @desc 继续被暂停的下载
     */
    public function continue_download(){
        $file_key = get_post('file_key');
        $down_status = $this->get_down_status();
        if(empty($down_status) || !isset($down_status[$file_key])){
            return json_output('101','参数错误');
        }
        if(!isset($down_status[$file_key]['from']) || !isset($down_status[$file_key]['to'])){
            return json_output('102','系统错误');
        }
        $down_info = $this->baidu_pcs->download($down_status[$file_key]['from'],$down_status[$file_key]['to'],false,$down_status[$file_key]['progress']);
        if($down_info){
            $down_status[$file_key]=$down_info;
            $this->save_download_info($down_status,true);
            return json_output(0,'success',$down_info);
        }else{
            return json_output(101,'系统错误请重试');
        }
    }


    /**
     * @desc 删除一个下载任务，通常是已经被完成的下载任务
     */
    public function remove(){
        $file_key =get_post('file_key');
        $down_status = $this->get_down_status();
        if(empty($down_status) || !isset($down_status[$file_key])){
            return json_output('101','参数错误');
        }
        if(!isset($down_status[$file_key]['progress']) ){
            return json_output('102','系统错误');
        }
        if(is_file($down_status[$file_key]['pregress'])){
            unlink($down_status[$file_key]['pregress']);
        }
        if($down_status[$file_key]['pid']){
            $this->baidu_pcs->kill($down_status[$file_key]['pid']);
        }
        unset($down_status[$file_key]);
        return json_output(0,'success');
    }
}