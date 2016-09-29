<?php defined('IN_IA') or exit('Access Denied');?>
<?php include $this -> template('header');?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/init.css">
<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/we7/style/daterangepicker.css">
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/we7/script/daterangepicker.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js" type="text/javascript" ></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/jquery.tinymce.min.js"></script>
<script src="<?php bloginfo('template_directory') ?>/js/tinymce/tinymce.min.js"></script>
<style>
    .panel{border-radius: 0px; -webkit-box-shadow: 0 0px 0px rgba(0,0,0,0.05); box-shadow: 0 0px 0px rgba(0,0,0,0.05);}
    .sltfield{height: 34px;padding: 6px 12px;font-size: 14px;line-height: 1.428571429;color: #555;vertical-align: middle;background-color: #fff;background-image: none;border: 1px solid #ccc;border-radius: 4px;-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);-webkit-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;}
    .daterangepicker .ranges input {
        font-size:13px;
        cursor: not-allowed;
        background-color: #eeeeee;
        border:1px solid #cccccc;
        border-radius:4px;
        height:32px;
    }
    .daterangepicker .ranges label {
        font-size:11px;
        display:block;
        line-height:20px;
    }
    .daterangepicker_end_input{
        margin-left:-11px;
        margin-bottom:4px;
    }
    .daterangepicker select.hourselect, .daterangepicker select.minuteselect, .daterangepicker select.ampmselect {
      width: 60px;
      margin-bottom: 0;
      border: 1px solid #cccccc;  border-radius:4px;
    }
	th{
		padding-top: 15px;
		vertical-align: top;
	}
	td{padding: 10px;}
	.btn-default{border-radius:4px;}
</style>
<div class="main_auto">
    <div class="main-title">
    <div class="title-1">当前位置：<a href="<?php echo $this->createWebUrl('list',array());?>">微投票</a> > <font class="fontpurple"><?php echo empty($id)?"创建投票":"修改活动";?></font></div>
</div>
<form id="edit" action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="reply_id" value="<?php  echo $reply['id'];?>" />
    <div class="alert alert-block alert-new">
        <table>
            <?php if(!empty($reply['votenum'])){ ?>
                <div class="alert alert-danger col-md-6">
                    活动已经有用户投票，不能编辑
                </div>
            <?php } ?>
            <tbody>
                <tr>
                    <th style="width: 130px;">投票标题</th>
                    <td>
                        <input type="text" id="title" class="form-control" placeholder="" name="title" value="<?php  echo $reply['title'];?>">
                    </td>
                </tr>
                <tr>
                    <th>投票图片</th>
                    <td><img class="" src="<?php echo empty($reply['thumb'])?'':($baseurl.$reply['thumb']);?>" height='100' width='200' <?php if(empty($reply['thumb'])){ ?>style="display:none;"<?php } ?>/>
                        <input type="file" class="form-control pic_upload" name="thumb" id="thumb" style="margin-top:5px;"/>
                        <div class="help-block">建议尺寸：400像素 * 200像素</div>  
                    </td>
                </tr>
                <tr>
                    <th>投票内容</th>
                    <td>
                        <textarea style="height:150px;" id="description" name="description" class="form-control" ><?php echo $reply['description']; ?></textarea>
                        <div class="help-block">用于投票的说明</div>
                    </td>
                </tr>
            </tbody> 
        </table>

        <table>
            <tr>
                <th style="width: 130px;">投票限制</th>
                <td>
                    <div style="margin-bottom:46px;">
                        <div style="float:left;margin-right:8px;">
                            <label style="line-height:31px;display:block;"><input type="radio" name="votelimit" value="1"  <?php  if($reply['votelimit'] == 1) { ?> checked="checked"<?php  } ?>/>人数限制</label>
                        </div>
                        <div style="line-height:35px;float:left;">
                            <input type="text" class="form-control" id="votetotal" name="votetotal" value="<?php echo $reply['votetotal'];?>" style="float:left; width:60%"><span style="line-height:35px;margin-left: 5px;">人</span>
                        </div>
                    </div>
                    <div style="margin-top:12px;">
                        <div style="float:left;margin-right:8px;">
                            <label style="line-height:31px;"><input type="radio" name="votelimit" value="0" <?php  if($reply['votelimit'] == 0) { ?> checked="checked"<?php  } ?>/>时间限制</label>
                        </div>
                        <input name="datelimit-start" id="datelimit-start" type="hidden" value="<?php echo date('Y-m-d H:i', $reply['starttime'])?>" />
                       <input name="datelimit-end" id="datelimit-end" type="hidden" value="<?php echo date('Y-m-d H:i', $reply['endtime'])?>" />
                       <button class="btn btn-default" id="datelimit-date-range" class="date" type="button"><span class="date-title"><?php echo date('Y-m-d H:i', $reply['starttime'])?> 至 <?php echo date('Y-m-d H:i', $reply['endtime'])?></span> <i class="icon-caret-down"></i></button>
                    </div>
                </td>
            </tr>
            <tr>
                <th>每人投票次数</th>
                <td>
                    <div class="">
                        <input type="text" class="form-control" name="votetimes" id="votetimes" value="<?php  echo $reply['votetimes'];?>" style="width:50%;display: inline-block;"/>
                        <span class="add-on">次</span>
                    </div>
                    <div class="help-block">限制每人投票次数，0为不限制</div>
                </td>
            </tr>
            <tr>
                <th>投票选项类型</th>
                <td>
                    <label class="radio-inline"><input type="radio" name="votetype" value="0" <?php  if($reply['votetype'] == 0) { ?> checked="true" <?php  } ?>>单选</label>&nbsp;&nbsp;
                    <label class="radio-inline"><input type="radio" name="votetype" value="1" <?php  if($reply['votetype'] == 1) { ?> checked="true" <?php  } ?>>多选</label>&nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <th>投票类型</th>
                <td>
                    <label class="radio-inline"><input type="radio" name="isimg" onclick="changeTo('text')" id='isimg' value="0" <?php  if($reply['isimg'] == 0) { ?> checked="true" <?php  } ?>>文本</label>&nbsp;&nbsp;
                    <label class="radio-inline"><input type="radio" name="isimg" onclick="changeTo('image')" value="1" <?php  if($reply['isimg'] == 1) { ?> checked="true" <?php  } ?>>图片</label>&nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <th><label for="">投票选项</label></th>
                <td id="vote_options_list" style="padding-top: 18px;">
                    <?php  if(is_array($options)) { foreach($options as $o) { ?>
                        <div class="alert alert-info reply-market-list col-md-6">
                            <input name="option_id[]" type="hidden" class="col-md-3" value="<?php  echo $o['id'];?>" />
                            <table class="tb reply-news-edit" width="100%">
                                <tbody>
                                    <tr>
                                        <th>选项名称</th>
                                        <td>
                                            <input name="option_title[]" type="text" class="col-md-3 item_title form-control" value="<?php  echo $o['title'];?>"/>
                                        </td>
                                    </tr>
                                    <tr class="item-image" <?php  if(empty($reply['isimg'])) { ?>style="display:none;"<?php  } ?>>
                                        <th>选项图片</th>
                                        <td>
                                            <img class="item-image" src="<?php  echo (!empty($o['thumb']))?($baseurl.$o['thumb']):""; ?>" width='200' height='100' style="margin-top:6px;<?php echo empty($o['thumb'])?'display:none;':''; ?>"/>
                                            <input type="file" class="form-control pic_upload item-image" name="option_thumb_<?php  echo $o['id'];?>" style="margin-top:5px;"/>
                                            <div class="help-block item-image">建议尺寸：400像素 * 200像素 </div>    
                                            <input type="hidden" name="option_thumb_old_<?php  echo $o['id'];?>" value="<?php  echo $o['thumb'];?>" class='vote_img_old'/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <td>
                                            <a href="javascript:;" class="remove-item" onclick="deleteItem(this)" style="float:right;"  title="删除">删除投票项 <i class='icon-remove'></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php  } } ?>
                    <div id="items_div" tabindex="-1" class="alert alert-block alert-new" style="width:500px">
                        <?php  if($hasData) { ?>
                        <a href="javascript:;">已经存在投票, 不能修改投票信息</a>
                        <?php  } else { ?>
                        <a href="javascript:;" onclick="addItem();">添加投票选项 <i class="icon-plus-sign" title="添加投票选项"></i></a>
                        <?php  } ?>
                    </div>
                    <span class="help-block">投票开始后(已经有用户投票), 将不能再修改投票信息, 请仔细编辑. </span>
                </td>
            </tr>
            </tbody>
        </table>

    </div>
    <button class="btn btn-primary submit" style="margin-left: 300px;width: 100px;">提交</button>
</form>
<?php tinymce_js("#description"); ?>
<script type="text/javascript">
$('#edit').submit(function(){
    $('textarea[name="description"]').val($('#editor').html());
});
function changeTo(t){
    if(t=='image'){
        $(".item-image").show();
    }
    else{
        $(".item-image").hide();
    }
}
        function doDeleteItemImage(obj, id) {
            var filename = $('input#' + id + "-value").val();
                    $('.' + id + "_preview").html("");
                    $(obj).html("正在删除...").attr("disabled", true);
                    ajaxopen('./index.php?act=attachment&do=delete&filename=' + filename, function(){
                    $(obj).html("<i class='icon-upload-alt'></i> 删除").hide().removeAttr("disabled");
                    });
            }
    function addItem() {
        var url = "<?php  echo $this -> createWebUrl('item',array('name'=>'vote'))?>"+"&type=" +($("#isimg").get(0).checked?"text":"image");
        $.ajax({
           //'url': "./source/modules/vote/template/item.html" ,
           "url": url ,
           success:function(data){
               $("#itemlen").val( parseInt($("#itemlen").val()) + 1);
               $('#items_div').before(data);
           }

        });
        return;

    }
    function deleteItem(o) {
        $(o).parent().parent().parent().parent().parent().remove();
    }
function result(){

    ajaxshow("<?php  echo $this -> createWebUrl('result',array('id' => $reply['rid']))?>","查看票数");
}
<?php if(!empty($reply['votenum'])){ ?>
    $(function(){
        $('input,textarea').attr('disabled','disabled');
        $('#items_div>a,.remove-item,.submit').remove();
    })
<?php } ?>

    var itemcheck = function(){
          if($("#title").isEmpty()){
              Tip.focus("title",'请填写投票标题!',"right");
              return false;
          }
          if($(".voteitem").length<=1){
               Tip.focus("items_div",'至少二个投票选项!',"bottom");
              return false;
          }
          var full = true;
          $(".item_title").each(function(i){
              if( $(this).isEmpty()) {

                  Tip.focus(".item_title:eq(" + i + ")","请输入投票选项标题!","top");
                  full =false;
                  return false;
              }

          });

             if(!$("#isimg").get(0).checked){
                  $(".item-image").each(function(i){
                     //if( $(this).isEmpty()) {
                     if($(".vote_img_old",$(this)).isEmpty() && $(".vote_img_file",$(this)).isEmpty()){
                       Tip.focus(".item-image:eq(" + i + ")","请上传投票选项图片!","top");
                         full =false;
                         return false;
                     }
                });
              }
          return full;
    }
    function getFullPath(file) {    //得到图片的完整路径  
        var url = null ; 
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url ;
    } 
    $(thumb).change(function(){  
        objUrl = getFullPath(this.files[0]);
        if (objUrl) {
            $(this).prev().show();
            $(this).prev().attr("src", objUrl);
        }
    });
    $("#vote_options_list").on('change','.pic_upload',function(){  
        objUrl = getFullPath(this.files[0]);
        if (objUrl) {
            $(this).prev().show();
            $(this).prev().attr("src", objUrl);
        }
    });
    $("#datelimit-date-range").daterangepicker({
        startDate: $(":hidden[id=datelimit-start]").val(),
        endDate: $(":hidden[id=datelimit-end]").val(),
        format: "YYYY-MM-DD HH:mm",
            timePicker: true,
            timePicker12Hour : false,
            timePickerIncrement: 1,
            minuteStep: 1,

        locale: {
            applyLabel: "确定",
            cancelLabel: "取消",
            fromLabel: "从",
            toLabel: "至",
            weekLabel: "周",
            customRangeLabel: "日期范围",
            daysOfWeek: moment()._lang._weekdaysMin.slice(),
            monthNames: moment()._lang._monthsShort.slice(),
            firstDay: 0
        }
    }, function(start, end){
        $("#datelimit-date-range .date-title").html(start.format("YYYY-MM-DD HH:mm") + " 至 " + end.format("YYYY-MM-DD HH:mm"));
        $(":hidden[id=datelimit-start]").val(start.format("YYYY-MM-DD HH:mm"));
        $(":hidden[id=datelimit-end]").val(end.format("YYYY-MM-DD HH:mm"));
    });
</script> 