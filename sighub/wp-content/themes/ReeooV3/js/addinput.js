function createInput(parentID,inputFileID, maxNum){

if (maxNum > 0) {
x=document.getElementsByName(inputFileID);
y=x.length;
if (y >= maxNum) {
alert('���ֻ�������' + maxNum + '��');
return false;
}
}

var parent=$G(parentID);//��ȡ��Ԫ��

var div=document.createElement("div");//����һ��div����tb���ڰ���input file
var x=parseInt(Math.random()*(80-1))+1;
var divName=inputFileID+x.toString();//���div����������
div.name=divName;
div.id=divName;

var aElement=document.createElement("input"); //����input
aElement.name=inputFileID;
aElement.id=inputFileID;
aElement.type="text";//��������Ϊfile
aElement.className = "dynInputLen";

var delBtn=document.createElement("input");//�ٴ���һ������ɾ��input file��Button
delBtn.type="button";
delBtn.value=" ";
delBtn.className = "btn_del";
delBtn.onclick=function(){ removeInput(parentID,divName)};//Ϊbutton����tbonclick����

div.appendChild(aElement);//��input file����div����
div.appendChild(delBtn);//��ɾ����ť����div����
parent.appendChild(div);//��div�������븸Ԫ��
}
//============================
//����:ɾ��һ������input file��div ����
//����:parentID---input file�ؼ��ĸ�Ԫ��ID
// DelDivID----������input file��div ����ID
//============================
function removeInput(parentID,DelDivID){
var parent=$G(parentID);
parent.removeChild($G(DelDivID));
}
//ͨ��Ԫ��ID��ȡ�ĵ��е�Ԫ��
function $G(v){return document.getElementById(v);}