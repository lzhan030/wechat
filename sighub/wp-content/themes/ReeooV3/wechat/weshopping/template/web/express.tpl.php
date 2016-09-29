<?php defined('IN_IA') or exit('Access Denied');?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php include $this -> template('header');?>
<div class="main-title">
	<div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('index',array());?>">微商城</a> > <font class="fontpurple">物流管理</font></div>
</div>
<div class="shop_content">
	<ul class="nav nav-tabs">
		<li <?php  if($operation == 'display') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('express',array('op' =>'display'))?>">物流公司</a></li>
		<li<?php  if($operation == 'post' && empty($express['id'])) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('express',array('op' =>'post'))?>">添加物流公司</a></li>
		<?php  if(!empty($express['id']) && $operation== 'post') { ?> <li class="active"><a href="<?php  echo $this->createWebUrl('express',array('op' =>'post','id'=>$express['id']))?>">编辑物流公司</a></li> <?php  } ?>
	</ul>
	<?php  if($operation == 'display') { ?>
	<div class="main panel panel-default" style="height: auto;">
		<div class="panel-heading">物流公司列表</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover" style="min-width:800px;">
				<thead>
					<tr>
						<th style="width:50px;">编号</th>
						<th style="width:150px;">物流公司名称</th>
						<th style="width:150px">公司网站</th>
						<th style="width:150px;">说明</th>
						<th style="width:120px;">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($list)) { foreach($list as $item) { ?>
					<tr>
						<td><?php  echo $item['id'];?></td>
						<td><?php  echo $item['express_name'];?></td>
						<td><input type="text" class="form-control" value="<?php echo $item['express_url'];?>" readonly="readonly" /></td>
						<td><?php  echo $item['express_des'];?></td>
						<td style="text-align:left;">
							<a href="<?php  echo $this->createWebUrl('express', array('op' => 'delete', 'id' => $item['id']))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" class="btn btn-warning btn-sm" ata-placement="bottom" >删除</a>
							<a href="<?php  echo $this->createWebUrl('express', array('op' => 'post', 'id' => $item['id']))?>" class="btn btn-info btn-sm" data-placement="bottom" >修改</a>
						</td>
					</tr>
					<?php  } } ?>
				</tbody>
			</table>
			<?php echo $pager;?>
		</div>
	</div>
</div>
<?php  } else if($operation == 'post') { ?>

<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return formcheck()'>
		<input type="hidden" name="id" value="<?php  echo $express['id'];?>" />
		<div class="panel panel-default">
			<div class="panel-heading">
				物流详细设置
			</div>
			<div class="panel-body">
				<!--Comment: 常用快递公司用于提供相应的快递接口操作的快递公司-->
				<!--div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">常用快递公司</label>
					<div class="col-sm-9 col-xs-12">
					<select id="common_corp" class="form-control input-medium">
						<option value="" data-name="">其他快递</option>
						<option value="shunfeng" data-name="顺丰">顺丰</option>
						<option value="shentong" data-name="申通">申通</option>
						<option value="yunda" data-name="韵达快运">韵达快运</option>
						<option value="tiantian" data-name="天天快递">天天快递</option>
						<option value="yuantong" data-name="圆通速递">圆通速递</option>
						<option value="zhongtong" data-name="中通速递">中通速递</option>
						<option value="ems" data-name="ems快递">ems快递</option>
						<option value="huitongkuaidi" data-name="汇通快运">汇通快运</option>
						<option value="quanfengkuaidi" data-name="全峰快递">全峰快递</option>
						<option value="zhaijisong" data-name="宅急送">宅急送</option>
						<option value="aae" data-name="aae全球专递">aae全球专递</option>
						<option value="anjie" data-name="安捷快递">安捷快递</option>
						<option value="anxindakuaixi" data-name="安信达快递">安信达快递</option>
						<option value="biaojikuaidi" data-name="彪记快递">彪记快递</option>
						<option value="bht" data-name="bht">bht</option>
						<option value="baifudongfang" data-name="百福东方国际物流">百福东方国际物流</option>
						<option value="coe" data-name="中国东方（COE）">中国东方（COE）</option>
						<option value="changyuwuliu" data-name="长宇物流">长宇物流</option>
						<option value="datianwuliu" data-name="大田物流">大田物流</option>
						<option value="debangwuliu" data-name="德邦物流">德邦物流</option>
						<option value="dhl" data-name="dhl">dhl</option>
						<option value="dpex" data-name="dpex">dpex</option>
						<option value="dsukuaidi" data-name="d速快递">d速快递</option>
						<option value="disifang" data-name="递四方">递四方</option>
						<option value="fedex" data-name="fedex（国外）">fedex（国外）</option>
						<option value="feikangda" data-name="飞康达物流">飞康达物流</option>
						<option value="fenghuangkuaidi" data-name="凤凰快递">凤凰快递</option>
						<option value="feikuaida" data-name="飞快达">飞快达</option>
						<option value="guotongkuaidi" data-name="国通快递">国通快递</option>
						<option value="ganzhongnengda" data-name="港中能达物流">港中能达物流</option>
						<option value="guangdongyouzhengwuliu" data-name="广东邮政物流">广东邮政物流</option>
						<option value="gongsuda" data-name="共速达">共速达</option>
						<option value="hengluwuliu" data-name="恒路物流">恒路物流</option>
						<option value="huaxialongwuliu" data-name="华夏龙物流">华夏龙物流</option>
						<option value="haihongwangsong" data-name="海红">海红</option>
						<option value="haiwaihuanqiu" data-name="海外环球">海外环球</option>
						<option value="jiayiwuliu" data-name="佳怡物流">佳怡物流</option>
						<option value="jinguangsudikuaijian" data-name="京广速递">京广速递</option>
						<option value="jixianda" data-name="急先达">急先达</option>
						<option value="jjwl" data-name="佳吉物流">佳吉物流</option>
						<option value="jymwl" data-name="加运美物流">加运美物流</option>
						<option value="jindawuliu" data-name="金大物流">金大物流</option>
						<option value="jialidatong" data-name="嘉里大通">嘉里大通</option>
						<option value="jykd" data-name="晋越快递">晋越快递</option>
						<option value="kuaijiesudi" data-name="快捷速递">快捷速递</option>
						<option value="lianb" data-name="联邦快递（国内）">联邦快递（国内）</option>
						<option value="lianhaowuliu" data-name="联昊通物流">联昊通物流</option>
						<option value="longbanwuliu" data-name="龙邦物流">龙邦物流</option>
						<option value="lijisong" data-name="立即送">立即送</option>
						<option value="lejiedi" data-name="乐捷递">乐捷递</option>
						<option value="minghangkuaidi" data-name="民航快递">民航快递</option>
						<option value="meiguokuaidi" data-name="美国快递">美国快递</option>
						<option value="menduimen" data-name="门对门">门对门</option>
						<option value="ocs" data-name="OCS">OCS</option>
						<option value="peisihuoyunkuaidi" data-name="配思货运">配思货运</option>
						<option value="quanchenkuaidi" data-name="全晨快递">全晨快递</option>
						<option value="quanjitong" data-name="全际通物流">全际通物流</option>
						<option value="quanritongkuaidi" data-name="全日通快递">全日通快递</option>
						<option value="quanyikuaidi" data-name="全一快递">全一快递</option>
						<option value="rufengda" data-name="如风达">如风达</option>
						<option value="santaisudi" data-name="三态速递">三态速递</option>
						<option value="shenghuiwuliu" data-name="盛辉物流">盛辉物流</option>
						<option value="sue" data-name="速尔物流">速尔物流</option>
						<option value="shengfeng" data-name="盛丰物流">盛丰物流</option>
						<option value="saiaodi" data-name="赛澳递">赛澳递</option>
						<option value="tiandihuayu" data-name="天地华宇">天地华宇</option>
						<option value="tnt" data-name="tnt">tnt</option>
						<option value="ups" data-name="ups">ups</option>
						<option value="wanjiawuliu" data-name="万家物流">万家物流</option>
						<option value="wenjiesudi" data-name="文捷航空速递">文捷航空速递</option>
						<option value="wuyuan" data-name="伍圆">伍圆</option>
						<option value="wxwl" data-name="万象物流">万象物流</option>
						<option value="xinbangwuliu" data-name="新邦物流">新邦物流</option>
						<option value="xinfengwuliu" data-name="信丰物流">信丰物流</option>
						<option value="yafengsudi" data-name="亚风速递">亚风速递</option>
						<option value="yibangwuliu" data-name="一邦速递">一邦速递</option>
						<option value="youshuwuliu" data-name="优速物流">优速物流</option>
						<option value="youzhengguonei" data-name="邮政包裹挂号信">邮政包裹挂号信</option>
						<option value="youzhengguoji" data-name="邮政国际包裹挂号信">邮政国际包裹挂号信</option>
						<option value="yuanchengwuliu" data-name="远成物流">远成物流</option>
						<option value="yuanweifeng" data-name="源伟丰快递">源伟丰快递</option>
						<option value="yuanzhijiecheng" data-name="元智捷诚快递">元智捷诚快递</option>
						<option value="yuntongkuaidi" data-name="运通快递">运通快递</option>
						<option value="yuefengwuliu" data-name="越丰物流">越丰物流</option>
						<option value="yad" data-name="源安达">源安达</option>
						<option value="yinjiesudi" data-name="银捷速递">银捷速递</option>
						<option value="zhongtiekuaiyun" data-name="中铁快运">中铁快运</option>
						<option value="zhongyouwuliu" data-name="中邮物流">中邮物流</option>
						<option value="zhongxinda" data-name="忠信达">忠信达</option>
						<option value="zhimakaimen" data-name="芝麻开门">芝麻开门</option>
					</select>
					<span class="help-block">如果您选择了常用快递，则客户可以订单中查询快递信息，如果缺少您想要的快递，您可以联系我们! </span>
					</div>
				</div-->
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">物流公司名称(必填)</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id='express_name' name="express_name" class="form-control" value="<?php  echo $express['express_name'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">公司网站</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="express_url" class="form-control" value="<?php  echo $express['express_url'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">详细说明</label>
					<div class="col-sm-9 col-xs-12">
						<textarea type="text" name="express_des" class="form-control"><?php  echo $express['express_des'];?></textarea>
					</div>
				</div>
			</div>
		</div>
	<div class="form-group col-sm-12">
		<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick='return formcheck()' />
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
	</div>
	</form>
</div>
<script language='javascript'>
	function formcheck(){
		if($("#express_name").isEmpty()){
			alert("请填写物流公司名称!");
			return false;
		}
		return true;
	}
	
	$(function(){
		$("#common_corp").change(function(){
			var obj = $(this);
			var sel =obj.find("option:selected");
			$("#express_name").val(sel.attr("data-name"));
			$("#express_url").val(sel.val() );
		});
		<?php  if(!empty($express['id'])) { ?>
		$("#common_corp").val(  "<?php  echo $express['express_url'];?>");
		<?php  } ?>
		
	})
	</script>
<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>