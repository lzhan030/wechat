<?php defined('IN_IA') or exit('Access Denied');?>
<div class="alert alert-info reply-market-list col-md-6">
    <input name="option_id[]" type="hidden" class="col-md-3" value="<?php  echo $tag;?>" />
    <table class="tb reply-news-edit" width="100%">
        <tbody>
            <tr>
                <th>选项名称</th>
                <td>
                    <input name="option_title[]" type="text" class="col-md-3 item_title form-control" value=""/>
                </td>
            </tr>
            <tr class="item-image" <?php  if($type!='image') { ?>style="display:none;"<?php  } ?>>
                <th>选项图片</th>
                <td>
                    <img class="item-image" src="" height='100' width='200' style="margin-top:6px;display:none;" />
                    <input type="file" class="form-control pic_upload item-image" name="option_thumb_<?php  echo $tag;?>" style="margin-top:5px;"/>
                    <div class="help-block item-image">建议尺寸：400像素 * 200像素</div>    
                    <input type="hidden" name="option_thumb_old_<?php  echo $tag;?>" class='vote_img_old'/>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>
                    <a href="javascript:;" onclick="deleteItem(this)" style="float:right;"  title="删除">删除投票项 <i class='icon-remove'></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
