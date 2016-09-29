<?php
header('Content-type: application/json;charset=UTF-8');
if(1==0){
?>
$ticks =array('2013/7/7', '2013/7/8', '2013/7/9', '2013/7/10','2013/7/11','2013/7/12','2013/7/13','2013/7/14','2013/7/15','2013/7/16');
$display = array(435,354,234,345,345,334,334,534,322,111);
$click = array(78, 87, 55, 45, 45, 34, 34, 67, 86, 86);

$day_cost =  array(8, 5, 16, 7, 6, 4, 4, 5, 4, 4);
$click_rate = array(16,35,25,9,9,8,9,10,11,11);
$result=array(	'date' => $ticks,
				'site1' => $display,
				'site2' => $click,
				'site3' => $day_cost,
				'site4' => $click_rate,
);
echo json_encode($result);
<?php
}
?>
<?php 
$tmp=array("codes1" => array(array("today" =>"2013/12/29","countclick"=>33),array("today" =>"2013/12/30","countclick"=>11),array("today" =>"2013/12/31","countclick"=>53)),"codes2" => array(array("today" =>"2013/12/29","countclick"=>100),array("today" =>"2013/12/30","countclick"=>150),array("today" =>"2013/12/31","countclick"=>120)),"codes3" => array(array("today" =>"2013/12/29","countclick"=>200),array("today" =>"2013/12/30","countclick"=>130),array("today" =>"2013/12/31","countclick"=>240)));
//{"codes1":[{"today":"2013/12/29","countclick	":33},{"today":"2013/12/30","countclick":11},{"today":"2013/12/31","countclick":53}],"codes2":[{"today":"2013/12/29","countclick	":33},{"today":"2013/12/30","countclick":11},{"today":"2013/12/31","countclick":53}]}
echo json_encode($tmp);
?>