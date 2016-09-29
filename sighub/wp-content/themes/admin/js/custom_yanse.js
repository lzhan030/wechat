//<SCRIPT LANGUAGE=JavaScript>
var SelRGB = '';
var DrRGB = '';
var SelGRAY = '120';
var hexch = new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
function ToHex(n)
{ var h, l;
 n = Math.round(n);
 l = n % 16;
 h = Math.floor((n / 16)) % 16;
 return (hexch[h] + hexch[l]);
}
function DoColor(c, l)
{ var r, g, b;
  r = '0x' + c.substring(1, 3);
  g = '0x' + c.substring(3, 5);
  b = '0x' + c.substring(5, 7);
  
  if(l > 120)
  {
    l = l - 120;
    r = (r * (120 - l) + 255 * l) / 120;
    g = (g * (120 - l) + 255 * l) / 120;
    b = (b * (120 - l) + 255 * l) / 120;
  }else
  {
    r = (r * l) / 120;
    g = (g * l) / 120;
    b = (b * l) / 120;
  }
  return '#' + ToHex(r) + ToHex(g) + ToHex(b);
}
function EndColor()
{ var i;
  if(DrRGB != SelRGB)
  {
    DrRGB = SelRGB;
    for(i = 0; i <= 30; i ++)
      GrayTable.rows(i).bgColor = DoColor(SelRGB, 240 - i * 8);
  }
   if(navigator.appName.indexOf("Explorer") > -1){
    document.getElementById("theme_color").value = DoColor(document.getElementById("RGB").innerText,document.getElementById("GRAY").innerText);
  } else{
  document.getElementById("theme_color").value = DoColor(document.getElementById("RGB").textContent,document.getElementById("GRAY").textContent);
   } 
  ShowColor.bgColor =   document.getElementById("theme_color").value ;
}
 
//让innerText在火狐浏览器中兼容
function getEvent()
{
if(document.all)
{
return window.event;//如果是ie
}
func=getEvent.caller;
while(func!=null)
{
var arg0=func.arguments[0];
if(arg0)
{
if((arg0.constructor==Event || arg0.constructor ==MouseEvent)
||(typeof(arg0)=="object" && arg0.preventDefault && arg0.stopPropagation))
{
return arg0;
}
}
func=func.caller;
}
return null;
}
//结束：var evt=getEvent();
//var obj=evt.srcElement || evt.target; 调用的时候只需这两句
 
function ColorTableonmouseover(){
var evt=getEvent();
var obj=evt.srcElement || evt.target;
if(navigator.appName.indexOf("Explorer") > -1){
document.getElementById("RGB").innerText= obj.bgColor;
} else{
document.getElementById('RGB').textContent =obj.bgColor;
} 
 EndColor();
}
function ColorTableonclick(){
var evt=getEvent();
var obj=evt.srcElement || evt.target;
if(navigator.appName.indexOf("Explorer") > -1){
SelRGB= obj.bgColor;
} else{
SelRGB =obj.bgColor;
} 
 EndColor();
}
function ColorTableonmouseout(){
var evt=getEvent();
var obj=evt.srcElement || evt.target;
if(navigator.appName.indexOf("Explorer") > -1){
document.getElementById("RGB").innerText=SelRGB;
} else{
document.getElementById('RGB').textContent=SelRGB;
} 
 EndColor();
}
function GrayTableonmouseover(){
var evt=getEvent();
var obj=evt.srcElement || evt.target;
if(navigator.appName.indexOf("Explorer") > -1){
SelGRAY= obj.title;
} else{
SelGRAY=obj.title;
} 
 EndColor();
}
function GrayTableonmouseover(){
var evt=getEvent();
var obj=evt.srcElement || evt.target;
if(navigator.appName.indexOf("Explorer") > -1){
document.getElementById("GRAY").innerText=obj.title;
} else{
document.getElementById('GRAY').textContent =obj.title;
} 
 EndColor();
}
function GrayTableonmouseout(){
var evt=getEvent();
var obj=evt.srcElement || evt.target;
if(navigator.appName.indexOf("Explorer") > -1){
document.getElementById("GRAY").innerText=SelGRAY;
} else{
document.getElementById('GRAY').textContent =SelGRAY;
} 
 EndColor();
}
//确定
function submitcolor(){
  window.returnValue =  document.getElementById("theme_color").value ;
  window.close();
}
//</SCRIPT>
function wc(r, g, b, n)
{
 r = ((r * 16 + r) * 3 * (15 - n) + 0x80 * n) / 15;
 g = ((g * 16 + g) * 3 * (15 - n) + 0x80 * n) / 15;
 b = ((b * 16 + b) * 3 * (15 - n) + 0x80 * n) / 15;
 document.write('<TD BGCOLOR=#' + ToHex(r) + ToHex(g) + ToHex(b) + ' height=8 width=8></TD>');
}
var cnum = new Array(1, 0, 0, 1, 1, 0, 0, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1, 1, 0, 0);
  for(i = 0; i < 16; i ++)
  {
     document.write('<TR>');
     for(j = 0; j < 30; j ++)
     {
      n1 = j % 5;
      n2 = Math.floor(j / 5) * 3;
      n3 = n2 + 3;
      wc((cnum[n3] * n1 + cnum[n2] * (5 - n1)),
       (cnum[n3 + 1] * n1 + cnum[n2 + 1] * (5 - n1)),
       (cnum[n3 + 2] * n1 + cnum[n2 + 2] * (5 - n1)), i);
     }
     document.writeln('</TR>');
  }
