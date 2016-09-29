document.onclick=function (ev)
{
	var ev=ev||event;
	var oTarget=ev.srcElement || ev.target;

	if(oTarget.nodeName.toLowerCase()=='input' && oTarget.getAttribute('type')=='checkbox')
	{
		var aCheckBox=document.getElementsByName('inputCheckBox');

		for(var i=0; i<aCheckBox.length; i++)
		{
			if(aCheckBox[i].getAttribute('type')=='checkbox')
			{
				aCheckBox[i].checked=false;
			}
		}
			oTarget.checked=true;
	}
}

