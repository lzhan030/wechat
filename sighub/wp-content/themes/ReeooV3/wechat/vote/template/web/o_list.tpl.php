<?php defined('IN_IA') or exit('Access Denied');?>﻿<?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<?php  include template('common', TEMPLATE_INCLUDEPATH);?>
<div class="main">
    <div class="stat">
        <div class="stat-div">
            <div class="navbar navbar-static-top">
                <div class="navbar-inner">
                    <span class="pull-right" style="color:red; padding:10px 10px 0 0;">总数：<?php  echo count($list);?></span>
                    <span class="brand">投票记录</span>
                </div>
            </div>
            <div class="sub-item" id="table-list">
                <h4 class="sub-title" style="float:right;color:red;"><a href="">刷新</a></h4>
                <h4 class="sub-title">名单</h4>

                <form action="" method="post" onsubmit="">
                    <div class="sub-content">
                        <table class="table table-hover">
                            <thead class="navbar-inner">
                                <tr>
                                    
                                    <th class="row-hover">用户</th>
                                    <th class="row-hover">投票项</th>
                                    <th class="row-hover">投票时间</th>
                                </tr>
                            </thead>
                            <tbody class="navbar-inner">

                                <?php  if(is_array($list)) { foreach($list as $v) { ?>
                                <tr>
                                   
                                    <td style="text-align: center;">
                                        <?php  echo $v['from_user'];?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php  echo $v['votes'];?>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php  echo date('Y-m-d h:s',$v['votetime'])?>
                                    </td>
                                    								
                                </tr>
                                <?php  } } ?>
                            </tbody>
                        </table>
                        <!-- 						<table class="table">
                                                                                <tr style="width:40px;" class="row-first">
                                                                                        
                                                                                                <th style="width:40px;" class="row-first"><input type="checkbox" onclick="selectall(this, 'select');"/>全选</th>
                                                                                
                                                                                        
                                                                                        <td>
                                                                                                <input type="submit" name="delete" value="删除" class="btn btn-primary" />
                                                                                                <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                                                                                        </td>
                                                                                </tr>
                                                                        </table> -->
                    </div>
                </form>
                <?php  echo $pager;?>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        //详细数据相关操作
        var tdIndex;
        $("#table-list thead").delegate("th", "mouseover", function() {
            if ($(this).find("i").hasClass("")) {
                $("#table-list thead th").each(function() {
                    if ($(this).find("i").hasClass("icon-sort"))
                        $(this).find("i").attr("class", "");
                });
                $("#table-list thead th").eq($(this).index()).find("i").addClass("icon-sort");
            }
        });
        $("#table-list thead th").click(function() {
            if ($(this).find("i").length > 0) {
                var a = $(this).find("i");
                if (a.hasClass("icon-sort") || a.hasClass("icon-caret-up")) { //递减排序
                    /*
                     数据处理代码位置
                     */
                    $("#table-list thead th i").attr("class", "");
                    a.addClass("icon-caret-down");
                } else if (a.hasClass("icon-caret-down")) { //递增排序
                    /*
                     数据处理代码位置
                     */
                    $("#table-list thead th i").attr("class", "");
                    a.addClass("icon-caret-up");
                }
                $("#table-list thead th,#table-list tbody:eq(0) td").removeClass("row-hover");
                $(this).addClass("row-hover");
                tdIndex = $(this).index();
                $("#table-list tbody:eq(0) tr").each(function() {
                    $(this).find("td").eq(tdIndex).addClass("row-hover");
                });
            }
        });
    });
</script>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>