﻿<?php

class menuDefine{

    public $ph_name;   //上传图片文件名
    public $ph_tmp_name;    //图片临时文件名
    public $ph_path;    //上传文件存放路径
	public $ph_type;   //图片类型
   
    public $al_ph_type=array('image/jpg','image/jpeg','image/png','image/pjpeg','image/gif','image/bmp','image/x-png');    //允许上传图片类型
    public $al_ph_size=1000000;   //允许上传文件大小
	
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
	function up_photo(){		
		$s = new SaeStorage();  
		$result=$s->upload( 'wordpress' ,$this->ph_path,$this->ph_tmp_name); 
		if($result){
			//return $picUrl="http://".$s->getUrl( 'wordpress' ,$this->ph_path);
			return $picUrl=$s->getUrl( 'wordpress' ,$this->ph_path);
		}else{
			return $picUrl="";
		}
	}
		
		 
	//错误提示
	function showerror($errorstr){
		echo "<script language=java script>alert('$errorstr');location='java script:history.go(-1);';</script>";
		exit();
	}
  
	function save(){
		$this->check_size();
		$this->check_type();
		$url=$this->up_photo();
		return $url;
	}
}
?>