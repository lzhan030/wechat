/*
	根据html数据创建一个ITEM节点
*/
function buildAddForm(id, targetwrap) {
	var sourceobj = $('#' + id);
	var html = $('<div class="item">');
	id = id.split('-')[0];
	var size = $('.item').size();
	var htmlid = id + '-item-' + size;
	while (targetwrap.find('#' + htmlid).size() >= 1) {
		var htmlid = id + '-item-' + size++;
	}
	html.html(sourceobj.html().replace(/\(itemid\)/gm, htmlid));
	html.attr('id', htmlid);
	targetwrap.append(html);
	return html;
}

function doDeleteItem(itemid, deleteurl) {
	if (confirm('删除操作不可恢复，确认删除吗？')){
		if (deleteurl) {
			ajaxopen(deleteurl, function(){
				$('#' + itemid).remove();
			});
		} else {
			$('#' + itemid).remove();
		}	
	}
	return false;
}
