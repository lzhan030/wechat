<?php

$path = explode ( 'wp-content', __FILE__ );
$wp_root_path = $path [0];
require_once ($wp_root_path . '/wp-load.php');

global  $wpdb;

$userid = $_GET['userid'];
$siteid = $_GET['siteid'];
$startdate = $_GET['startdate'];
$enddate = $_GET['enddate'];

//2014-07-15新增修改
$gweid = $_SESSION['GWEID'];

if ($_GET['Selected']!=1) {
			$startdate = $_GET['startdate'];
			$enddate = $_GET['enddate'];
		} else {
			$enddate=date("Y-m-d");
			switch($_GET['period']) {
				case 0 :
					$startdate=date("Y-m-d");
					break;
				case 1 :
					$startdate=date("Y-m-d",strtotime("-1 week +1 day"));
					break;
				case 2 :
					$startdate=date("Y-m-d",strtotime("-1 month +1 day"));
					break;
				case 3 :
					$startdate=date("Y-m-d",strtotime("-3 month +1 day"));
					break;
				case 4 :
					$startdate=date("Y-m-d",strtotime("-1 year +1 day"));
					break;
			}
		}
$jsonresult=array();
$day1 = 3600 * 24;
$month1 = 31 * $day1;
$year1 = 365 * $day1;
if(strtotime($enddate) - strtotime($startdate) <= $month1 && strtotime($enddate) - strtotime($startdate) > $day1){
	$current_date = $startdate;

	while($current_date <= $enddate) {
	
		//echo $current_date;
		if($siteid == 0){
			//$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 00:00:00' AND time <'".$current_date." 23:59:59' AND site_id IN(SELECT id FROM ".$wpdb->prefix."orangesite where site_user=".$userid.")");
			//2014-07-15 新增修改
			$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 00:00:00' AND time <'".$current_date." 23:59:59' AND site_id IN(SELECT id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." )");
			$element=array();
			$element['today']=$current_date;
			$element['countClick']=$clicktimes;
			$jsonresult[]=$element;
			
			}
		elseif($siteid>0){
			$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 00:00:00' AND time <'".$current_date." 23:59:59' AND site_id = ".$siteid);
			$element=array();
			$element['today']=$current_date;
			$element['countClick']=$clicktimes;
			$jsonresult[]=$element;
			}
		elseif($siteid == -1){
			//$clicktimes = $wpdb->get_results("SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 00:00:00' AND time <'".$current_date." 23:59:59' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." GROUP BY id ORDER BY id ASC");
			//2014-07-15新增修改
			$clicktimes = $wpdb->get_results("SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 00:00:00' AND time <'".$current_date." 23:59:59' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." GROUP BY id ORDER BY id ASC");
			//echo "SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 00:00:00' AND time <'".$current_date." 23:59:59' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." GROUP BY id ORDER BY id ASC";
			foreach($clicktimes as &$clicktime){
				$clicktime->site_name  = str_replace ( " ", "_" , $clicktime->site_name );
				if( !array_key_exists('_'.$clicktime->site_name.'_'.$clicktime->id,$jsonresult) )
					$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id] = array();
				$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id][] = array("today" => str_replace('-','/',$current_date), "countclick" => $clicktime->counts);
			}
		}
			
		//echo $clicktimes;

	$current_date = date("Y-m-d",strtotime("+1 day",strtotime($current_date)));
	}
	

}
if(strtotime($enddate) - strtotime($startdate) == 0)
{
    $current_date = $startdate;
    if($siteid == 0){
	    for($i =0; $i<24; $i++)
		{
	        $element=array();
			if($i < 10)
			{
				//$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 0".$i.":00:00' AND time <'".$current_date." 0".($i+1).":00:00' AND site_id IN(SELECT id FROM ".$wpdb->prefix."orangesite where site_user=".$userid.")");
				//2014-07-15新增修改
				$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 0".$i.":00:00' AND time <'".$current_date." 0".($i+1).":00:00' AND site_id IN(SELECT id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." )");
				
				$element['today']=$i;
				$element['countClick']=$clicktimes;
				$jsonresult[]=$element;
			}
			else
			{
			    
				//$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." ".$i.":00:00' AND time <'".$current_date." ".($i+1).":00:00' AND site_id IN(SELECT id FROM ".$wpdb->prefix."orangesite where site_user=".$userid.")");
				//2014-07-15新增修改
				$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." ".$i.":00:00' AND time <'".$current_date." ".($i+1).":00:00' AND site_id IN(SELECT id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." )");
				$element['today']=$i;
				$element['countClick']=$clicktimes;
				$jsonresult[]=$element;
			}
		}
			
	}
    elseif($siteid>0){
	     for($i =0; $i<24; $i++)
		{
	        $element=array();
		    if($i < 10)
			{
				$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 0".$i.":00:00' AND time <'".$current_date." 0".($i+1).":00:00' AND site_id = ".$siteid);
				
				$element['today']=$i;
				$element['countClick']=$clicktimes;
				$jsonresult[]=$element;
			}
			else
			{
			    $clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." ".$i.":00:00' AND time <'".$current_date." ".($i+1).":00:00' AND site_id = ".$siteid);
				
				$element['today']=$i;
				$element['countClick']=$clicktimes;
				$jsonresult[]=$element;
			}
		}
    
    }
	elseif($siteid == -1){
	    for($i =0; $i<24; $i++)
		{
		    if($i < 10)
			{
				//$clicktimes = $wpdb->get_results("SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 0".$i.":00:00' AND time <'".$current_date." 0".($i+1).":00:00' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." GROUP BY id ORDER BY id ASC");
				//2014-07-15新增修改
				$clicktimes = $wpdb->get_results("SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." 0".$i.":00:00' AND time <'".$current_date." 0".($i+1).":00:00' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." GROUP BY id ORDER BY id ASC");
				foreach($clicktimes as &$clicktime){
					$clicktime->site_name  = str_replace ( " ", "_" , $clicktime->site_name );
					if( !array_key_exists('_'.$clicktime->site_name.'_'.$clicktime->id,$jsonresult) )
						$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id] = array();
					//$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id][] = array("today" => $current_date." //0".$i.":00:00", "countclick" => $clicktime->counts);
					$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id][] = array("today" => $i, "countclick" => $clicktime->counts);
				}
				
			}
			else
			{
			    //$clicktimes = $wpdb->get_results("SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." ".$i.":00:00' AND time <'".$current_date." ".($i+1).":00:00' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." GROUP BY id ORDER BY id ASC");
				//2014-07-15新增修改
				 $clicktimes = $wpdb->get_results("SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".$current_date." ".$i.":00:00' AND time <'".$current_date." ".($i+1).":00:00' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." GROUP BY id ORDER BY id ASC");
				foreach($clicktimes as &$clicktime){
					$clicktime->site_name  = str_replace ( " ", "_" , $clicktime->site_name );
					if( !array_key_exists('_'.$clicktime->site_name.'_'.$clicktime->id,$jsonresult) )
						$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id] = array();
					//$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id][] = array("today" => $current_date." //".$i.":00:00", "countclick" => $clicktime->counts);
					$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id][] = array("today" => $i, "countclick" => $clicktime->counts);
				}
				
			}
		}
	}
}

if (strtotime($enddate) - strtotime($startdate) > $month1) {

/* 	while (strtotime($current_month) <= strtotime($end_month)) {
		$element = array();
		$clicktimes = pdo_fetchcolumn("SELECT count(*) from " . tablename('stat_site') . " WHERE time > :start AND time < :end AND weid=:weid ", array(':start' => ($start_month == $current_month ? $startdate : ($current_month . '-1')) . ' 00:00:00', ':end' => ($end_month == $current_month ? $enddate : ($current_month . '-31')) . ' 23:59:59', ':weid' => $_W['weid']));
		$element['today'] = $current_month;
		$element['countClick'] = $clicktimes;
		$jsonresult[] = $element;
		$current_month = date("Y-m", strtotime("+1 month", strtotime($current_month)));
	} */
	
	
	
	$start_month = date("Y-m", strtotime($startdate));
	$end_month = date("Y-m", strtotime($enddate));
	$current_month = $start_month;

	while(strtotime($current_month) <= strtotime($end_month)) {
	
		//echo $current_date;
		if($siteid == 0){
			//$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00' AND time <'".($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59' AND site_id IN(SELECT id FROM ".$wpdb->prefix."orangesite where site_user=".$userid.")");
			//2014-07-15新增修改
			$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00' AND time <'".($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59' AND site_id IN(SELECT id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." )");
			$element=array();
			$element['today']=$current_month;
			$element['countClick']=$clicktimes;
			$jsonresult[]=$element;
			
			}
		elseif($siteid>0){
			$clicktimes = $wpdb->get_var("SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00' AND time <'".($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59' AND site_id = ".$siteid);
			$element=array();
			$element['today']=$current_month;
			$element['countClick']=$clicktimes;
			$jsonresult[]=$element;
			}
		elseif($siteid == -1){
			//$clicktimes = $wpdb->get_results("SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00' AND time <'".($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." GROUP BY id ORDER BY id ASC");
			//2014-07-15新增修改
			$clicktimes = $wpdb->get_results("SELECT site_name, (SELECT count(*) from ".$wpdb->prefix."wechat_website_statistics WHERE time >'".($start_month == $current_month ? $startdate : ($current_month . '-1'))." 00:00:00' AND time <'".($end_month == $current_month ? $enddate : ($current_month . '-31'))." 23:59:59' AND site_id = ".$wpdb->prefix."orangesite.id) as counts, id FROM ".$wpdb->prefix."orangesite where site_user=".$userid." AND GWEID = ".$gweid." GROUP BY id ORDER BY id ASC");
			foreach($clicktimes as &$clicktime){
				$clicktime->site_name  = str_replace ( " ", "_" , $clicktime->site_name );
				if( !array_key_exists('_'.$clicktime->site_name.'_'.$clicktime->id,$jsonresult) )
					$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id] = array();
				$jsonresult['_'.$clicktime->site_name.'_'.$clicktime->id][] = array("today" => str_replace('-','/',$current_month), "countclick" => $clicktime->counts);
			}
		}
			
		//echo $clicktimes;

	$current_month = date("Y-m",strtotime("+1 month",strtotime($current_month)));
	}

}
echo json_encode($jsonresult);


	
?>

