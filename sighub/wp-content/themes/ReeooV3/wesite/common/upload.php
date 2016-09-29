<?php

class upphoto{

    public $ph_name;   //上传图片文件名
    public $ph_tmp_name;    //图片临时文件名
    public $ph_path;    //上传文件存放路径
	public $ph_type;   //图片类型
   
    public $al_ph_type=array('image/jpg','image/jpeg','image/png','image/pjpeg','image/gif','image/bmp','image/x-png');    //允许上传图片类型
    public $al_ph_size=1000000;   //允许上传文件大小
	
	public $userid;
	
	public function __construct($gweid = NULL)
	{	
		global $wpdb;
		if(!empty($gweid)){
			$this -> userid = $wpdb -> get_var($wpdb -> prepare("SELECT user_id FROM {$wpdb -> prefix}wechat_group WHERE GWEID = %s",$gweid));
		    //echo "当前的用户id".$this -> userid;
		}
	}
   //获取文件类型
	function get_ph_type($phtype){
		$this->ph_type=$phtype;
	}
  
	//获取文件大小
	function get_ph_size($phsize){
		$this->ph_size=$phsize."<br>";
	}
	
	//获取文件存放路径
	function get_ph_surl($phpath){
		$this->ph_path=$phpath;
	}
  
	//获取上传临时文件名
	function get_ph_tmpname($tmp_name){
		$this->ph_tmp_name=$tmp_name;
		$this->imgsize=getimagesize($tmp_name);
	}
  
	//获取原文件名
	function get_ph_name($phname){
		//$this->ph_name=$this->ph_path.$this->datetime.strrchr($phname,"."); //strrchr获取文件的点最后一次出现的位置
		//$this->ph_name=$this->datetime.strrchr($phname,"."); //strrchr获取文件的点最后一次出现的位置
		return $this->ph_name;
	}

	// 判断上传文件存放目录
	function check_path(){
		if(!file_exists($this->ph_path)){
			mkdir($this->ph_path);
		}
	}
  
	//判断上传文件是否超过允许大小
	function check_size(){
		if($this->ph_size>$this->al_ph_size){
			$this->showerror("上传图片超过2000KB");
		}
	}
  
	//判断文件类型
	function check_type(){
		if(!in_array($this->ph_type,$this->al_ph_type)){
			$this->showerror("上传图片类型错误");
		}
	}
  
	//上传图片
	function up_photo($file = null){
		/*
		$s = new SaeStorage();  
		$result=$s->upload( 'wordpress' ,$this->ph_path,$this->ph_tmp_name); 
		if($result){
			//return $picUrl="http://".$s->getUrl( 'wordpress' ,$this->ph_path);
			return $picUrl=$s->getUrl( 'wordpress' ,$this->ph_path);
		}else{
			return $picUrl="";
		}*/
		global $wpdb;
		global  $current_user;
		//判断是否是分组管理员下的用户
		$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$current_user->ID);
		if(!empty($getgroupuserids)){
			foreach($getgroupuserids as $getgroupinfo)
			{
			    $usergroupid = $getgroupinfo -> group_id;
			    $usergroupflag = $getgroupinfo -> flag;
			}
		}else{  //分组里没有记录，则属于默认分组，groupid为0，对应的flag为0
			$usergroupid = 0;
			$usergroupflag = 0;
		}
		
		$id = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($usergroupid !=0 && $usergroupflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		
		//$id = (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		if(empty($id))
			if(empty($this -> userid))
				return false;
			else
				$id = $this -> userid;
		if ( ! function_exists( 'wp_handle_upload' ) ) 
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$uploadedfile = ($file == null ? $_FILES['file'] : $file);
		$uploadedfile['name'] = time().rand().strstr($uploadedfile['name'],'.');
		$uploadedfile['type'] = 'image/jpeg';
		$upload_overrides = array( 'test_form' => false );
		$size = $uploadedfile['size']/1048576;		
		$size=number_format($size,3,'.','');
		$user_space = $wpdb -> get_row("SELECT * FROM {$wpdb->prefix}wesite_space WHERE userid = {$id}",ARRAY_A);
		$available_space = $user_space['defined_space'] - $user_space['used_space'];
		if($available_space<$size)
			return false;
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
		if ( $movefile ) {
			$wpdb->query(
				"
				UPDATE {$wpdb->prefix}wesite_space 
				SET used_space = used_space+{$size}
				WHERE userid = {$id}
				"
			);
			$str = strstr($movefile['url'], 'uploads');
			$movurl=substr($str, 7);
			return $movurl;
		} else {
			return $picUrl="";
		}
	}
		
		 
	//错误提示
	function showerror($errorstr){
		echo "<script language=java script>alert('$errorstr');location='java script:history.go(-1);';</script>";
		exit();
	}
  
	function save($file = null){
		//$this->check_size();
		//$this->check_type();
		$upfile=$file;
		$url=$this->up_photo($upfile);
		return $url;
	}
}
?>