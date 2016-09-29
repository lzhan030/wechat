<?php

class WeSchoolModuleSite extends ModuleSite {
	
	//产生验证码的函数
	public function gernate_vericode( $length = 6 ) { 
		 // 密码字符集，可任意添加你需要的字符 
		 $chars = '0123456789abcdefghijklmnopqrstuvwxyz';  
		 $password = $chars[ mt_rand(1, strlen($chars) - 1) ];  
		 for ( $i = 0; $i < $length-1; $i++ )  
		 {  
		 // 这里提供两种字符获取方式  
		 // 第一种是使用 substr 截取$chars中的任意一位字符；  
		 // 第二种是取字符数组 $chars 的任意元素  
		 // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
		$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];  }  
		return $password; 
	}
	
	//click weschool button
	public function doWebIndex(){
	    global $_W;
	    $gweid =  $_W['gweid'];
        include $this -> template('weschool');
    } 
	public function doWebPersonmanage(){
        include $this -> template('personmanage');
    }
	public function doWebVideo(){
        include $this -> template('wechatvideo');
    }
	//点击微学校首页的设置按钮，进入全局设置页面
	public function doWebWeschool_custom_dlg(){
		global $wpdb, $_GPC;
		$gweid=$_SESSION['GWEID'];

		$count_num=pdo_fetchall("SELECT * FROM wp_wechat_usechat w1, wp_usermeta w2 WHERE w1.user_id=w2.user_id AND w1.GWEID=".$gweid);	
		foreach($count_num as $num){
			$user_id=$num['user_id'];
		}
		
		$homework_num=get_user_meta($user_id,'school_homework_displaycount',true);
		$video_num=get_user_meta($user_id,'school_video_displaycount',true);
		$notice_num=get_user_meta($user_id,'school_notice_displaycount',true);
		
		if( isset($_GPC['homework_number']) &&!empty($_GPC['video_number'])){

			$homework_number = $_GPC['homework_number'];
			$video_number = $_GPC['video_number'];
			$notice_number = $_GPC['notice_number'];	   
		   
			update_user_meta( $user_id, 'school_homework_displaycount', $homework_number);
			update_user_meta( $user_id, 'school_video_displaycount', $video_number);
			update_user_meta( $user_id, 'school_notice_displaycount', $notice_number);
			echo"<script language='JavaScript'>";
			echo"setTimeout('self.close()',0);";
			echo"opener.location.reload();";
			echo"</script>";	
			
		}

        include $this -> template('weschool_custom_dlg');
    }
	
	//获取根据查询条件得到的教师数据集
	public function doWebCountTeacherSearchPage($indata,$rg,$offset,$pagesize,$gweid)
	{
		global $wpdb;
		
		$myrows = pdo_fetchall("SELECT * FROM wp_school_teacher w1 WHERE w1.".$rg." like '%".$indata."%' AND w1.GWEID = ".$gweid." ORDER BY w1.tea_id limit ".$offset.",".$pagesize);
		
		return $myrows;
	}
	//获取所有的teacher的个数
	public function doWebCountTeacher($gweid)
	{
		global $wpdb;
	   
		$myrows = pdo_fetchall("SELECT COUNT(*) as userCount FROM wp_school_teacher WHERE GWEID = ".$gweid);							
		return $myrows;
	}	
	//获取teacher当页的所有数据集
	public function doWebCountTeacherPage($offset,$pagesize,$gweid)
	{
		global $wpdb;
		
		$myrows = pdo_fetchall( "SELECT * FROM wp_school_teacher WHERE GWEID = ".$gweid." ORDER BY tea_id limit ".$offset.",".$pagesize );
		return $myrows;
	}
	//教师管理页面
	public function doWebTeachermanage(){
	
	    global $wpdb, $_GPC;
	    //obtain userId
		global $current_user;	
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId = ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;				
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];

	    $flag=$_GPC['flag'];
	    $f=$_GPC["f"];
	    if($f==null){$f=$flag;}
	    $indata=$_GPC['Ipad'];
	    $in=$_GPC["in"];
	    if($in==null){$in=$indata;}
	    $indata=$in;
		$rg=$_GPC['range'];
		$r=$_GPC["r"];
		if($r==null){$r=$rg;}
		$rg=$r;
		//删除教师
		if(isset($_GPC['del']) && !empty($_GPC['del']) ){
		
			pdo_delete('school_teacher',array('tea_id' => $_GPC['del']));
		}
		
		$vmembersCount = pdo_fetchall("SELECT COUNT(*) as memberCount FROM wp_school_teacher w1 WHERE w1.".$rg." like '%".$indata."%' AND w1.GWEID = ".$gweid);
		$usersCount = $this -> doWebCountTeacher($gweid);
		
        include $this -> template('teachermanage');
    }
	//导入教师信息excel
	public function doWebUploadteacher(){
		global $wpdb;
		error_reporting(E_ERROR);
	    //获取session中的gweid
	    $gweid=$_SESSION['GWEID'];
	
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel.php';
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel/IOFactory.php';
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel/Reader/Excel5.php';

		$filename = $_FILES['inputExcel']['name'];
		$tmp_name = $_FILES['inputExcel']['tmp_name'];
		
		//判断上传文件的后缀名
		$extstring = substr($filename, strrpos($filename, ".")+1, strlen($filename)-strrpos($filename, "."));
		
		if($extstring === "xls")
		{
		   $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2003 for 2003 format 
		}
		elseif($extstring === "xlsx")
		{
		   $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format 
		}
		else
		{
			echo "导入失败，格式有误";
			exit;
		}
		
		$objPHPExcel = $objReader->load($tmp_name); //$filename可以是上传的文件，或者是指定的文件
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		$k = 0; 
		//循环读取excel文件,读取一条,插入一条
		$count = 0;
		for($j=2;$j<=$highestRow;$j++)
		{

			$a = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值
			$b = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();//获取B列的值
			$c = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();//获取B列的值
			if($b === '女')
			   $b = 0;
			else
			   $b = 1;
			$vericode = $this -> gernate_vericode();   
			
			//判断验证码是否重复 
			$countvericodes = $wpdb -> get_results($wpdb -> prepare("SELECT count(*) as countcode FROM wp_school_teacher WHERE GWEID=".$gweid." and tea_vericode = %s",$vericode),ARRAY_A);
			foreach($countvericodes as $countvericode)
			{
			    $vericodenumber = $countvericode['countcode'];
			}
			while($vericodenumber != 0)
			{
			    $vericode = $this -> gernate_vericode();  
				$countvericodes = $wpdb -> get_results($wpdb -> prepare("SELECT count(*) as countcode FROM wp_school_teacher WHERE GWEID=".$gweid." and tea_vericode = %s",$vericode),ARRAY_A);
				foreach($countvericodes as $countvericode)
				{
					$vericodenumber = $countvericode['countcode'];
				}
			}
			
			$insertresult = pdo_insert('school_teacher',array('tea_name' => $a, 'tea_sex' => $b, 'tea_gradeclass' => $c, 'tea_vericode'  =>$vericode, 'GWEID' => $gweid));
			if(!empty($insertresult))
					$count++;
			
		}
		if($count)
			echo "导入成功，导入{$count}条老师信息";
		else
			echo "导入失败，没有找到老师信息或格式错误";
        
    }
	
	//添加教师页面
	public function doWebAddteacher(){
	
	    global $wpdb, $_GPC;
		//获取session中的gweid
	    $gweid=$_SESSION['GWEID'];
		//拿到所有的年级
		$all_tgc = pdo_fetchall("(SELECT  distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid.") ORDER BY tea_gradeclass");
		
		
		if( isset($_GPC['teacher_name']) ){

		   $teacher_name = $_GPC['teacher_name'];
		   $teacher_sex = $_GPC['teacher_sex'];
		   $teacher_gradeclass = $_GPC['teacher_gradeclass'];
		   $in=$_GPC['in'];
			if($in!=null){
			$teacher_gradeclass=$in;
			}
		   $teacher_vericode = $_GPC['teacher_vericode'];
		   
		   //判断验证码是否重复 
			$countvericodes = pdo_fetchall("SELECT count(*) as countcode FROM wp_school_teacher WHERE GWEID=".$gweid." and tea_vericode = '".$teacher_vericode."'");
			foreach($countvericodes as $countvericode)
			{
			    $vericodenumber = $countvericode['countcode'];
			}
			
		    if($vericodenumber == 0)
			{
				pdo_insert('school_teacher',array('tea_name' => $teacher_name, 'tea_sex' => $teacher_sex, 'tea_gradeclass' => $teacher_gradeclass, 'tea_vericode'  =>$teacher_vericode, 'GWEID' => $gweid));
			}
			
		}
	
        include $this -> template('addteacher');
    }
	
	//编辑教师页面
	public function doWebEditteacher(){
	
	    global $wpdb, $_GPC;
		//获取session中的gweid
	    $gweid = $_SESSION['GWEID'];
		$tid = $_GPC['id'];
		$all_tgc = pdo_fetchall("SELECT  distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
		if( isset($_GPC['teacher_name']) &&!empty($_GPC['teacher_name'])){
		
		   $teacher_name = $_GPC['teacher_name'];
		   $teacher_sex = $_GPC['teacher_sex'];
		   $in=$_GPC['in'];
		   	if($in!=null){
				$teacher_gradeclass=$in;
			}
			else{ 
				$teacher_gradeclass = $_GPC['teacher_gradeclass'];
			} 
		   $teacher_vericode = $_GPC['teacher_vericode'];
		   //判断验证码是否重复 
			$countvericodes = pdo_fetchall("SELECT count(*) as countcode FROM wp_school_teacher WHERE GWEID=".$gweid." and tea_vericode = '".$teacher_vericode."' AND tea_id != ".$tid);
			foreach($countvericodes as $countvericode)
			{
			    $vericodenumber = $countvericode['countcode'];
			}
			if($vericodenumber == 0)
			{
				pdo_update('school_teacher',array('tea_name'=>$teacher_name,'tea_sex'=>$teacher_sex, 'tea_gradeclass'=>$teacher_gradeclass,'tea_vericode'=>$teacher_vericode),array('tea_id'=>$tid));	
			}
		}

        $teachers = pdo_fetchall("SELECT * FROM wp_school_teacher WHERE GWEID=".$gweid." and tea_id = ".$tid);
		foreach($teachers as $teacher )
		{
			$teachername = $teacher['tea_name'];
			$teachersex = $teacher['tea_sex'];
			$teachergradeclass = $teacher['tea_gradeclass'];
			$teachervericode = $teacher['tea_vericode'];
		}

        include $this -> template('editteacher');
    }
	
	//查询教师页面
	public function doWebSearchteacher(){
	    global $_GPC;
	    $range = $_GPC['range'];	
	    $indata = $_GPC['indata'];
        include $this -> template('searchteacher');
    }
	
	public function doMobileHomeworkverify(){
		global $_GPC;
		$uAgent = $_SERVER['HTTP_USER_AGENT']; 
		$osPat = "android|UCWEB|iPhone|iPad|BlackBerry|Symbian|Windows Phone|hpwOS"; 
		if(preg_match("/($osPat)/i", $uAgent )) { 
			$regtype = 'Mobile';
		} else { 
			$regtype = 'Web';
		}

		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		//一进到该页面时判断需不需要再次输入学生验证码
		if(!empty($fromuser)){	
			$number=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
		}else{
			$number=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
		}
		
		foreach($number as $count)
		{
				$scount=$count['number'];
		}

		if($scount != 0){
			$flag1 = true;
		}
			
		//家长输入验证码
		if( isset($_GPC['user_vercode']) ){    
			$user_vercode = $_GPC['user_vercode'];
			$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$user_vercode."'");
				
			foreach($countnumber as $count){
				$coer=$count['number'];
			}			
			if($coer == 0){
			?>
				<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
				<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<script>
						alert("您输入的验证码有误");
					</script>
				</head>
				</html>
		
			<?php
				}else{
					if(!empty($fromuser)){
						pdo_update('school_student',array('stu_fromuser'=>$fromuser),array('stu_vericode'=>$user_vercode,'GWEID'=>$gweid));	
					}
					$_SESSION['user_vercode']=$user_vercode;
					$flag1 = true;	
				}
		}
		include $this -> template('homeworkverify');
		
	}
	
	public function doWebExporthomework(){
		global $_GPC;
		$gweid=$_SESSION['GWEID'];
			
		$range=$_GPC['range'];
		$indata=$_GPC['indata'];

		$filename="作业表.csv";//先定义一个excel文件

		header("Content-Type: application/vnd.ms-execl"); 
		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename"); 
		header("Pragma: no-cache"); 
		header("Expires: 0");

		//我们先在excel输出表头，当然这不是必须的
		echo iconv("utf-8", "gb2312", "作业编号").",";
		echo iconv("utf-8", "gb2312", "作业标题").",";
		echo iconv("utf-8", "gb2312", "作业内容").",";
		echo iconv("utf-8", "gb2312", "年班级").",";
		echo iconv("utf-8", "gb2312", "开始时间").",";
		echo iconv("utf-8", "gb2312", "结束时间")."\n";
		//如果输入为空或者选择全部，则导出全部作业信息
		if(empty($indata) || $range=="all" || empty($range)){
			$homeworks=pdo_fetchall("SELECT * FROM wp_school_homework where GWEID=".$gweid);		
		}else{
			$homeworks=pdo_fetchall("SELECT * FROM wp_school_homework where GWEID='".$gweid."' and ".$range." like '%".$indata."%'");	
		
		}
		foreach($homeworks as $homework){

			echo iconv("utf-8", "gb2312", $homework['homework_id']).",";
			echo iconv("utf-8", "gb2312", $homework['homework_title']).",";
			echo iconv("utf-8", "gb2312", $homework['homework_content']).",";
			echo iconv("utf-8", "gb2312", $homework['homework_gradeclass']).",";
			echo iconv("utf-8", "gb2312", $homework['homework_starttime']).",";
			echo iconv("utf-8", "gb2312", $homework['homework_endtime'])."\n";
		}

    }	
	
	//获取根据查询条件得到的作业数据集
	public function doWebCountHomeworkSearchPage($gweid,$indata,$rg,$offset,$pagesize)
	{
		$myrows =pdo_fetchall("SELECT * FROM wp_school_homework  where GWEID=".$gweid." and ".$rg." like '%".$indata."%' ORDER BY homework_id DESC limit ".$offset.",".$pagesize);

		return $myrows;
	}
		
	//获取作业当页的所有数据集
	public function doWebCountHomeworkPage($gweid,$offset,$pagesize)
	{		
		$myrows =pdo_fetchall("SELECT * FROM wp_school_homework where GWEID=".$gweid." ORDER BY homework_id DESC limit ".$offset.",".$pagesize);
		return $myrows;
	}

	//作业管理页面
	public function doWebHomeworkmanage(){
		$gweid=$_SESSION['GWEID'];
	    global $wpdb, $_GPC;
	    //obtain userId
		global $current_user;	
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;				
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		
		$flag=$_GPC['flag'];
		$f=$_GPC["f"];
		if($f==null){$f=$flag;}
		$indata=$_GPC['Ipad'];
		$in=$_GPC["in"];
		if($in==null){$in=$indata;}
		$indata=$in;
		$rg=$_GPC['range'];
		$r=$_GPC["r"];
		if($r==null){$r=$rg;}
	    $rg=$r;
		//删除作业
		if(isset($_GPC['del']) && !empty($_GPC['del']) ){		
		
			pdo_delete('school_homework',array('homework_id' => $_GPC['del']));			
		}
		
		$homeworksCount =pdo_fetchall("SELECT COUNT(*) as homeworkCount FROM wp_school_homework  WHERE GWEID='".$gweid."' and ".$r." like '%".$in."%'");
		//获取根据查询条件得到的作业个数
	
		//获取所有的作业的个数
		$homeworkCount = pdo_fetchall("SELECT COUNT(*) as homeworkCount FROM wp_school_homework where GWEID=".$gweid);
	
		include $this -> template('homeworkmanage');
    }
	
	
	//获取所有的年级/班级
	public function doWebHomeworkGradeclass()
	{
		$myrows =pdo_fetchall("SELECT DISTINCT homework_gradeclass FROM wp_school_homework");
		return $myrows;
	}
	//添加作业页面
	public function doWebAddhomework(){
		global $wpdb, $_GPC;
		//获取session中的gweid
		$gweid=$_SESSION['GWEID'];
	    if(!isset($_GPC['gweid'])||!isset($_GPC['fromuser'])){
			$gweid = $_SESSION['GWEID'];
			$fromuser = $_SESSION['fromuser'];
		}else{
			$gweid =  $_GPC['gweid'];
			$fromuser = $_GPC['fromuser'];
		}
		
		$allgradeclass=pdo_fetchall( "SELECT DISTINCT tea_gradeclass FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
			//显示所有的年级
		$all_gc=pdo_fetchall("SELECT DISTINCT SUBSTR(tea_gradeclass,1,4) as sub_tea FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
		
		if( isset($_GPC['work_title']) ){
			$htmlData = '';
			$work_title = $_GPC['work_title'];
			$content1 = $_GPC['content1'];
			$home_gradeclass = $_GPC['home_gradeclass'];
			$sDate1=$_GPC['sDate1'];
			$sDate2=$_GPC['sDate2'];
			$htmlData = stripslashes($_GPC['content1']);

			pdo_insert('school_homework',array('homework_title' => $work_title, 'homework_content' => $htmlData, 'homework_starttime' => $sDate1, 'homework_endtime'  =>$sDate2,'homework_gradeclass'  =>$home_gradeclass, 'GWEID' => $gweid));
			
			echo"<script language='JavaScript'>";
			echo"setTimeout('self.close()',0);";
			echo"opener.location.reload();";
			echo"</script>";
			
		}
				
        include $this -> template('addhomework');
    }
	
	//手机端添加作业
	public function doMobileAddHomework(){
		global $wpdb, $_GPC;
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			if(!empty($fromuser)){
				$teagradeclass=pdo_fetchall( "SELECT tea_gradeclass FROM wp_school_teacher where GWEID=".$gweid." AND tea_fromuser='".$fromuser."'");
			}else{
				$teagradeclass=pdo_fetchall( "SELECT tea_gradeclass FROM wp_school_teacher where GWEID=".$gweid." AND tea_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($teagradeclass as $teagra){
				$tg_select=$teagra['tea_gradeclass'];
			}
			
			//2014-07-13新增修改，显示的是从老师和学生表中共同决定的年级和班级
			$allgradeclass = pdo_fetchall("(SELECT  distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid.") ORDER BY tea_gradeclass");
			//找到所有的年级
			$all_gc = pdo_fetchall("(SELECT  distinct(SUBSTR(`tea_gradeclass`,1,4)) as sub_tea FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(SUBSTR(`stu_gradeclass`,1,4)) FROM wp_school_student where GWEID=".$gweid.") ORDER BY sub_tea");
			
			
			if( isset($_GPC['work_title']) ){
				$htmlData = '';
				$work_title = $_GPC['work_title'];
				$home_content = $_GPC['home_content'];
				$home_gradeclass = $_GPC['home_gradeclass'];
				$sDate1=$_GPC['sDate1'];
				$sDate2=$_GPC['sDate2'];
				//$htmlData = stripslashes($_GPC['content1']);

				$Status=pdo_insert('school_homework',array('homework_title' => $work_title, 'homework_content' => $home_content, 'homework_starttime' => $sDate1, 'homework_endtime'  =>$sDate2,'homework_gradeclass'  =>$home_gradeclass, 'GWEID' => $gweid));
				
				if($Status!==false) {
					$flag = true;
					$countmember = false;
				}else{
				?>
				   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<script>
							alert("发布失败");
						</script>
					</head>
					</html>	
				<?php
				}
				
			}
					
			include $this -> template('addhomework_mobile');
		}else{
			$this->doMobileVerifyuser();			
		}
		
		
    }

	//获取对应id的作业信息
	function doWebGetHomeworkById($hid)
	{
		$myrows =pdo_fetchall("SELECT * FROM wp_school_homework where homework_id=".$hid);		
		return $myrows;
		
	}
	//编辑作业页面
	public function doWebEdithomework(){
		 global $wpdb, $_GPC;
		//获取session中的gweid
	    $gweid = $_SESSION['GWEID'];
		$homework_id = $_GPC['homeworkId'];
		
		
		if( isset($_GPC['work_title']) &&!empty($_GPC['work_title'])){
		
			$htmlData = '';
			$work_title = $_GPC['work_title'];
			$content1 = $_GPC['content1'];
			$home_gradeclass = $_GPC['home_gradeclass'];
			$sDate1=$_GPC['sDate1'];
			$sDate2=$_GPC['sDate2'];
			$htmlData = stripslashes($_GPC['content1']);		   
		   
			pdo_update('school_homework',array('homework_title'=>$work_title,'homework_content'=>$htmlData, 'homework_gradeclass'=>$home_gradeclass,'homework_starttime'=>$sDate1,'homework_endtime'=>$sDate2),array('homework_id'=>$homework_id));
			echo"<script language='JavaScript'>";
			echo"setTimeout('self.close()',0);";
			echo"opener.location.reload();";
			echo"</script>";		
			
		}
		
		$allgradeclass=pdo_fetchall( "SELECT DISTINCT tea_gradeclass FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
		//显示所有的年级
		$all_gc=pdo_fetchall("SELECT DISTINCT SUBSTR(tea_gradeclass,1,4) as sub_tea FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
        $homeworks = pdo_fetchall("SELECT * FROM wp_school_homework WHERE homework_id = ".$homework_id);
		foreach($homeworks as $homework )
		{
			$work_title= $homework['homework_title'];
			$work_content=$homework['homework_content'];
			$home_gradeclass=$homework['homework_gradeclass'];
			$home_starttime=$homework['homework_starttime'];
			$home_endtime=$homework['homework_endtime'];
		}

        include $this -> template('edithomework');
	    
    }
	
	//查询作业页面
	public function doWebSearchhomework(){
		global $_GPC;
	    $range = $_GPC['range'];	
	    $indata = $_GPC['indata'];
        include $this -> template('searchhomework');
    }
	public function doMobileHomeworklist(){
	
		global $_GPC;
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		//取到user_id
		$userid=pdo_fetchall("SELECT * FROM wp_wechat_usechat where GWEID=".$gweid);
		foreach($userid as $uid){
			$user_id=$uid['user_id'];
		}
		//2014-07-10新增修改
		$counts = 0;
		$countstea = 0;
		if(!empty($fromuser)){
			$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countnumber as $count){
				$counts=$count['number'];
			}
		}else{
		    //2014-07-14新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "parent")
			{
				$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countnumber as $count){
				$counts=$count['number'];
			}
			}
		}
		
		if(!empty($fromuser)){
			$countnumbertea=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
			foreach($countnumbertea as $counttea){
				$countstea=$counttea['number'];
			}
		}else{
		     //2014-07-14新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countnumbertea=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");	
				foreach($countnumbertea as $counttea){
						$countstea=$counttea['number'];
					}				
			}
		}
		
		//此处增加判断是老师进入还是家长进入
		if($countstea){
			//通过fromuser和gweid拿到班级
			if(!empty($fromuser)){
				$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
			}else{
				$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($teacher as $st){
				$gradeclass=$st['tea_gradeclass'];		
			}
			//通过班级拿到作业
			$grade=substr($gradeclass,0,4);
			$grades=$grade.'*';
			$homeworks=pdo_fetchall("SELECT * FROM wp_school_homework where GWEID=".$gweid." and homework_gradeclass='".$gradeclass."' or homework_gradeclass='*' or homework_gradeclass='".$grades."'");	
			$counthomeworks=	pdo_fetchall("SELECT count(*) as countnumbers FROM wp_school_homework where GWEID=".$gweid." and homework_gradeclass='".$gradeclass."' or homework_gradeclass='*' or homework_gradeclass='".$grades."'");
			foreach($counthomeworks as $ch){
				$chw=$ch['countnumbers'];
			}
			$counthomenum=get_user_meta($user_id,'school_homework_displaycount',true);
			if($chw>$counthomenum){
				$countnumber=$counthomenum;
			}else{
				$countnumber=$chw;
			}
			include $this -> template('homeworklist_teacher');
			
		}else{
				//通过fromuser和gweid拿到班级
			if(!empty($fromuser)){
				$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			}else{
				$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($student as $st){
				$gradeclass=$st['stu_gradeclass'];		
			}
			//通过班级拿到作业
			$grade=substr($gradeclass,0,4);
			$grades=$grade.'*';
			$homeworks=pdo_fetchall("SELECT * FROM wp_school_homework where GWEID=".$gweid." and homework_gradeclass='".$gradeclass."' or homework_gradeclass='*' or homework_gradeclass='".$grades."'");	
			$counthomeworks=	pdo_fetchall("SELECT count(*) as countnumbers FROM wp_school_homework where GWEID=".$gweid." and homework_gradeclass='".$gradeclass."' or homework_gradeclass='*' or homework_gradeclass='".$grades."'");		
			foreach($counthomeworks as $ch){
				$chw=$ch['countnumbers'];
			}
			$counthomenum=get_user_meta($user_id,'school_homework_displaycount',true);
			if($chw>$counthomenum){
				$countnumber=$counthomenum;
			}else{
				$countnumber=$chw;
			}
			include $this -> template('homeworklist');
		}		
	}
	
	//获取homelist页中homeworklist的当页偏移量
	public function doWebCountHomeWork($offset,$pagesize,$gweid,$fromuser){
		
		//首先判断一下，是老师的还是家长的显示
		if(!empty($fromuser)){	
			$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
		}else{
			$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
		}
		foreach($countnumber as $count){
			$counts=$count['number'];
		}
		if(!empty($fromuser)){
			$countnumbertea=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
		}else{
			$countnumbertea=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
		}
		foreach($countnumbertea as $counttea){
			$countstea=$counttea['number'];
		}
		
		if($counts){
			if(!empty($fromuser)){
				$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			}else{
				$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($student as $st){
				$gradeclass=$st['stu_gradeclass'];		
			}
			//通过班级拿到作业
			$grade=substr($gradeclass,0,4);
			$grades=$grade.'*';
			$myrows=pdo_fetchall("SELECT * FROM wp_school_homework where GWEID=".$gweid." and homework_gradeclass='".$gradeclass."' or homework_gradeclass='*' or homework_gradeclass='".$grades."' ORDER BY homework_id DESC limit ".$offset.",".$pagesize);	
			return $myrows;
		}else{
			if(!empty($fromuser)){
				$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
			}else{
				$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($teacher as $st){
				$gradeclass=$st['tea_gradeclass'];		
			}
			//通过班级拿到作业
			$grade=substr($gradeclass,0,4);
			$grades=$grade.'*';
			$myrows=pdo_fetchall("SELECT * FROM wp_school_homework where GWEID=".$gweid." and homework_gradeclass='".$gradeclass."' or homework_gradeclass='*' or homework_gradeclass='".$grades."' ORDER BY homework_id DESC limit ".$offset.",".$pagesize);	
			return $myrows;
		}
	}
	
	
	//获取notice中teacher当页的所有数据集
	public function doWebCountNoctieteacher($offset,$pagesize,$gweid,$fromuser)
	{	
		//通过fromuser和gweid拿到班级
		if(!empty($fromuser)){
			$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
		}else{
			$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
		}
		foreach($student as $st){
			$gradeclass=$st['stu_gradeclass'];
			$studentid=$st['stu_id'];
		}
		//通过班级拿到公告
		//老师：老师发给全部+老师发给改家长年级+老师发给改家长班级
		$grade=substr($gradeclass,0,4);
		$grades=$grade.'*';

		$myrows=pdo_fetchall("SELECT * FROM wp_school_notice w1, wp_school_teacher w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.tea_id AND w1.GWEID=".$gweid." and w1.notice_publisher like 't%' and (w1.notice_rights='".$gradeclass."' or w1.notice_rights='*' or w1.notice_rights='".$grades."') ORDER BY w1.notice_id DESC limit ".$offset.",".$pagesize);	
		return $myrows;
	}
	//获取notice中students当页的所有数据集
	public function doWebCountNoticestudents($offset,$pagesize,$gweid,$fromuser)
	{
		//通过fromuser和gweid拿到班级
		if(!empty($fromuser)){
			$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
		}else{
			$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
		}
		foreach($student as $st){
			$gradeclass=$st['stu_gradeclass'];
			$studentid=$st['stu_id'];
		}
		//通过班级拿到公告
		//老师：老师发给全部+老师发给改家长年级+老师发给改家长班级
		$grade=substr($gradeclass,0,4);
		$grades=$grade.'*';
		//家长：其他家长发给该班级

		$myrows=pdo_fetchall("SELECT * FROM wp_school_notice w1, wp_school_student w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.stu_id AND w1.GWEID=".$gweid." and w1.notice_publisher like 's%' and w1.notice_rights='".$gradeclass."' ORDER BY w1.notice_id DESC limit ".$offset.",".$pagesize);	
		return $myrows;
	}
	//获取notice中people当页的所有数据集
	public function doWebCountNoticepeoples($offset,$pagesize,$gweid,$fromuser)
	{
		//通过fromuser和gweid拿到班级
		if(!empty($fromuser)){
			$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
		}else{
			$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
		}
		foreach($student as $st){
			$gradeclass=$st['stu_gradeclass'];
			$studentid=$st['stu_id'];
		}
		//通过班级拿到公告
		//老师：老师发给全部+老师发给改家长年级+老师发给改家长班级
		$grade=substr($gradeclass,0,4);
		$grades=$grade.'*';
		//个人
		$studentid="s".$studentid;

		$myrows=pdo_fetchall("SELECT * FROM wp_school_notice w1, wp_school_student w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.stu_id AND w1.GWEID=".$gweid." and w1.notice_publisher='".$studentid."' ORDER BY w1.notice_id DESC limit ".$offset.",".$pagesize);
		return $myrows;
	}
	
	
	public function doMobileViewhomework(){
	
		global $_GPC;
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			$homework_id = $_GPC['homeworkId'];
			$homeworks = pdo_fetchall("SELECT * FROM wp_school_homework WHERE homework_id = ".$homework_id);
			foreach($homeworks as $homework )
			{
				$work_title= $homework['homework_title'];
				$work_content=$homework['homework_content'];
				$home_starttime=$homework['homework_starttime'];
				$home_endtime=$homework['homework_endtime'];
			}		
			include $this -> template('viewhomework');
		}else{
			$this->doMobileVerifyuser();			
		}
	}
	
	public function doWebExportnotice(){
		global $_GPC;
		$gweid=$_SESSION['GWEID'];
			
		$range=$_GPC['range'];
		$indata=$_GPC['indata'];

		$filename="公告表.csv";//先定义一个excel文件

		header("Content-Type: application/vnd.ms-execl"); 
		header("Content-Type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename"); 
		header("Pragma: no-cache"); 
		header("Expires: 0");

		//我们先在excel输出表头，当然这不是必须的
		echo iconv("utf-8", "gb2312", "公告编号").",";
		echo iconv("utf-8", "gb2312", "公告标题").",";
		echo iconv("utf-8", "gb2312", "公告内容").",";
		echo iconv("utf-8", "gb2312", "是否允许评论").",";//注意这个要换行
		echo iconv("utf-8", "gb2312", "公告查看权限").",";
		echo iconv("utf-8", "gb2312", "发布时间").",";
		echo iconv("utf-8", "gb2312", "发布人")."\n";
		//如果输入为空或者选择全部，则导出全部信息
		if(empty($indata) || $range=="all" || empty($range)){
			$notices=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid);
		}else{
			$notices=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID='".$gweid."' and ".$range." like '%".$indata."%'");
		}
		
		foreach($notices as $notice){

			echo iconv("utf-8", "gb2312", $notice['notice_id']).",";
			echo iconv("utf-8", "gb2312", $notice['notice_title']).",";
			echo iconv("utf-8", "gb2312", $notice['notice_content']).",";
			echo iconv("utf-8", "gb2312", $notice['notice_allowcomments']).",";
			echo iconv("utf-8", "gb2312", $notice['notice_rights']).",";
			echo iconv("utf-8", "gb2312", $notice['notice_date']).",";
			echo iconv("utf-8", "gb2312", $notice['notice_publisher'])."\n";

		}
    }	
	
	//获取根据查询条件得到的公告数据集
	public function doWebCountNoticeSearchPage($gweid,$indata,$rg,$offset,$pagesize)
	{
			
		global $wpdb;	
		
		if($rg=='notice_rights'){
			if(strpos($indata,'级所有班')!==false){
				$indata_notcie=substr($indata,0,4).'*';
				$myrows = pdo_fetchall("(SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata_notcie."%' AND w1.GWEID = ".$gweid." ) UNION 
							  (SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata_notcie."%' AND w3.GWEID = ".$gweid.") ORDER BY notice_id limit ".$offset.",".$pagesize);
			}else if(strpos($indata,'班所有老师')!==false){
				$indata_notcie=substr($indata,0,6).'t*';
				$myrows = pdo_fetchall("(SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata_notcie."%' AND w1.GWEID = ".$gweid." ) UNION 
							   (SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata_notcie."%' AND w3.GWEID = ".$gweid.") ORDER BY notice_id limit ".$offset.",".$pagesize);
			}else if(strpos($indata,'老师')!==false){
				$name=substr($indata,6);
				$teaname=substr($name,0,strpos($indata,'老师')-6);
				$glassgrade=substr($indata,0,6);
				$teacher_id=pdo_fetchall("SELECT * FROM wp_school_teacher w2 WHERE w2.tea_gradeclass='".$glassgrade."' and w2.tea_name='".$teaname."' and w2.GWEID= ".$gweid);
				foreach($teacher_id as $id){
					$tcher=$id['tea_id'];
				}
				$indata_notcie=substr($indata,0,6).'t'.$tcher;
				$myrows = pdo_fetchall("(SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata_notcie."%' AND w1.GWEID = ".$gweid." ) UNION 
							   (SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata_notcie."%' AND w3.GWEID = ".$gweid.") ORDER BY notice_id limit ".$offset.",".$pagesize);
			}else{
				$myrows = pdo_fetchall("(SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata."%' AND w1.GWEID = ".$gweid." ) UNION 
							   (SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata."%' AND w3.GWEID = ".$gweid.") ORDER BY notice_id limit ".$offset.",".$pagesize);
			}
		}else{
			$myrows = pdo_fetchall("(SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata."%' AND w1.GWEID = ".$gweid." ) UNION 
							   (SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata."%' AND w3.GWEID = ".$gweid.") ORDER BY notice_id limit ".$offset.",".$pagesize);
		}
		
		return $myrows;
	}
	
	//获取公告当页的所有数据集
	public function doWebCountNoticePage($gweid,$offset,$pagesize)
	{		 
		$myrows =pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." ORDER BY notice_id DESC limit ".$offset.",".$pagesize );
		return $myrows;
	}


	//公告管理页面
	public function doWebNoticemanage(){
		$gweid=$_SESSION['GWEID'];
	    global $wpdb, $_GPC;
	    //obtain userId
		global $current_user;
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;					
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
			
		$flag=$_GPC['flag'];
		$f=$_GPC["f"];
		if($f==null){$f=$flag;}
		$indata=$_GPC['Ipad'];
		$in=$_GPC["in"];
		if($in==null){$in=$indata;}
		$indata=$in;
		$rg=$_GPC['range'];
		$r=$_GPC["r"];
		if($r==null){$r=$rg;}
	    $rg=$r;
		//删除公告
		if(isset($_GPC['del']) && !empty($_GPC['del']) ){							
			pdo_delete('school_notice',array('notice_id' => $_GPC['del']));
		}
		
		if($rg=='notice_rights'){
			if(strpos($indata,'级所有班')!==false){
				$indata_notice=substr($indata,0,4).'*';
				$noticesCount=pdo_fetchall("SELECT COUNT(*) as noticeCount FROM ((SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata_notice."%' AND w1.GWEID = ".$gweid." ) UNION 
				(SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata_notice."%' AND w3.GWEID = ".$gweid.")) as notice ");
			}else if(strpos($indata,'班所有老师')!==false){
				$indata_notice=substr($indata,0,6).'*';
				$noticesCount=pdo_fetchall("SELECT COUNT(*) as noticeCount FROM ((SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata_notice."%' AND w1.GWEID = ".$gweid." ) UNION 
				(SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata_notice."%' AND w3.GWEID = ".$gweid.")) as notice ");	
			}else if(strpos($indata,'老师')!==false){
				$name=substr($indata,6);
				$teaname=substr($name,0,strpos($indata,'老师')-6);
				$glassgrade=substr($indata,0,6);
				$teacher_id=pdo_fetchall("SELECT * FROM wp_school_teacher w2 WHERE w2.tea_gradeclass='".$glassgrade."' and w2.tea_name='".$teaname."' and w2.GWEID= ".$gweid);
				foreach($teacher_id as $id){
					$tcher=$id['tea_id'];
				}
				$indata_notice=substr($indata,0,6).'t'.$tcher;
				$noticesCount=pdo_fetchall("SELECT COUNT(*) as noticeCount FROM ((SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata_notice."%' AND w1.GWEID = ".$gweid." ) UNION 
				(SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata_notice."%' AND w3.GWEID = ".$gweid.")) as notice ");
			}else{
				$noticesCount=pdo_fetchall("SELECT COUNT(*) as noticeCount FROM ((SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata."%' AND w1.GWEID = ".$gweid." ) UNION 
				(SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata."%' AND w3.GWEID = ".$gweid.")) as notice ");
			}
		}else{
			$noticesCount=pdo_fetchall("SELECT COUNT(*) as noticeCount FROM ((SELECT w1.notice_id,w1.notice_title,w1.notice_content,w1.notice_allowcomments,w1.notice_rights,w1.notice_date,w1.notice_publisher FROM wp_school_notice w1, wp_school_teacher w2 WHERE (SUBSTR(w1.notice_publisher,1,1) = 't' and SUBSTR(w1.notice_publisher,2) = w2.tea_id) AND ".($rg=='notice_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata."%' AND w1.GWEID = ".$gweid." ) UNION 
				(SELECT w3.notice_id,w3.notice_title,w3.notice_content,w3.notice_allowcomments,w3.notice_rights,w3.notice_date,w3.notice_publisher FROM wp_school_notice w3, wp_school_student w4 WHERE (SUBSTR(w3.notice_publisher,1,1) = 's' and SUBSTR(w3.notice_publisher,2) = w4.stu_id) AND ".($rg=='notice_publisher'?'w4.stu_name':'w3.'.$rg)." like '%".$indata."%' AND w3.GWEID = ".$gweid.")) as notice ");
		}
		
		//获取所有的公告的个数
		$noticeCount = pdo_fetchall("SELECT COUNT(*) as noticeCount FROM wp_school_notice where GWEID=".$gweid);
		
		include $this -> template('noticemanage');
    }
	
	//获取某个特定id的老师
	public function doWebSelecttea($tid){
		$myrows =pdo_fetchall("SELECT * FROM wp_school_teacher WHERE tea_id=".$tid);
		return $myrows;
	}
	public function doWebSelectteacher($teid){
		$myrows =pdo_fetchall("SELECT * FROM wp_school_teacher WHERE tea_id=".$teid);
		return $myrows;
	}
	public function doWebSelectstudent($stid){
		$myrows =pdo_fetchall("SELECT * FROM wp_school_student WHERE stu_id=".$stid);
		return $myrows;
	}
	//获取所有的年级/班级
	public function doWebNoticeGradeclass()
	{
		$myrows =pdo_fetchall("SELECT DISTINCT notice_gradeclass FROM wp_school_notice");

		return $myrows;
	}
	//家长添加公告页面
	public function doMobileAddFamilyNotice(){
		global $wpdb, $_GPC;
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			//找到该家长可以显示的老师
			//先找到家长对应的年级
			if(!empty($fromuser)){
				$grade_family=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'" );	
			}else{
				$grade_family=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'" );	
			}
			foreach($grade_family as $family){
				$familu_g=$family['stu_gradeclass'];
			}
			//找到所有的老师对应的gradeclass
			$all_teacher=pdo_fetchall("SELECT  DISTINCT tea_gradeclass FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
			//找到所有的老师对应的名字
			$all_teaname=pdo_fetchall("SELECT  DISTINCT tea_name,tea_id FROM wp_school_teacher where GWEID=".$gweid." AND tea_gradeclass='".$familu_g."' ORDER BY tea_id");
	 
			if( isset($_GPC['notice_title']) ){
				$htmlData = '';
				$notice_title=$_GPC['notice_title'];
				$notice_content = $_GPC['notice_content'];
				$home_gradeclass = $_GPC['home_gradeclass'];
				$radioteacher=$_GPC['radioteacher'];
				if($radioteacher==1){
					$teacher_name=$_GPC['teacher_name'];
				}else{
					$teacher_name=null;
				}
				if($teacher_name==null){
					$notice_right = $home_gradeclass;
				}else{
					$notice_right = $home_gradeclass.'t'.$teacher_name;
				}
				$commentSelected=$_GPC['commentSelected'];
				if(!empty($fromuser)){	
					$stuid = pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
				}else{
					$stuid = pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				}
				foreach($stuid as $student){
					$stu_id=$student['stu_id'];
				}
				
				$stu_id='s'.$stu_id;
				date_default_timezone_set('PRC');
				$date=date("Y-m-d H:i:s");
				
				$Status =pdo_insert('school_notice',array('notice_title' => $notice_title, 'notice_content' => $notice_content, 'notice_allowcomments' => $commentSelected, 'notice_rights'  =>$notice_right, 'notice_date'  =>$date, 'notice_publisher'  =>$stu_id,'GWEID' => $gweid));			
				
				if($Status!==false) {
					$flag = true;
					$countmember = false;
				}else{
				?>
				   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<script>
							alert("发布失败");
						</script>
					</head>
					</html>	
				<?php
				}			
			}
					
			include $this -> template('addnotice_family');
		}else{
			$this->doMobileVerifyuser();			
		}
		
		
    }
	//老师添加公告页面
	public function doMobileAddTeacherNotice(){
		global $wpdb, $_GPC;
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			 //取到老师的信息
			 if(!empty($fromuser)){
				$tea_info=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
			}else{
				$tea_info=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($tea_info as $tinfo){
				$teainfo=$tinfo['tea_gradeclass'];
			}
			
			//2014-07-13新增修改，显示的是从老师和学生表中共同决定的年级和班级
			$allgradeclass = pdo_fetchall("(SELECT  distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid.") ORDER BY tea_gradeclass");
			//找到所有的年级
			$all_gc = pdo_fetchall("(SELECT  distinct(SUBSTR(`tea_gradeclass`,1,4)) as sub_tea FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(SUBSTR(`stu_gradeclass`,1,4)) FROM wp_school_student where GWEID=".$gweid.") ORDER BY sub_tea");
			

			if( isset($_GPC['notice_title']) ){
				$notice_title=$_GPC['notice_title'];
				$notice_content = $_GPC['notice_content'];
				$home_gradeclass = $_GPC['home_gradeclass'];
				$notice_gradeclass=$_GPC['notice_gradeclass'];
				$notice_right = $home_gradeclass;
				$commentSelected=$_GPC['commentSelected'];	
				if(!empty($fromuser)){
					$teaid = pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
				}else{
					$teaid = pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				}
				foreach($teaid as $teacher){
					$tea_id=$teacher['tea_id'];
				}
				
				
				$tea_id='t'.$tea_id;
				date_default_timezone_set('PRC');
				$date=date("Y-m-d H:i:s");
				
				$Status =pdo_insert('school_notice',array('notice_title' => $notice_title, 'notice_content' => $notice_content, 'notice_allowcomments' => $commentSelected, 'notice_rights'  =>$notice_right, 'notice_date'  =>$date, 'notice_publisher'  =>$tea_id,'GWEID' => $gweid));			
				
				if($Status!==false) {
					$flag = true;
					$countmember = false;
				}else{
				?>
				   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<script>
							alert("发布失败");
						</script>
					</head>
					</html>	
				<?php
				}			

			}
					
			include $this -> template('addnotice_teacher');
		}else{
			$this->doMobileVerifyuser();			
		}
		
    }

	//获取对应id的公告信息
	function doWebGetNoticeById($nid)
	{	
		$myrows =pdo_fetchall("SELECT * FROM wp_school_notice where notice_id=".$nid);
		return $myrows;
		
	}
	//编辑公告页面
	public function doWebEditNotice(){
		 global $wpdb, $_GPC;
		//获取session中的gweid
	    $gweid = $_SESSION['GWEID'];
		$notice_id=$_GPC["noticeId"];
		
		if( isset($_GPC['notice_title']) &&!empty($_GPC['notice_title'])){
		
			$htmlData = '';
			$notice_title=$_GPC['notice_title'];
			$htmlData = stripslashes($_GPC['content1']);
			$notice_grade=$_GPC['notice_grade'];
			$notice_class=$_GPC['notice_class'];
			$commentSelected=$_GPC['commentSelected'];		   
			//$rights='g'.$notice_grade.'c'.$notice_class;
			$rights=$_GPC['notice_gradeclass'];
			pdo_update('school_notice',array('notice_title'=>$notice_title,'notice_content'=>$htmlData, 'notice_allowcomments'=>$commentSelected,'notice_rights'=>$rights),array('notice_id'=>$notice_id));
			
			echo"<script language='JavaScript'>";
			echo"setTimeout('self.close()',0);";
			echo"opener.location.reload();";
			echo"</script>";
		}

        $notices = pdo_fetchall("SELECT * FROM wp_school_notice WHERE notice_id = ".$notice_id);
		foreach($notices as $notice )
		{
			$notice_title= $notice['notice_title'];
			$notice_content=$notice['notice_content'];
			$notice_allowcomments=$notice['notice_allowcomments'];
			$notice_rights=$notice['notice_rights'];
		}
		$allgradeclass=pdo_fetchall( "SELECT DISTINCT tea_gradeclass FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
		//显示所有的年级
		$all_gc=pdo_fetchall("SELECT DISTINCT SUBSTR(tea_gradeclass,1,4) as sub_tea FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
		
        include $this -> template('editnotice');
	    
    }
	
	//查询公告页面
	public function doWebSearchnotice(){
		global $_GPC;
	    $range = $_GPC['range'];	
	    $indata = $_GPC['indata'];
        include $this -> template('searchnotice');
    }
	public function doMobileNoticelist(){
		global $_GPC;
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		//2014-07-10新增修改，根据登录时的身份
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount){	
			$this->doMobileNoticeteacherlist();
		
		}else if($stucount){
			$this->doMobileNoticefamilylist();
		}else{
			$this->doMobileVerifyuser();			
		}
	}
	
	
	
	public function doMobileNoticefamilylist(){
		global $_GPC;
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			//取到user_id
			$userid=pdo_fetchall("SELECT * FROM wp_wechat_usechat where GWEID=".$gweid);
			foreach($userid as $uid){
				$user_id=$uid['user_id'];
			}
			$tab_ul=$_GPC['tab_ul'];
			
			if(!empty($fromuser)){	
				$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			}else{
				$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($countnumber as $count){
				$counts=$count['number'];
			}
			
			//通过fromuser和gweid拿到班级
			if(!empty($fromuser)){	
				$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			}else{
				$student=pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($student as $st){
				$gradeclass=$st['stu_gradeclass'];
				$studentid=$st['stu_id'];
			}
			//通过班级拿到公告
			//老师：老师发给全部+老师发给改家长年级+老师发给改家长班级
			$grade=substr($gradeclass,0,4);
			$grades=$grade.'*';
			$noticeteachers=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." and notice_publisher like 't%' and (notice_rights='".$gradeclass."' or notice_rights='*' or notice_rights='".$grades."')");
			$countnoticeteachers=pdo_fetchall("SELECT count(*) as Cnoticeteachers FROM wp_school_notice w1, wp_school_teacher w2 WHERE  SUBSTR(w1.notice_publisher,2)=w2.tea_id AND w1.GWEID=".$gweid." and w1.notice_publisher like 't%' and (w1.notice_rights='".$gradeclass."' or w1.notice_rights='*' or w1.notice_rights='".$grades."')");
			foreach($countnoticeteachers as $cnt){
				$cntc=$cnt['Cnoticeteachers'];
			}
			$countnoticenum=get_user_meta($user_id,'school_notice_displaycount',true);
				if($cntc>$countnoticenum){
					$countteacher=$countnoticenum;
				}else{
					$countteacher=$cntc;
				}
			//家长：其他家长发给该班级
			$noticestudents=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." and notice_publisher like 's%' and notice_rights='".$gradeclass."'");	
			$countnoticestudents=pdo_fetchall("SELECT count(*) as Cnoticestudents FROM wp_school_notice w1, wp_school_student w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.stu_id AND w1.GWEID=".$gweid." and w1.notice_publisher like 's%' and w1.notice_rights='".$gradeclass."'");	
			foreach($countnoticestudents as $cns){
				$cnsc=$cns['Cnoticestudents'];
			}
			$countnoticenum=get_user_meta($user_id,'school_notice_displaycount',true);
				if($cnsc>$countnoticenum){
					$countnumber=$countnoticenum;
				}else{
					$countnumber=$cnsc;
				}
			//个人
			$studentid="s".$studentid;
			$noticepeoples=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." and notice_publisher='".$studentid."'");
			$countnoticepeoples=pdo_fetchall("SELECT count(*) as Cnoticepeoples FROM wp_school_notice w1, wp_school_student w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.stu_id AND w1.GWEID=".$gweid." and w1.notice_publisher='".$studentid."'");
			foreach($countnoticepeoples as $cnp){
				$cnpc=$cnp['Cnoticepeoples'];
			}
			$countnoticenum=get_user_meta($user_id,'school_notice_displaycount',true);
				if($cnpc>$countnoticenum){
					$countpeople=$countnoticenum;
				}else{
					$countpeople=$cnpc;
				}
			
			include $this -> template('noticefamilylist');
		
		}else{
			$this->doMobileVerifyuser();			
		}
		
	}
	
	public function doMobileNoticeteacherlist(){
		global $_GPC;
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		
		//2014-07-10新增修改，根据登录时的身份
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			//取到user_id
			$userid=pdo_fetchall("SELECT * FROM wp_wechat_usechat where GWEID=".$gweid);
			foreach($userid as $uid){
				$user_id=$uid['user_id'];
			}
			$tab_ul=$_GPC['tab_ul'];
			if(!empty($fromuser)){
				$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
			}else{
				$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($countnumber as $count){
				$counts=$count['number'];
			}
			
			//通过fromuser和gweid拿到班级
			if(!empty($fromuser)){
				$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
			}else{
				$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
			}
			foreach($teacher as $ta){
				$gradeclass=$ta['tea_gradeclass'];
				$teacherid=$ta['tea_id'];
			}
			//通过班级拿到公告
			//老师：老师发给全部+老师发给年级+老师发给班级
			$grade=substr($gradeclass,0,4);
			$grades=$grade.'*';
			$noticeteachers=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." and notice_publisher like 't%' and (notice_rights='".$gradeclass."' or notice_rights='*' or notice_rights='".$grades."')");	
			$countnoticeteachers=pdo_fetchall("SELECT count(*) as Cteachers FROM wp_school_notice w1, wp_school_teacher w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.tea_id AND w1.GWEID=".$gweid." and w1.notice_publisher like 't%' and (w1.notice_rights='".$gradeclass."' or w1.notice_rights='*' or w1.notice_rights='".$grades."')");	
			foreach($countnoticeteachers as $cnt){
				$cntc=$cnt['Cteachers'];
			}
			$countnoticenum=get_user_meta($user_id,'school_notice_displaycount',true);
				if($cntc>$countnoticenum){
					$countteachers=$countnoticenum;
				}else{
					$countteachers=$cntc;
				}
			//家长：其他家长发给该班级所有人（其他家长也能看）+家长发给改班级所有老师+家长发给该老师
			$tch=$gradeclass."t".$teacherid;
			$tchall=$gradeclass."t"."*";
			$noticestudents=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." and notice_publisher like 's%' and (notice_rights='".$gradeclass."' or notice_rights='".$tchall."' or notice_rights='".$tch."')");	
			$countnoticestudents=pdo_fetchall("SELECT count(*) as Cstudents FROM wp_school_notice w1, wp_school_student w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.stu_id AND w1.GWEID=".$gweid." and w1.notice_publisher like 's%' and (w1.notice_rights='".$gradeclass."' or w1.notice_rights='".$tchall."' or w1.notice_rights='".$tch."')");
			foreach($countnoticestudents as $cns){
				$cnsc=$cns['Cstudents'];
			}
			$countnoticenum=get_user_meta($user_id,'school_notice_displaycount',true);
				if($cnsc>$countnoticenum){
					$countstudents=$countnoticenum;
				}else{
					$countstudents=$cnsc;
				}
			//个人
			$teacherid="t".$teacherid;
			$noticepeoples=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." and notice_publisher='".$teacherid."'");
			$countnoticepeoples=pdo_fetchall("SELECT count(*) as Cpeoples FROM wp_school_notice w1, wp_school_teacher w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.tea_id AND w1.GWEID=".$gweid." and w1.notice_publisher='".$teacherid."'");
			foreach($countnoticepeoples as $cnp){
				$cnpc=$cnp['Cpeoples'];
			}
			$countnoticenum=get_user_meta($user_id,'school_notice_displaycount',true);
				if($cnpc>$countnoticenum){
					$countpeople=$countnoticenum;
				}else{
					$countpeople=$cnpc;
				}
				
			include $this -> template('noticeteacherlist');
		}else{
			$this->doMobileVerifyuser();			
		}
	}
	//下面三个函数是翻页使用的分别是老师，家长，个人的函数
		//老师
	public function doWebteacherNoticeteacher($offset,$pagesize,$gweid,$fromuser)
	{
		//通过fromuser和gweid拿到班级
		if(!empty($fromuser)){
			$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
		}else{
			$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
		}
		foreach($teacher as $ta){
			$gradeclass=$ta['tea_gradeclass'];
			$teacherid=$ta['tea_id'];
		}
		//通过班级拿到公告
		//老师：老师发给全部+老师发给年级+老师发给班级
		$grade=substr($gradeclass,0,4);
		$grades=$grade.'*';

		$myrows=pdo_fetchall("SELECT * FROM wp_school_notice w1, wp_school_teacher w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.tea_id AND w1.GWEID=".$gweid." and w1.notice_publisher like 't%' and (w1.notice_rights='".$gradeclass."' or w1.notice_rights='*' or w1.notice_rights='".$grades."') ORDER BY w1.notice_id DESC limit ".$offset.",".$pagesize);
		return $myrows;
	}
	//家长
		public function doWebteacherNoticefamily($offset,$pagesize,$gweid,$fromuser)
	{
		//通过fromuser和gweid拿到班级
		if(!empty($fromuser)){
			$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
		}else{
			$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
		}
		foreach($teacher as $ta){
			$gradeclass=$ta['tea_gradeclass'];
			$teacherid=$ta['tea_id'];
		}
		//通过班级拿到公告
		//老师：老师发给全部+老师发给年级+老师发给班级
		$grade=substr($gradeclass,0,4);
		$grades=$grade.'*';
		//家长：其他家长发给该班级所有人（其他家长也能看）+家长发给改班级所有老师+家长发给该老师
		$tch=$gradeclass."t".$teacherid;
		$tchall=$gradeclass."t"."*";

		$myrows=pdo_fetchall("SELECT * FROM wp_school_notice w1, wp_school_student w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.stu_id AND w1.GWEID=".$gweid." and w1.notice_publisher like 's%' and (w1.notice_rights='".$gradeclass."' or w1.notice_rights='".$tchall."' or w1.notice_rights='".$tch."') ORDER BY w1.notice_id DESC limit ".$offset.",".$pagesize);	
		return $myrows;
	}
		//个人
		public function doWebteacherNoticepersons($offset,$pagesize,$gweid,$fromuser)
	{
		//通过fromuser和gweid拿到班级
		if(!empty($fromuser)){
			$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
		}else{
			$teacher=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
		}
		foreach($teacher as $ta){
			$gradeclass=$ta['tea_gradeclass'];
			$teacherid=$ta['tea_id'];
		}
		//通过班级拿到公告
		//老师：老师发给全部+老师发给年级+老师发给班级
		$grade=substr($gradeclass,0,4);
		$grades=$grade.'*';
		$noticeteachers=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." and notice_publisher like 't%' and (notice_rights='".$gradeclass."' or notice_rights='*' or notice_rights='".$grades."')");	
		//家长：其他家长发给该班级所有人（其他家长也能看）+家长发给改班级所有老师+家长发给该老师
		$tch=$gradeclass."t".$teacherid;
		$tchall=$gradeclass."t"."*";
		$noticestudents=pdo_fetchall("SELECT * FROM wp_school_notice where GWEID=".$gweid." and notice_publisher like 's%' and (notice_rights='".$gradeclass."' or notice_rights='".$tchall."' or notice_rights='".$tch."')");	
		//个人
		$teacherid="t".$teacherid;

		$myrows=pdo_fetchall("SELECT * FROM wp_school_notice w1, wp_school_teacher w2 WHERE SUBSTR(w1.notice_publisher,2)=w2.tea_id AND w1.GWEID=".$gweid." and w1.notice_publisher='".$teacherid."' ORDER BY w1.notice_id DESC limit ".$offset.",".$pagesize);
		return $myrows;
	}
		
		public function doMobileViewfamilynotice(){
	
			global $_GPC;
			
			//2014-07-11新增修改
			$gweid =  $_GPC['gweid'];
			$fromuser = $_SESSION['fromuser'];
			
			/*2014-12-07添加身份验证*/
			$teacount = 0;
			$stucount = 0;
			if(!empty($fromuser)){
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
			
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			
			}else{
				//2014-07-10新增修改，根据登录时的身份
				if($_SESSION['user_type'] == "teacher")
				{
					$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
					foreach($countteas as $counttea){
						$teacount=$counttea['number'];
					}
				}
				else
				{
					$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
					foreach($countstus as $countstu){
						$stucount=$countstu['number'];
					}
				}
			}	
			
			if($teacount || $stucount){	
				$notice_id = $_GPC['noticeId'];		
				$notices = pdo_fetchall("SELECT * FROM wp_school_notice WHERE notice_id = ".$notice_id);
				foreach($notices as $notice )
				{
					$notice_title= $notice['notice_title'];
					$notice_content=$notice['notice_content'];
					$notice_allowcomments=$notice['notice_allowcomments'];
					$notice_rights=$notice['notice_rights'];
					$notice_date=$notice['notice_date'];
				}
				
				include $this -> template('viewfamilynotice');
			}else{
				$this->doMobileVerifyuser();			
			}
				
		}
		public function doMobileViewteachernotice(){
	
			global $_GPC;
			
			//2014-07-11新增修改
			$gweid =  $_GPC['gweid'];
			$fromuser = $_SESSION['fromuser'];
			
			/*2014-12-07添加身份验证*/
			$teacount = 0;
			$stucount = 0;
			if(!empty($fromuser)){
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
			
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			
			}else{
				//2014-07-10新增修改，根据登录时的身份
				if($_SESSION['user_type'] == "teacher")
				{
					$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
					foreach($countteas as $counttea){
						$teacount=$counttea['number'];
					}
				}
				else
				{
					$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
					foreach($countstus as $countstu){
						$stucount=$countstu['number'];
					}
				}
			}	
			
			if($teacount || $stucount){	
				$notice_id = $_GPC['noticeId'];		
				$notices = pdo_fetchall("SELECT * FROM wp_school_notice WHERE notice_id = ".$notice_id);
				foreach($notices as $notice )
				{
					$notice_title= $notice['notice_title'];
					$notice_content=$notice['notice_content'];
					$notice_allowcomments=$notice['notice_allowcomments'];
					$notice_rights=$notice['notice_rights'];
					$notice_date=$notice['notice_date'];
				}
				
				include $this -> template('viewteachernotice');
			
			}else{
				$this->doMobileVerifyuser();			
			}
			
		}
	
	//获取根据查询条件得到的评论数据集
	public function doWebCountReplySearchPage($gweid,$indata,$rg,$offset,$pagesize,$ntid)
	{
			
		//global $wpdb;
		$myrows=pdo_fetchall("SELECT * FROM ((SELECT w1.reply_id,w1.reply_content,w1.reply_time,w1.reply_author FROM wp_school_reply w1, wp_school_teacher w2 WHERE (SUBSTR(w1.reply_author,1,1) = 't' and SUBSTR(w1.reply_author,2) = w2.tea_id) AND ".($rg=='reply_author'?'w2.tea_name':'w1.'.$rg)." like '%".$indata."%' AND w1.notice_id=".$ntid." AND w1.GWEID = ".$gweid." ) UNION 
	  															   (SELECT w3.reply_id,w3.reply_content,w3.reply_time,w3.reply_author FROM wp_school_reply w3, wp_school_student w4 WHERE (SUBSTR(w3.reply_author,1,1) = 's' and SUBSTR(w3.reply_author,2) = w4.stu_id) AND ".($rg=='reply_author'?'w4.stu_name':'w3.'.$rg)." like '%".$indata."%' AND w3.notice_id=".$ntid." AND w3.GWEID = ".$gweid.")) as reply ORDER BY reply_id DESC limit ".$offset.",".$pagesize);
		
		return $myrows;
	}
	
	//获取评论当页的所有数据集
	public function doWebCountReplyPage($gweid,$offset,$pagesize,$ntid)
	{		 
		$myrows =pdo_fetchall("SELECT * FROM wp_school_reply where GWEID=".$gweid." and notice_id=".$ntid."  ORDER BY  reply_id DESC limit ".$offset.",".$pagesize);
	
		return $myrows;
	}


	//评论管理页面
	public function doWebReplymanage(){
		$gweid=$_SESSION['GWEID'];
	    global $wpdb, $_GPC;
	    //obtain userId
		global $current_user;	
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;				
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;

		$flag=$_GPC['flag'];
		$f=$_GPC["f"];
		if($f==null){$f=$flag;}
		$indata=$_GPC['Ipad'];
		$in=$_GPC["in"];
		if($in==null){$in=$indata;}
		$indata=$in;
		$rg=$_GPC['range'];
		$r=$_GPC["r"];
		if($r==null){$r=$rg;}
		$rg=$r;
		$noc_id=$_GPC['noticeId'];
		$notice_id=$_GPC["notice_id"];
		if($notice_id==null){$notice_id=$noc_id;}
		$noc_id=$notice_id;
	
		//删除评论
		if(isset($_GPC['del']) && !empty($_GPC['del']) ){						
			pdo_delete('school_reply',array('reply_id' => $_GPC['del']));
		}
		
	    $replysCount=pdo_fetchall("SELECT COUNT(*) as replyCount FROM ((SELECT w1.reply_id,w1.reply_content,w1.reply_time,w1.reply_author FROM wp_school_reply w1, wp_school_teacher w2 WHERE (SUBSTR(w1.reply_author,1,1) = 't' and SUBSTR(w1.reply_author,2) = w2.tea_id) AND ".($rg=='reply_author'?'w2.tea_name':'w1.'.$rg)." like '%".$indata."%' AND w1.notice_id=".$ntid." AND w1.GWEID = ".$gweid." ) UNION 
	  															   (SELECT w3.reply_id,w3.reply_content,w3.reply_time,w3.reply_author FROM wp_school_reply w3, wp_school_student w4 WHERE (SUBSTR(w3.reply_author,1,1) = 's' and SUBSTR(w3.reply_author,2) = w4.stu_id) AND ".($rg=='reply_author'?'w4.stu_name':'w3.'.$rg)." like '%".$indata."%' AND w3.notice_id=".$ntid." AND w3.GWEID = ".$gweid.")) as reply ");
		
	
		//获取所有的公告的个数
		$replyCount = pdo_fetchall("SELECT COUNT(*) as replyCount FROM wp_school_reply where GWEID=".$gweid." and notice_id=".$notice_id);
		 
		include $this -> template('replymanage');
    }
	

	//获取对应id的评论信息
	function doWebGetReplyById($rid)
	{
		$myrows =pdo_fetchall("SELECT * FROM wp_school_reply where reply_id=".$rid);		
		return $myrows;
		
	}
	
	//查询评论页面
	public function doWebSearchreply(){
		global $_GPC;
	    $range = $_GPC['range'];	
	    $indata = $_GPC['indata'];
		$notice_id = $_GPC['notice_id'];
        include $this -> template('searchreply');
    }
	//评论人转换
	public function doWebSelectReplyteacher($teid){
		$myrows =pdo_fetchall("SELECT * FROM wp_school_teacher WHERE tea_id=".$teid);

		return $myrows;
	}
	public function doWebSelectReplystudent($stid){
		$myrows =pdo_fetchall("SELECT * FROM wp_school_student WHERE stu_id=".$stid);

		return $myrows;
	}
	
	
	public function doMobileReplyfamilylist(){
		global $_GPC;
		$notice_id = $_GPC['noticeId'];	
		$no=$_GPC["no"];
		if($no==null){$no=$notice_id;}
		$notice_id=$no;
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			if( isset($_GPC['reply_content']) ){
				if(!empty($fromuser)){
					$stuid = pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
				}else{
					$stuid = pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				}
				foreach($stuid as $student){
					$stu_id=$student['stu_id'];
				}
				
				$stu_id='s'.$stu_id;
				
				$reply_content=$_GPC['reply_content'];			
				//$reply_time=$_GPC['reply_time'];
				date_default_timezone_set('PRC');
				$reply_time=date("Y-m-d");
				$reply_author=$stu_id;
						   
				
				pdo_insert('school_reply',array('reply_content' => $reply_content, 'reply_time' => $reply_time, 'reply_author' => $reply_author, 'notice_id'  =>$notice_id,'GWEID' => $gweid));
			}
			
			$replys=pdo_fetchall("SELECT * FROM wp_school_reply where GWEID=".$gweid." and notice_id=".$notice_id);
			$countreplys=pdo_fetchall("SELECT count(*) as Creply FROM wp_school_reply where GWEID=".$gweid." and notice_id=".$notice_id);
			
		}else{
			$this->doMobileVerifyuser();			
		}
		include $this -> template('replyfamilylist');
	}	
	//reply family用到的翻页
	public function doWebReplyfamily($offset,$pagesize,$gweid,$notice_id){
		$myrows=pdo_fetchall("SELECT * FROM wp_school_reply where GWEID=".$gweid." and notice_id=".$notice_id." ORDER BY reply_id DESC limit ".$offset.",".$pagesize);
		return $myrows;
	}
	//用于显示reply查询的teacher的名字
	public function doWebReplyfamilytname($teaid){
		$myrows=pdo_fetchall("SELECT tea_name FROM wp_school_teacher where tea_id=".$teaid);
		return $myrows;
	}
	//用于显示reply查询的family的名字
	public function doWebReplyfamilysname($stuid){
		$myrows=pdo_fetchall("SELECT stu_name FROM wp_school_student where stu_id=".$stuid);
		return $myrows;
	}
	
	
	public function doMobileReplyteacherlist(){
		global $_GPC;
		$notice_id = $_GPC['noticeId'];	
		$no=$_GPC["no"];
		if($no==null){$no=$notice_id;}
		$notice_id=$no;
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			if( isset($_GPC['reply_content']) ){
				if(!empty($fromuser)){
					$teaid = pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
				}else{
					$teaid = pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				}
				foreach($teaid as $teacher){
					$tea_id=$teacher['tea_id'];
				}
				
				$tea_id='t'.$tea_id;
				
				$reply_content=$_GPC['reply_content'];			
				//$reply_time=$_GPC['reply_time'];
				date_default_timezone_set('PRC');
				$reply_time=date("Y-m-d");
				$reply_author=$tea_id;
						   
				
				pdo_insert('school_reply',array('reply_content' => $reply_content, 'reply_time' => $reply_time, 'reply_author' => $reply_author, 'notice_id'  =>$notice_id,'GWEID' => $gweid));
			}
			
			$replys=pdo_fetchall("SELECT * FROM wp_school_reply where GWEID=".$gweid." and notice_id=".$notice_id);
			$countreplys=pdo_fetchall("SELECT count(*) as Cteacher FROM wp_school_reply where GWEID=".$gweid." and notice_id=".$notice_id);
			
		}else{
			$this->doMobileVerifyuser();			
		}
		
		include $this -> template('replyteacherlist');
	}	
	//reply teacher用到的翻页
	public function doWebReplyteacher($offset,$pagesize,$gweid,$notice_id){
		$myrows=pdo_fetchall("SELECT * FROM wp_school_reply where GWEID=".$gweid." and notice_id=".$notice_id." ORDER BY reply_id DESC limit ".$offset.",".$pagesize);
		return $myrows;
	}
	
	public function doWebTest(){
		global $wpdb,$_W;
		pdo_delete('wesite_space',array('userid' => 0));
		var_dump(pdo_fetchall("SELECT * FROM wp_posts WHERE ID= :id LIMIT 2",array(':id' => 700)));
		echo md5(serialize(pdo_fetchall("SELECT * FROM wp_posts WHERE ID= :id LIMIT 2",array(':id' => 700))));
	}

	//微学校访问入口
	public function doMobileIndex(){
	    global $_GPC, $_W;
		$gweid =  $_GPC['gweid'];
		
		$_SESSION['GWEID'] = $gweid;
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		$select_url = pdo_fetchall("SELECT * FROM wp_school WHERE GWEID=".$gweid);

		include $this -> template('index');
	}
	//有关学生
	//ma获取根据查询条件得到的学生数据集
	public function doWebCountStudentSearchPage($indata,$rg,$offset,$pagesize,$gweid)
	{	
		$myrows = pdo_fetchall("SELECT * FROM wp_school_student w1 WHERE w1.".$rg." like '%".$indata."%' AND w1.GWEID = ".$gweid." ORDER BY w1.stu_id limit ".$offset.",".$pagesize);
		return $myrows;
	}
	//ma获取所有的student的个数
	public function doWebCountStudent($gweid)
	{
		$myrows = pdo_fetchall("SELECT COUNT(*) as userCount FROM wp_school_student WHERE GWEID = ".$gweid);							
		return $myrows;
	}
	//获取student当页的所有数据集
	public function doWebCountStudentPage($offset,$pagesize,$gweid)
	{
		
		$myrows = pdo_fetchall( "SELECT * FROM wp_school_student WHERE GWEID = ".$gweid." ORDER BY stu_id limit ".$offset.",".$pagesize );
		return $myrows;
	}
	//学生管理页面
	public function doWebStudentmanage(){
	
	    global $_GPC;
	    //obtain userId
		global $current_user;	
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;				
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];

	    $flag=$_GPC['flag'];
	    $f=$_GPC["f"];
	    if($f==null){$f=$flag;}
	    $indata=$_GPC['Ipad'];
	    $in=$_GPC["in"];
	    if($in==null){$in=$indata;}
	    $indata=$in;
		$rg=$_GPC['range'];
		$r=$_GPC["r"];
		if($r==null){$r=$rg;}
		$rg=$r;
		//删除学生
		if(isset($_GPC['del']) && !empty($_GPC['del']) ){
		
			pdo_delete('school_student',array('stu_id' => $_GPC['del']));
		}
		
		$smembersCount = pdo_fetchall("SELECT COUNT(*) as memberCount FROM wp_school_student w1 WHERE w1.".$rg." like '%".$indata."%' AND w1.GWEID = ".$gweid);
		$usersCount = $this -> doWebCountStudent($gweid);
		
        include $this -> template('studentmanage');
    }
	//导入学生信息excel
	public function doWebUploadstudent(){
		error_reporting(E_ERROR);
	    //获取session中的gweid
	    $gweid=$_SESSION['GWEID'];
	
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel.php';
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel/IOFactory.php';
		require_once MODULES_DIR.$this -> module['name'].'/upload/PHPExcel/Reader/Excel5.php';

		$filename = $_FILES['inputExcel']['name'];
		$tmp_name = $_FILES['inputExcel']['tmp_name'];
		
		//判断上传文件的后缀名
		$extstring = substr($filename, strrpos($filename, ".")+1, strlen($filename)-strrpos($filename, "."));
		
		if($extstring === "xls")
		{
		   $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format 
		}
		elseif($extstring === "xlsx")
		{
		   $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format 
		}
		else
		{
			echo "导入失败，格式有误";
			exit;
		}
		
		$objPHPExcel = $objReader->load($tmp_name); //$filename可以是上传的文件，或者是指定的文件
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
		$highestColumn = $sheet->getHighestColumn(); // 取得总列数
		$k = 0; 
		//循环读取excel文件,读取一条,插入一条
		$count = 0;
		for($j=2;$j<=$highestRow;$j++)
		{

			$a = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值
			$b = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();//获取B列的值
			$c = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();//获取C列的值
			$d = $objPHPExcel->getActiveSheet()->getCell("D".$j)->getValue();//获取D列的值
			//echo "这是生日".$d ;
			$e = $objPHPExcel->getActiveSheet()->getCell("E".$j)->getValue();//获取E列的值
			if($c === '男')
			   $c = 0;
			else
			   $c = 1;
			$vericode = $this -> gernate_vericode(); 
			//判断验证码是否重复 
			$countvericodes = pdo_fetchall("SELECT count(*) as countcode FROM wp_school_student WHERE GWEID=".$gweid." and stu_vericode = '".$vericode."'");
			foreach($countvericodes as $countvericode)
			{
			    $vericodenumber = $countvericode['countcode'];
			}
			while($vericodenumber != 0)
			{
			    $vericode = $this -> gernate_vericode();  
				$countvericodes = pdo_fetchall("SELECT count(*) as countcode FROM wp_school_student WHERE GWEID=".$gweid." and stu_vericode = '".$vericode."'");
				foreach($countvericodes as $countvericode)
				{
					$vericodenumber = $countvericode['countcode'];
				}
			}
			//判断学号是否重复
		   $numbercount = pdo_fetchall("SELECT COUNT(*) counts  FROM wp_school_student w1 WHERE w1.GWEID=".$gweid." and w1.stu_number = ".$a);
				foreach($numbercount as $countsnumber)
			{
				$stunumber = $countsnumber['counts'];
			}
			if ($stunumber==0&&$vericodenumber==0)
			$insertresult = pdo_insert('school_student',array('stu_number' => $a, 'stu_name' => $b, 'stu_sex' => $c,'stu_birth' => $d,'stu_gradeclass' => $e,'stu_vericode'  =>$vericode, 'GWEID' => $gweid));
			if(!empty($insertresult))
				$count++;
			
		}
		if($count)
			echo "导入成功，导入{$count}条学生信息";
		else
			echo "导入失败，学生学号或验证码有重复";
        
    }
	
	//添加学生页面
	public function doWebAddstudent(){
	
	    global $_GPC;
		//获取session中的gweid
	    $gweid=$_SESSION['GWEID'];
		//拿到所有的年级
		//$all_sgc = pdo_fetchall("SELECT  distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid." ORDER BY stu_gradeclass");
		
		$all_sgc = pdo_fetchall("(SELECT distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid.") UNION DISTINCT (SELECT distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid.") ORDER BY stu_gradeclass");
		
		
		if( isset($_POST['student_number']) ){

		$student_number = $_GPC['student_number'];
		$student_name = $_GPC['student_name'];
		$student_sex = $_GPC['student_sex'];
		$student_birth = $_GPC['student_birth'];
		$student_gradeclass=$_GPC['stu_gradeclass'];
		$in=$_GPC['in'];
		if($in!=null){
			$student_gradeclass=$in;
		}
		
		$student_vericode=$_GPC['student_vericode']; 
		//判断学号是否重复
		$numbercount = pdo_fetchall("SELECT COUNT(*) counts  FROM wp_school_student w1 WHERE  w1.stu_number = ".$student_number." and w1.GWEID=".$gweid);
		foreach($numbercount as $countsnumber)
		{
			$stunumber = $countsnumber['counts'];
		}
		//判断验证码是否重复
		$countvericodes = pdo_fetchall("SELECT count(*) as countcode FROM wp_school_student WHERE GWEID=".$gweid." and stu_vericode = '".$student_vericode."'");
		foreach($countvericodes as $countvericode)
		{
			 $vericodenumber = $countvericode['countcode'];
		}
		if ($stunumber==0&&$vericodenumber==0){
		$stu_id =  pdo_insert('school_student',array('stu_number' => $student_number, 'stu_name' => $student_name,'stu_sex' => $student_sex,'stu_birth' => $student_birth, 'stu_gradeclass' => $student_gradeclass, 'stu_vericode'  =>$student_vericode,'GWEID' => $gweid));
		}
	} 
        include $this -> template('addstudent');
    }
	
	//编辑学生页面
	public function doWebEditstudent(){
	
	    global $_GPC;
		//获取session中的gweid
	    $gweid = $_SESSION['GWEID'];
		$sid = $_GPC['id'];
		//拿到所有的年级
		$all_sgc = pdo_fetchall("SELECT  distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid." ORDER BY stu_gradeclass");
		if( isset($_GPC['student_number']) &&!empty($_GPC['student_number'])){
		   $student_number = $_GPC['student_number'];
		   $student_name = $_GPC['student_name'];
		   $student_sex = $_GPC['student_sex'];
		   $student_birth = $_GPC['student_birth'];
		    $in=$_GPC['in'];
		   	if($in!=null){
				$student_gradeclass=$in;
			}
			else{ 
				$student_gradeclass = $_GPC['student_gradeclass'];
			} 
		   //$student_gradeclass = $_GPC['student_gradeclass'];
		   $student_vericode = $_GPC['student_vericode'];
		   //echo "zhi".$teacher_gradeclass;
		   //判断学号是否重复
		   $numbercount = pdo_fetchall("SELECT COUNT(*) counts  FROM wp_school_student WHERE GWEID=".$gweid." and stu_number = ".$student_number." and stu_id!=".$sid);
			foreach($numbercount as $countsnumber)
		{
			$stunumber = $countsnumber['counts'];
		}
			$countvericodes = pdo_fetchall("SELECT count(*) as countcode FROM wp_school_student WHERE  GWEID=".$gweid." and stu_vericode = '".$student_vericode."' and stu_id!=".$sid);
			foreach($countvericodes as $countvericode)
		{
			 $vericodenumber = $countvericode['countcode'];
		}
		   if ($stunumber==0&&$vericodenumber==0){
		   pdo_update('school_student',array('stu_number'=>$student_number,'stu_name'=>$student_name,'stu_sex'=>$student_sex,'stu_birth'=>$student_birth,'stu_gradeclass'=>$student_gradeclass,'stu_vericode'=>$student_vericode),array('stu_id'=>$sid));	
			
		}
		}

        $students = pdo_fetchall("SELECT * FROM wp_school_student WHERE  GWEID=".$gweid." and stu_id = ".$sid);
		foreach($students as $student )
		{	$studentnumber = $student['stu_number'];
			$studentname = $student['stu_name'];
			$studentsex = $student['stu_sex'];
			$studentbirth = $student['stu_birth'];
			$studentgradeclass = $student['stu_gradeclass'];
			$studentvericode = $student['stu_vericode'];
			//$studentfromuser = $student['student_fromuser'];
		}

        include $this -> template('editstudent');
    }
	
	//查询学生页面
	public function doWebSearchstudent(){
	    global $_GPC;
	    $range = $_GPC['range'];	
	    $indata = $_GPC['indata'];
        include $this -> template('searchstudent');
    }
	//有关视频
	//ma获取根据查询条件得到的video数据集
	public function doWebCountVideoSearchPage($indata,$rg,$offset,$pagesize,$gweid)
	{	
		//$myrows = pdo_fetchall("SELECT * FROM wp_school_video w1, wp_school_teacher w2 WHERE w1.video_publisher = w2.tea_id AND ".($rg=='video_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata."%' AND w1.GWEID = ".$gweid." ORDER BY w1.video_id DESC limit ".$offset.",".$pagesize);
		$myrows = pdo_fetchall("SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey  LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE ".($rg=='video_publisher'?'w2.tea_name':'w1.'.$rg)." like '%".$indata."%' AND w1.GWEID = ".$gweid." ORDER BY w1.video_id DESC limit ".$offset.",".$pagesize);
		
		return $myrows;
	}
	//ma获取所有的video的个数
	public function doWebCountVideo($gweid)
	{
		global $wpdb;
        $myrows = pdo_fetchall( "SELECT COUNT(*) as videoCount FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey  LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w1.GWEID = ".$gweid);		
		return $myrows;
	}
	//获取video当页的所有数据集
	public function doWebCountVideoPage($offset,$pagesize,$gweid)
	{
		$myrows = pdo_fetchall( "SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey  LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w1.GWEID = ".$gweid." ORDER BY w1.video_id DESC limit ".$offset.",".$pagesize );	
		return $myrows;
	}
	//获取video当页的所有数据集
	public function doMobileCountVideoPage($offset,$pagesize,$gweid)
	{
		$myrows = pdo_fetchall( "SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey  LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w1.GWEID = ".$gweid." ORDER BY w1.video_id DESC limit ".$offset.",".$pagesize );	
		return $myrows;
	}
	//获取video当页的所有数据集
	public function doWebCountVideoStuPage($offset,$pagesize,$gweid,$grade)
	{
		$myrows = pdo_fetchall( "SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey  LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w1.GWEID = ".$gweid."  and (w1.video_gradeclass = '*' OR w1.video_gradeclass like '%".$grade."%') ORDER BY w1.video_id DESC limit ".$offset.",".$pagesize );	
		return $myrows;
	}
	
	
	//视频管理页面
	public function doWebVideomanage(){
	
	 global $_GPC;
	    //obtain userId
		global $current_user;
		//判断是否是分组管理员中的用户
		$groupadminflag = $this->site_issuperadmin($current_user->ID);
		$userId =  ((is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) || ($groupadminflag == 1 && !empty($_SESSION['GWEID_matched_userid']))) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;					
		//$userId= (is_super_admin( $current_user->ID ) && !empty($_SESSION['GWEID_matched_userid'])) ? $_SESSION['GWEID_matched_userid'] : $current_user->ID;
		$gweid=$_SESSION['GWEID'];

	    $flag=$_GPC['flag'];
	    $f=$_GPC["f"];
	    if($f==null){$f=$flag;}
	    $indata=$_GPC['Ipad'];
	    $in=$_GPC["in"];
	    if($in==null){$in=$indata;}
	    $indata=$in;
		$rg=$_GPC['range'];
		$r=$_GPC["r"];
		if($r==null){$r=$rg;}
		$rg=$r;
		
		//删除视频
		if(isset($_GPC['del']) && !empty($_GPC['del']) ){
		
			pdo_delete('school_video',array('video_id' => $_GPC['del']));
		}
		if($rg=='video_publisher')
		{
			$vidCount = pdo_fetchall( "SELECT COUNT(*) as videoCount FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w2.tea_name like '%".$indata."%' AND w1.GWEID = ".$gweid);
		}
		else 
		{ 
		   $vidCount = pdo_fetchall( "SELECT COUNT(*) as videoCount FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w1.".$rg." like '%".$indata."%' AND w1.GWEID = ".$gweid);
		}
		$vsCount = $this -> doWebCountVideo($gweid);
		
        include $this -> template('videomanage');
    }	
	//编辑视频页面
	public function doWebEditvideo(){
	
	    global $_GPC;
		//获取session中的gweid
	    $gweid = $_SESSION['GWEID'];
		$vid = $_GPC['id'];
		
		
		if( isset($_GPC['video_title']) &&!empty($_GPC['video_title'])){
		
		   $video_title = $_GPC['video_title'];
		   $video_desp = $_GPC['video_desp'];
		   $video_time = $_GPC['video_time'];
		   $video_gradeclass = $_GPC['video_gradeclass'];
		   echo "获取当前传递过来的年级或班级:".$video_gradeclass;
		   //$video_publisher = $_GPC['video_publisher']; 
		pdo_update('school_video',array('video_title'=>$video_title,'video_desp'=>$video_desp, 'video_time'=>$video_time,'video_gradeclass'=>$video_gradeclass),array('video_id'=>$vid));	
		}

        //$videos = pdo_fetchall("SELECT * FROM wp_school_video w1, wp_school_teacher w2 WHERE w1.video_publisher = w2.tea_id AND w1.video_id = ".$vid);
		$videos = pdo_fetchall( "SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey  LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w1.video_id = ".$vid);
		$upload =wp_upload_dir();
		foreach($videos as $video )
		{
			$videotitle = $video['video_title'];
			$videodesp = $video['video_desp'];
			if($video['type'] == 'pic')
			{
				if((empty($video['video_url']))||(stristr($video['video_url'],"http")!==false)){
					$videourl=$video['video_url'];
				}else{
					$videourl=$upload['baseurl'].$video['video_url'];
				}
				
			}
			else
			{
			    $videourl = $video['url'];
			}
			$videotime = $video['video_time'];
			$videogradeclass = $video['video_gradeclass'];
			$videopublisher = $video['tea_name']; 
		}
		
		//找到所有的老师对应的gradeclass
		$all_gc = pdo_fetchall("SELECT  distinct(tea_gradeclass) FROM wp_school_teacher ORDER BY tea_gradeclass");
        //找到所有的年级
		$all_g = pdo_fetchall("SELECT  distinct(SUBSTR(`tea_gradeclass`,1,4)) as allgrade FROM wp_school_teacher ORDER BY tea_gradeclass");
		
        include $this -> template('editvideo');
    }
	
	//查询视频页面
	public function doWebSearchvideo(){
	    global $_GPC;
	    $range = $_GPC['range'];	
	    $indata = $_GPC['indata']; 
        include $this -> template('searchvideo');
    }
	
	
	/**************
	***************  判断老师、家长登陆验证码，进行身份验证
	**************/
	public function doMobileVerifyuser(){
		global $_GPC;
		$uAgent = $_SERVER['HTTP_USER_AGENT']; 
		$osPat = "android|UCWEB|iPhone|iPad|BlackBerry|Symbian|Windows Phone|hpwOS"; 
		if(preg_match("/($osPat)/i", $uAgent )) { 
			//echo "来者手机终端"; 
			$regtype = 'Mobile';
		} else { 
			//echo "来者pc端"; 
			$regtype = 'Web';
		}
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
	
		$tcount = 0;
		$scount = 0;
		if(!empty($fromuser)){
			//一进到该页面时判断需不需要再次输入老师或家长验证码		
			$number=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
		
			$numberstu=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			
		}else{
			//2014-07-10新增修改，如果是老师就从教师表中查找
			if($_SESSION['user_type'] == "teacher")
			{
				$number=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($number as $count)
				{
					$tcount=$count['number'];
				}
			}
			else
			{
				$numberstu=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($numberstu as $count)
				{
					$scount=$count['number'];
				}
			}
			
		}
		
		if(($tcount != 0)||($scount != 0)){
			$flag1 = true;
		}
			
		//老师或家长输入验证码
		if( isset($_GPC['user_vercode']) ){    
			$user_vercode = $_GPC['user_vercode'];
			//家长或者教师只有一个人正在使用
			
			//2014-07-09新增修改，获取页面上输入的登录身份
			$usertype = $_GPC['user_type'];
			//2014-07-10新增修改，将用户身份也放到session中
			$_SESSION['user_type']=$usertype;
			
			$coertea = 0;
			$coerstu = 0;
			if($usertype == "teacher")
			{			
				$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$user_vercode."'");
					
				foreach($countnumber as $count){
					$coertea=$count['number'];
				}
			}			
			if($usertype == "parent")
			{	
				$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$user_vercode."'");
					
				foreach($countnumber as $count){
					$coerstu=$count['number'];
				}
			}
			if(($usertype == "teacher" && $coertea == 0)&&($usertype == "parent" && $coerstu == 0)){
			?>
				<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
				<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<script>
						alert("您输入的验证码有误");
					</script>
				</head>
				</html>
		
			<?php
				}else{//janeen:必须保证家长和老师的验证码不能一样啊，否则判断出错
				 //2014-07-09新增修改
				    if($usertype == "teacher" && $coertea != 0)
					{	
						if(!empty($fromuser)){
							pdo_update('school_teacher',array('tea_fromuser'=>$fromuser),array('tea_vericode'=>$user_vercode,'GWEID'=>$gweid));	
						}
						$_SESSION['user_vercode']=$user_vercode;
					}
					if($usertype == "parent" && $coerstu != 0)
					{	
						if(!empty($fromuser)){
							pdo_update('school_student',array('stu_fromuser'=>$fromuser),array('stu_vericode'=>$user_vercode,'GWEID'=>$gweid));
						}	
						$_SESSION['user_vercode']=$user_vercode;
					}
					$flag1 = true;	
				}
		}
		include $this -> template('userverify');
		
	}
	
	//教师手机上传视频页面
	public function doMobileVideoupload(){
	
	    global $wpdb, $_GPC;
		//获取session中的gweid
	    $gweid=$_SESSION['GWEID'];
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			
			//获取上传的url
			require_once MODULES_DIR.$this -> module['name'].'/upload/qiniu/rs.php';
			$bucket = "wevideo";
			$key = "pic.jpg";
			$accessKey = 'BnEuL9EBya39evSshr9Z5uUZYdWaElRZlDuC1c7b';
			$secretKey = 'kQntsPFbLqaQLDEN_dOBm3c8VUiyrVIylkNBq__b';

			Qiniu_SetKeys($accessKey, $secretKey);

			$putPolicy = new Qiniu_RS_PutPolicy($bucket);
			$putPolicy -> Expires = 3600*24;
			$putPolicy -> PersistentOps = 'avthumb/mp4';
			//$putPolicy -> PersistentNotifyUrl='http://2.wpcloudforsina.sinaapp.com/mobile.php?module=weSchool&do=TranscodingCallback&GWEID=2356800&fromuser=qiniucallback';
			$putPolicy -> PersistentNotifyUrl = home_url().'/mobile.php?module=weSchool&do=TranscodingCallback&GWEID=2356800&fromuser=qiniucallback';
			$upToken = $putPolicy->Token(null);

			//先找到教师对应的年级
			if(!empty($fromuser)){
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_fromuser='".$fromuser."' AND GWEID =".$gweid );	
			}else{
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_vericode='".$_SESSION['user_vercode']."' AND GWEID =".$gweid );	
			}
			foreach($teacher_detail as $teacher){
				$teacher_gc=$teacher['tea_gradeclass'];
			}
			
			//找到所有的老师对应的gradeclass
			$all_gc = pdo_fetchall("(SELECT  distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid.") ORDER BY tea_gradeclass");
			
			//找到所有的年级
			$all_g = pdo_fetchall("(SELECT  distinct(SUBSTR(`tea_gradeclass`,1,4)) as allgrade FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(SUBSTR(`stu_gradeclass`,1,4)) FROM wp_school_student where GWEID=".$gweid.") ORDER BY allgrade");
			
			if( isset($_GPC['video_title']) ){
				$video_title=$_GPC['video_title'];
				$video_desp = $_GPC['video_desp'];
				$video_gradeclass = $_GPC['video_gradeclass'];
				$file=$_GPC['file'];
				
				if(!empty($fromuser)){
					$teaid = pdo_fetchall("SELECT * FROM wp_school_teacher where tea_fromuser='".$fromuser."' AND GWEID =".$gweid);
				}else{
					$teaid = pdo_fetchall("SELECT * FROM wp_school_teacher where tea_vericode='".$_SESSION['user_vercode']."' AND GWEID =".$gweid);			
				}
				foreach($teaid as $teacher){
					$tea_id=$teacher['tea_id'];
				}
				$date=date("Y-m-d h:m:s");
				
				$Status =pdo_insert('school_video',array('video_title' => $video_title, 'video_desp' => $video_desp, 'video_url' => '', 'video_time'  =>$date, 'video_publisher'  =>$tea_id,'GWEID' => $gweid));			
				
				if($Status!==false) {
					$flag = true;
					$countmember = false;
				}else{
				?>
				   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<script>
							alert("上传失败");
						</script>
					</head>
					</html>	
				<?php
				}			

				
			}
		
			include $this -> template('videoupload');
		}else{
			$this->doMobileVerifyuser();			
		}
		
	} 
	
	//测试手机使用flash上传视频页面
	public function doMobileSwfvideoupload(){
	    global $wpdb, $_GPC;
		//获取session中的gweid
	    $gweid=$_SESSION['GWEID'];
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			//获取上传的url
			require_once MODULES_DIR.$this -> module['name'].'/upload/qiniu/rs.php';
			$bucket = "wevideo";
			$key = "pic.jpg";
			$accessKey = 'BnEuL9EBya39evSshr9Z5uUZYdWaElRZlDuC1c7b';
			$secretKey = 'kQntsPFbLqaQLDEN_dOBm3c8VUiyrVIylkNBq__b';

			Qiniu_SetKeys($accessKey, $secretKey);

			$putPolicy = new Qiniu_RS_PutPolicy($bucket);
			$putPolicy -> Expires = 3600*24;
			$putPolicy -> PersistentOps = 'avthumb/mp4';
			//$putPolicy -> PersistentNotifyUrl='http://2.wpcloudforsina.sinaapp.com/mobile.php?module=weSchool&do=TranscodingCallback&GWEID=2356800&fromuser=qiniucallback';
			$putPolicy -> PersistentNotifyUrl=home_url().'/mobile.php?module=weSchool&do=TranscodingCallback&GWEID=2356800&fromuser=qiniucallback';
			$upToken = $putPolicy->Token(null);
			
			
			//先找到教师对应的年级
			if(!empty($fromuser)){
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_fromuser='".$fromuser."' AND GWEID =".$gweid );
			}else{
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_vericode='".$_SESSION['user_vercode']."' AND GWEID =".$gweid );
			}
				
			foreach($teacher_detail as $teacher){
				$teacher_gc=$teacher['tea_gradeclass'];
			}
			
			//找到所有的老师对应的gradeclass
			$all_gc = pdo_fetchall("(SELECT  distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid.") ORDER BY tea_gradeclass");
			
			//找到所有的年级
			$all_g = pdo_fetchall("(SELECT  distinct(SUBSTR(`tea_gradeclass`,1,4)) as allgrade FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(SUBSTR(`stu_gradeclass`,1,4)) FROM wp_school_student where GWEID=".$gweid.") ORDER BY allgrade");
			
			
			include $this -> template('swfvideoupload');
		
		}else{
			$this->doMobileVerifyuser();			
		}
		
	}
	
	//教师手机上传图片页面
	public function doMobilePictureupload(){
	
	    global $wpdb, $_GPC;
		//获取session中的gweid
	    $gweid=$_SESSION['GWEID'];
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		
		/*2014-12-07添加身份验证*/
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
		    //2014-07-10新增修改，根据登录时的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			//先找到教师对应的年级		
			if(!empty($fromuser)){
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_fromuser='".$fromuser."' AND GWEID =".$gweid );	
			}else{
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_vericode='".$_SESSION['user_vercode']."' AND GWEID =".$gweid );
			}		
			foreach($teacher_detail as $teacher){
				$teacher_gc=$teacher['tea_gradeclass'];
			}
			
			//找到所有的老师对应的gradeclass
			$all_gc = pdo_fetchall("(SELECT  distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(stu_gradeclass) FROM wp_school_student where GWEID=".$gweid.") ORDER BY tea_gradeclass");
			
			//找到所有的年级
			$all_g = pdo_fetchall("(SELECT  distinct(SUBSTR(`tea_gradeclass`,1,4)) as allgrade FROM wp_school_teacher where GWEID=".$gweid." ) UNION DISTINCT (SELECT distinct(SUBSTR(`stu_gradeclass`,1,4)) FROM wp_school_student where GWEID=".$gweid.") ORDER BY allgrade");
		
			include $this -> template('pictureupload');
		}else{
			$this->doMobileVerifyuser();			
		}
		
	} 
	
	public function doMobilePictureCreate(){
		global $_GPC;
		include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		
		$gweid=$_SESSION['GWEID'];
		
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
	    $fromuser = $_SESSION['fromuser'];
		
		//有些手机中的图片没有扩展名导致上传不成功，这里加上jpg类型
		$type =strtolower(strstr($_FILES['file']['name'], '.'));
		if($type == false)
		{
		    $_FILES['file']['name'] = $_FILES['file']['name'].".jpg";
			$type = ".jpg";
		}
		$picname = $_FILES['file']['name'];
		$picsize = $_FILES['file']['size'];
		
		if ($picname != "") {
			//if ($picsize > 1024000) {
			if ($picsize > 10240000) {
			    $status = array(
				    'name'=>'',
					'info'=>'图片大小不能超过10M!'
					
				);
				//echo json_encode($status);
				//echo '图片大小不能超过1M';
				?>
				   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<script>
							alert("图片大小不能超过10M!");
							//location.href = '<?php echo $this->createMobileUrl('pictureupload',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>';
							location.href = '<?php echo $this->createMobileUrl('pictureupload',array('gweid' => $gweid));?>';
						</script>
					</head>
					</html>	
				<?php
				exit;
			}
			//$type =strtolower(strstr($picname, '.'));
			
			if ($type != ".gif" && $type != ".jpg"&& $type != ".png" && $type != ".jpeg") {
			    $status = array(
				    'name'=>'',
					'info'=>'图片格式不对!'	
				);
				?>
				   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<script>
							alert("图片格式不对!");
							//location.href = '<?php echo $this->createMobileUrl('pictureupload',array('GWEID' => $gweid, 'fromuser' =>$fromuser));?>';
							location.href = '<?php echo $this->createMobileUrl('pictureupload',array('gweid' => $gweid));?>';
						</script>
					</head>
					</html>	
				<?php
				
				exit;
			} 
			
			$up=new upphoto();	
			$picUrl=$up->save();
			//$path=substr( $picUrl,strripos($picUrl,'uploads/')+8 );
			$path=substr( $picUrl,1);
		}
		$size = round($picsize/1024,2);
		$upload =wp_upload_dir();
		if((empty($picUrl))||(stristr($picUrl,"http")!==false)){
			$echopicurl=$picUrl;
		}else{
			$echopicurl=$upload['baseurl'].$picUrl;
		}
		$arr = array(
			'name'=>$picname,
			'pic'=>$echopicurl,
			'size'=>$size
		);
		
		
		
		if( isset($_GPC['picture_title']) ){
			//先找到教师对应的tea_id
			if(!empty($fromuser)){
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_fromuser='".$fromuser."' AND GWEID =".$gweid);	
			}else{
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_vericode='".$_SESSION['user_vercode']."' AND GWEID =".$gweid);	
			}
			foreach($teacher_detail as $teacher){
				$tea_id=$teacher['tea_id'];
			}
			$picture_title = $_GPC['picture_title'];
			$picture_desp = $_GPC['picture_desp'];
			$picture_gradeclass = $_GPC['picture_gradeclass'];
			date_default_timezone_set('PRC');
			$date=date("Y-m-d H:i:s");
				
			pdo_insert('school_video',array('type'=>'pic','video_title' => $picture_title, 'video_desp' => $picture_desp, 'video_url'=>$picUrl,'video_time'  =>$date, 'video_gradeclass'  =>$picture_gradeclass, 'video_publisher'  =>$tea_id,'GWEID' => $gweid));
			
			//header("Location: ".$this->createMobileUrl('videolist',array('GWEID' => $gweid, 'fromuser' =>$fromuser)));
			header("Location: ".$this->createMobileUrl('videolist',array('gweid' => $gweid, 'fromuser' =>$fromuser)));
			
		}
		
	}
	
	//测试mobile显示页面
	public function doMobileVideolist(){
	    global $_GPC;

	    //2014-07-11新增修改,fromuser能取到就取到，取不到就使用验证码
		$fromuser = $_SESSION['fromuser'];
		$gweid =  $_GPC['gweid'];
			
		//取到user_id//error
		$userid=pdo_fetchall("SELECT * FROM wp_wechat_usechat where GWEID=".$gweid);
		
		foreach($userid as $uid){
			$user_id=$uid['user_id'];
		}
		//如果fromuser为空，该用户应该是从pc端登陆的；如果从手机端访问，可以在session中获取fromuser
		//2014-07-10新增修改
		$counts = 0;
		$countstu = 0;
		if(!empty($fromuser) ){
		    //判断该用户是不是教师
			$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
			foreach($countnumber as $count){
				$counts=$count['number'];
			}
			
			//判断该用户是不是家长
			$countnumberstu=pdo_fetchall("SELECT COUNT(*) as numberstu FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countnumberstu as $count){
				$countstu=$count['numberstu'];
			}
			
		}else{
		
			//判断该用户是不是教师
			//2014-07-10新增修改，根据登陆时选择的身份
			if($_SESSION['user_type'] == "teacher")
			{
				$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countnumber as $count){
					$counts=$count['number'];
				}
			}
			else
			{
				//判断该用户是不是家长
				$countnumberstu=pdo_fetchall("SELECT COUNT(*) as numberstu FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countnumberstu as $count){
					$countstu=$count['numberstu'];
				}		
		    }
		}
		
		//如果是老师,则会列出所有的视频列表
		if($counts != 0)
		{
	        
			$videos = pdo_fetchall( "SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey WHERE w1.GWEID = ".$gweid." ORDER BY w1.video_id DESC");
            
			$videosCount = pdo_fetchall("SELECT COUNT(*) as videoCount FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey WHERE GWEID = ".$gweid);
			foreach($videosCount as $count)
		    {
		       $teacount = $count['videoCount'];
		    }
			$counthomenum=get_user_meta($user_id,'school_video_displaycount',true);
			if($teacount>$counthomenum){
				$countnumber=$counthomenum;
			}else{
				$countnumber=$teacount;
			}
			
		}
		//如果是家长,则会列出该家长所在的班级以及所在年级的视频列表
		if($countstu != 0)
        {	
		    //获取当前家长所在班级
			if(!empty($fromuser)){
				$stugcs = pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");	
			}else{
				$stugcs = pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
			}
            foreach($stugcs as $stugc){
				$gc=$stugc['stu_gradeclass'];
			}		
            
            //家长可以查看该班级+班级所在年级,模糊匹配 
		   $grade=substr($gc,0,4);
		   
		   $grades=$grade.'*';
		   
			$videostu = pdo_fetchall( "SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey  LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w1.GWEID = ".$gweid." and (w1.video_gradeclass = '*' OR w1.video_gradeclass like '%".$grade."%') ORDER BY w1.video_id DESC");			
		   
		   $videostuCount = pdo_fetchall( "SELECT COUNT(*) as videoCount FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w3 ON w1.pkey = w3.pkey  LEFT JOIN wp_school_teacher w2 on w1.video_publisher = w2.tea_id WHERE w1.GWEID = ".$gweid." and (w1.video_gradeclass = '*' OR w1.video_gradeclass like '%".$grade."%')");
		   foreach($videostuCount as $count)
		   {
		       $stucount = $count['videoCount'];
		   }
		   $counthomenum=get_user_meta($user_id,'school_video_displaycount',true);
			if($stucount>$counthomenum){
				$countnumber=$counthomenum;
			}else{
				$countnumber=$stucount;
			}
		}
		
		
		include $this -> template('videolist');
	}
	
	public function doMobileVideoUploadUrl(){
		global $_GPC;
		require_once MODULES_DIR.$this -> module['name'].'/videoUploadUrl.php';
		return $url;
	}
	
	public function doMobileVideoCreate(){
		global $_GPC;
		header('Content-Type: application/json; charset=utf-8');
		
		$gweid=$_SESSION['GWEID'];
        //2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		if( isset($_GPC['video_title']) ){
		    
			
			//先找到教师对应的tea_id
			if(!empty($fromuser)){
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_fromuser='".$fromuser."' AND GWEID =".$gweid);	
			}else{
				$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where tea_vericode='".$_SESSION['user_vercode']."' AND GWEID =".$gweid);
			}
			
			foreach($teacher_detail as $teacher){
				$tea_id=$teacher['tea_id'];
			}
			$video_title = $_GPC['video_title'];
			$video_desp = $_GPC['video_desp'];
			$video_gradeclass = $_GPC['video_gradeclass'];
			date_default_timezone_set('PRC');
			$date=date("Y-m-d H:i:s");
			
			$video_key = $_GPC['video_key'];
			$video_pkey = $_GPC['video_pkey'];
				
			pdo_insert('school_video',array('video_title' => $video_title, 'key' =>$video_key, 'pkey' => $video_pkey,'video_desp' => $video_desp, 'video_time'  =>$date, 'video_gradeclass'  =>$video_gradeclass, 'video_publisher'  =>$tea_id,'GWEID' => $gweid));
			
		}
		
	}
	
	public function doMobileVideoplay(){
		global $_GPC;
		$gweid = $_GPC['gweid'];
		
		/*2014-12-07添加身份验证*/
		$fromuser = $_SESSION['fromuser'];
		
		$teacount = 0;
		$stucount = 0;
		if(!empty($fromuser)){
			$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");	
			foreach($countteas as $counttea){
				$teacount=$counttea['number'];
			}
		
			$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countstus as $countstu){
				$stucount=$countstu['number'];
			}
		
		}else{
			if($_SESSION['user_type'] == "teacher")
			{
				$countteas=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
				foreach($countteas as $counttea){
					$teacount=$counttea['number'];
				}
		    }
			else
			{
				$countstus=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
				foreach($countstus as $countstu){
					$stucount=$countstu['number'];
				}
			}
		}	
		
		if($teacount || $stucount){	
			$id = $_GPC['id'];
		
			//$video_detail=pdo_fetchall("SELECT * FROM wp_school_video w1, wp_school_videotranscode w2 where ( w1.pkey = w2.pkey OR w1.type = 'pic') AND video_id=".$id );		
			$video_detail=pdo_fetchall("SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w2 ON w1.pkey = w2.pkey WHERE w1.video_id=".$id );
			//echo "SELECT * FROM wp_school_video w1 LEFT JOIN wp_school_videotranscode w2 ON w1.pkey = w2.pkey WHERE w1.video_id=".$id;
			$upload =wp_upload_dir();
			foreach($video_detail as $video){
			   
			   $type = $video['type'];   //判断要浏览的是video还是pic	
				if($type == "pic") { 
				if((empty($video['video_url']))||(stristr($video['video_url'],"http")!==false)){
					$vurl=$video['video_url'];
				}else{
					$vurl=$upload['baseurl'].$video['video_url'];
				}
				
				
				$video_url = "<img width='100%' src='".$vurl."'/>";
				  //$video_url = $video['video_url'];
				}
				else
				{
				  $videourl = $video['url'];
				  //echo $videourl;
				}
			}
			
			include $this -> template('videoplay');
		}else{
			$this->doMobileVerifyuser();			
		}
		
	}
	
	
	
	//测试手机上传视频页面
	public function doWebVideoupload(){
	    global $wpdb, $_GPC;
		//获取session中的gweid
	    $gweid=$_SESSION['GWEID'];
		if(!isset($_GPC['gweid'])||!isset($_GPC['fromuser'])){
			//$gweid = $_SESSION['WECID'];
			$gweid=$_SESSION['GWEID'];
			$fromuser = $_SESSION['fromuser'];
		}else{
			$gweid =  $_GPC['gweid'];
			$fromuser = $_GPC['fromuser'];
		}	
		
		//先找到教师对应的年级
		if(!empty($fromuser)){
			$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser=".$fromuser );
		}else{
			$teacher_detail=pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode=".$_SESSION['user_vercode']);
		}
			
		
		foreach($teacher_detail as $teacher){
			$teacher_gc=$teacher['tea_gradeclass'];
		}
		//找到所有的老师对应的gradeclass
		$all_gc = pdo_fetchall("SELECT  distinct(tea_gradeclass) FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
		//找到所有的年级
		$all_g = pdo_fetchall("SELECT  distinct(SUBSTR(`tea_gradeclass`,1,4)) as allgrade FROM wp_school_teacher where GWEID=".$gweid." ORDER BY tea_gradeclass");
	    
		if( isset($_GPC['video_title']) ){
			$video_title=$_GPC['video_title'];
			$video_desp = $_GPC['video_desp'];
			$video_gradeclass = $_GPC['video_gradeclass'];
			$file=$_GPC['file'];
			
			if(!empty($fromuser)){
				$teaid = pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser=".$fromuser);
			}else{
				$teaid = pdo_fetchall("SELECT * FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode=".$_SESSION['user_vercode']);
			}
			
			foreach($teaid as $teacher){
				$tea_id=$teacher['tea_id'];
			}
			date_default_timezone_set('PRC');
			$date=date("Y-m-d H:i:s");
			
			$Status =pdo_insert('school_video',array('video_title' => $video_title, 'video_desp' => $video_desp, 'video_url' => '', 'video_time'  =>$date, 'video_publisher'  =>$tea_id,'GWEID' => $gweid));			
			
			if($Status!==false) {
				$flag = true;
				$countmember = false;
			}else{
			?>
			   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<script>
						alert("上传失败");
					</script>
				</head>
				</html>	
			<?php
			}			
		}
		
		include $this -> template('testupload');
	}
	public function doMobileTranscodingCallback(){
		global $HTTP_RAW_POST_DATA,$wpdb;
		sleep(1);
		//debug info
		$kv = new SaeKV();
		$ret = $kv->init();
		$kv->add(date('QNCallback Y-m-d H:i:s'), $HTTP_RAW_POST_DATA);
		//debug info end
		$data = json_decode($HTTP_RAW_POST_DATA,true);
		if(!empty($data) && ($data['code']==0||$data['code']==4)){
			$pkey = $data['id'];
			foreach($data['items'] as $item)
				if($item['code']==0){
					$url = 'http://wevideo.qiniudn.com/'.$item['key'];
					//$updateStatus = $wpdb -> update($wpdb -> prefix.'school_video',array('video_url' => $url,'transcode_flag' => 1),array('pkey' => $pkey));
					pdo_insert('school_videotranscode',array('pkey' => $pkey, 'url' => $url));		
					$kv->add(date('QNCallbackSql Y-m-d H:i:s'), $wpdb -> last_query.' updateStatus:'.$updateStatus.'  error:'.$wpdb -> last_error );
				}
		}
	}
	
	//测试mobile显示页面
	public function doMobileListest(){
	    global $_GPC;
		//2014-07-11新增修改
		$gweid =  $_GPC['gweid'];
		$fromuser = $_SESSION['fromuser'];
		
		//取到user_id
		$userid=pdo_fetchall("SELECT * FROM wp_wechat_usechat where GWEID=".$gweid);
		foreach($userid as $uid){
			$user_id=$uid['user_id'];
		}
		//如果fromuser为空，该用户应该是从pc端登陆的；如果从手机端访问，可以在session中获取fromuser
		if(!empty($fromuser) ){
		    //判断该用户是不是教师
			$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_fromuser='".$fromuser."'");
			foreach($countnumber as $count){
				$counts=$count['number'];
			}
			
			//判断该用户是不是家长
			$countnumberstu=pdo_fetchall("SELECT COUNT(*) as numberstu FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			foreach($countnumberstu as $count){
				$countstu=$count['numberstu'];
			}
			
		}else{
		
			$countnumber=pdo_fetchall("SELECT COUNT(*) as number FROM wp_school_teacher where GWEID=".$gweid." and tea_vericode='".$_SESSION['user_vercode']."'");
			foreach($countnumber as $count){
				$counts=$count['number'];
			}
			
			//判断该用户是不是家长
			$countnumberstu=pdo_fetchall("SELECT COUNT(*) as numberstu FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
			foreach($countnumberstu as $count){
				$countstu=$count['numberstu'];
			}
		
		}
		
		//如果是老师,则会列出所有的视频列表
		if($counts != 0)
		{
	        $videos = pdo_fetchall("SELECT * FROM wp_school_video w1 WHERE w1.GWEID = ".$gweid." ORDER BY w1.video_id DESC");
			$videosCount = pdo_fetchall("SELECT COUNT(*) as videoCount FROM wp_school_video WHERE GWEID = ".$gweid);
			$counthomenum=get_user_meta($user_id,'school_video_displaycount',true);
			if($videosCount>$counthomenum){
				$countnumber=$counthomenum;
			}else{
				$countnumber=$videosCount;
			}
			
		}
		//如果是家长,则会列出该家长所在的班级以及所在年级的视频列表
		if($countstu != 0)
        {	
		    //获取当前家长所在班级
			if(!empty($fromuser)){
				$stugcs = pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_fromuser='".$fromuser."'");
			}else{
				$stugcs = pdo_fetchall("SELECT * FROM wp_school_student where GWEID=".$gweid." and stu_vericode='".$_SESSION['user_vercode']."'");
			}
            foreach($stugcs as $stugc){
				$gc=$stugc['stu_gradeclass'];
			}		
            
            //家长可以查看该班级+班级所在年级,模糊匹配 
		   $grade=substr($gc,0,4);
		   
		   $grades=$grade.'*';
		   $videostu=pdo_fetchall("SELECT * FROM wp_school_video w1, wp_school_teacher w2 WHERE w1.video_publisher = w2.tea_id AND w1.GWEID=".$gweid." and (w1.video_gradeclass = '*' OR w1.video_gradeclass like '%".$grade."%') ORDER BY w1.video_id DESC");	
		   $videostuCount = pdo_fetchall("SELECT COUNT(*) as videoCount FROM wp_school_video w1, wp_school_teacher w2 WHERE w1.video_publisher = w2.tea_id AND w1.GWEID=".$gweid." and (w1.video_gradeclass = '*' OR w1.video_gradeclass like '%".$grade."%')");	
		   $counthomenum=get_user_meta($user_id,'school_video_displaycount',true);
			if($videostuCount>$counthomenum){
				$countnumber=$counthomenum;
			}else{
				$countnumber=$videostuCount;
			}
		}
		
		include $this -> template('listest');
	}
	
	//create weSchool index page
	public function doWebCreateIndex(){
	    global $_W;
	    $gweid =  $_W['gweid'];
		$pic=pdo_fetchall("SELECT bg_url FROM wp_school where GWEID=".$gweid);
        
		include $this -> template('weschool_page_made');
    } 
	//update weSchool bgimg
	public function doWebPicUpdate(){
	    global $_W,$_GPC;
	    $gweid =  $_W['gweid'];
		
        include $this -> template('weschoolbg_update_dialog');
    }
	public function doWebBgUpdate(){
	    global $_W,$_GPC;
	    $gweid =  $_W['gweid'];
		include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		//上传图片
		if($_W['ispost'])
		if($_FILES["file"]["error"] > 0){
				echo "<h3>保存背景图片失败！</h3>";
		}else{
			$up=new upphoto();
			$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
			$up->get_ph_type($_FILES["file"]["type"]);
			$up->get_ph_size($_FILES["file"]["size"]);
			$up->get_ph_name($_FILES["file"]["name"]);
			$up->get_ph_surl("/bac_image/".$_FILES["file"]["name"]);
			$picUrl=$up->save();
			if($picUrl!=null){
				//$bac_update=web_admin_update_site_bacimg($siteId,$picUrl);
				//将对应gweid的picurl数量取出来，判断是插入还是更新
				//2014-07-08新增修改
				//$pic=pdo_fetchall("SELECT bg_url FROM wp_school where GWEID=".$gweid);
				$picinfos=pdo_fetchall("SELECT count(*) as pcount FROM wp_school where GWEID=".$gweid);
				foreach($picinfos as $picinfo)
				{
				    $pic = $picinfo['pcount'];
				}
				if($pic==0){
					$insertschool = pdo_insert('school',array('bg_url' => $picUrl, 'GWEID' => $gweid));
					/* 测试插入语句是否有问题，输出这条插入语句执行下看看
					global $wpdb;
					echo $wpdb -> last_query; */
				}else{
					$bac_update=pdo_update('school',array('bg_url'=>$picUrl),array('GWEID'=>$gweid));
					
				}
				if($bac_update===false){
					echo "<h3>保存背景图片失败！</h3>";
				}else{
					echo "<h3>保存背景图片成功！</h3>";
				}
			}else{
				echo "<h3>保存背景图片失败！</h3>";
			}
		}
		?>
		
			   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
				<html>
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<script language='javascript'>
						top.resizeTo(300, 200); 		
						setTimeout("self.close()", 2000); 
						opener.location.reload(); 
					</script>
				</head>
				</html>
		
		<?php
		
       // include $this -> template('weschool_page_made');
    } 
	//update weSchool menuimg
	public function doWebMenuUpdate(){
	    global $_W,$_GPC;
	    $gweid =  $_W['gweid'];
		$sid = $_GPC['sid'];
		
		//2014-07-12新增修改，获取链接url
		//sid的值代表的是1：图片和视频，2：作业，3：公告
        
		$menupics=pdo_fetchall("SELECT * FROM wp_school where GWEID=".$gweid);
		$upload =wp_upload_dir();
		foreach($menupics as $menupic)
		{ 
			if($sid == 1)
			{
				$url = $menupic['videopic_url'];
				
			}
			else if($sid == 2)
			{
			    $url = $menupic['homework_url'];
				
			}
			else
			{
				$url = $menupic['notice_url'];
				
			}
			if((empty($url))||(stristr($url,"http")!==false)){
				$weschoolurl=$url;
			}else{
				$weschoolurl=$upload['baseurl'].$url;
			}
			
		}
        include $this -> template('weschoolmenu_update_dialog');
    } 
	public function doWebBackindex(){
		global $_W;
		$gweid =  $_W['gweid'];
		include $this -> template('weschool');
	}
	
	//微学校菜单按钮图片
	public function doWebUpdatepic(){
		global $_W,$_GPC;
	    $gweid =  $_W['gweid'];
		$sid = $_GPC['sid'];
		include 'wp-content/themes/ReeooV3/wesite/common/upload.php';
		
		//上传图片
		if($_FILES["file"]["error"] > 0){
				echo "<h3>保存菜单图片失败！</h3>";
		}else{
			$up=new upphoto();
			$up->get_ph_tmpname($_FILES["file"]["tmp_name"]);
			$up->get_ph_type($_FILES["file"]["type"]);
			$up->get_ph_size($_FILES["file"]["size"]);
			$up->get_ph_name($_FILES["file"]["name"]);
			$up->get_ph_surl("/bac_image/".$_FILES["file"]["name"]);
			$picUrl=$up->save();
			if($picUrl!=null){
				//判断更新的是哪个menu菜单图片
				if($sid==1){
					$pic_count=pdo_fetchall("SELECT videopic_url FROM wp_school where GWEID=".$gweid);
					if($pic_count==null){
						$insertschool = pdo_insert('school',array('videopic_url' => $picUrl,'GWEID' =>$gweid));
					}else{
						$bac_update=pdo_update('school',array('videopic_url'=>$picUrl),array('GWEID'=>$gweid));
					}
				}elseif($sid==2){
					$pic_count=pdo_fetchall("SELECT homework_url FROM wp_school where GWEID=".$gweid);
					if($pic_count==null){
						$insertschool = pdo_insert('school',array('homework_url' => $picUrl,'GWEID'=>$gweid));
					}else{
						$bac_update=pdo_update('school',array('homework_url'=>$picUrl),array('GWEID'=>$gweid));
					}
				}elseif($sid==3){
					$pic_count=pdo_fetchall("SELECT notice_url FROM wp_school where GWEID=".$gweid);
					if($pic_count==null){
						$insertschool = pdo_insert('school',array('notice_url' => $picUrl,'GWEID'=>$gweid));
					}else{
						$bac_update=pdo_update('school',array('notice_url'=>$picUrl),array('GWEID'=>$gweid));
					}
				}
				
				if($bac_update===false){
					echo "<h3>保存菜单图片失败！</h3>";
				}else{
					echo "<h3>保存菜单图片成功！</h3>";
				}
			}else{
				echo "<h3>保存菜单图片失败！</h3>";
			}
		}
		?>
			
		   <!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				<script language='javascript'>
					top.resizeTo(300, 200); 		
					setTimeout("self.close()", 2000); 
					opener.location.reload(); 
				</script>
			</head>
			</html>
			
		<?php
		
	}
			
		//删除已更新的图片
	public function doWebDeletemenupic(){
		global $_W,$_GPC;
	    $gweid =  $_W['gweid'];
		$del=$_GPC['del'];
		if($del==1){
			$bac_update=pdo_update('school',array('videopic_url'=>null),array('GWEID'=>$gweid));
		}else if($del==2){
			$bac_update=pdo_update('school',array('homework_url'=>null),array('GWEID'=>$gweid));
		}else if($del==3){
			$bac_update=pdo_update('school',array('notice_url'=>null),array('GWEID'=>$gweid));
		}
		include $this -> template('weschool_page_made');
	}
	
	//恢复默认的背景图片
	public function doWebDeletebaimg(){
		global $_W,$_GPC;
	    $gweid =  $_W['gweid'];
		$del=$_GPC['del'];
		$bac_update=pdo_update('school',array('bg_url'=>null),array('GWEID'=>$gweid));
		include $this -> template('weschool_page_made');
	}

	//当前用户有可能是分组管理员下的，如果分组管理员下的切换，需要找到对应的session中的值
	function site_issuperadmin($currentuserid){
	   	global $_W,$wpdb;
		$getgroupuserids = $wpdb->get_results( "SELECT group_id, flag FROM {$wpdb -> prefix}user_group where user_id = ".$currentuserid);
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
		//如果是分组管理员
		if($usergroupid !=0 && $usergroupflag == 1){
			$groupadminflag = 1;
		}else{
			$groupadminflag = 0;
		}

		return $groupadminflag;
	}
	
}