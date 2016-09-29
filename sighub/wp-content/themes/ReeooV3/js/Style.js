$(document).ready(function(){
  
$("#checkbox1").click(function(){
    if($(this).attr("src")=="resource/checkbox.jpg")
      $(this).attr("src","resource/checkboxchecked.jpg");
    else
      $(this).attr("src","resource/checkbox.jpg");
        
});



 function tabs(tabTit,on,tabCon){
	$(tabCon).each(function(){
	  $(this).children().eq(0).show();
	  });
	$(tabTit).each(function(){
	  $(this).children().eq(0).addClass(on);
	  });
     $(tabTit).children().hover(function(){
        $(this).addClass(on).siblings().removeClass(on);
         var index = $(tabTit).children().index(this);
         $(tabCon).children().eq(index).show().siblings().hide();
         
    });
     }
 
  
  
  
   $("#favorable").hover(function(){
    if($(this).attr("src")=="resource/Youhui.jpg")
      $(this).attr("src","resource/YouhuiOnclick.jpg");
   
      
      $("#common").attr("src","resource/Changyong.jpg");
      $("#download").attr("src","resource/Xiazai.jpg");
      $("#management").attr("src","resource/Licai.jpg");
      $("#sunshine").attr("src","resource/Yangguang.jpg");
      $("#common1").attr("src","resource/ChangyongDetail.jpg");  
      
      $("#content2").hide();
      $("#bt").show();
      $("#bt1").show();
      $("#bt2").show();
      $("#bt3").show();
      $("#bt4").show();
      $("#bt5").show();
      $("#bt6").show();
         
   });
   $("#management").hover(function(){
    if($(this).attr("src")=="resource/Licai.jpg")
      $(this).attr("src","resource/LicaiOnclick.jpg");
   
        
      $("#common").attr("src","resource/Changyong.jpg");
      $("#download").attr("src","resource/Xiazai.jpg");
      $("#favorable").attr("src","resource/Youhui.jpg");
      $("#sunshine").attr("src","resource/Yangguang.jpg");
      $("#common1").attr("src","resource/ChangyongDetail.jpg"); 
      
      $("#content2").hide();
      $("#bt").show();
      $("#bt1").show();
      $("#bt2").show();
      $("#bt3").show();
      $("#bt4").show();
      $("#bt5").show();
      $("#bt6").show();     
            
   });
   $("#sunshine").hover(function(){
    if($(this).attr("src")=="resource/Yangguang.jpg")
      $(this).attr("src","resource/YangguangOnclick.jpg");
      
      $("#management").attr("src","resource/Licai.jpg");
      $("#download").attr("src","resource/Xiazai.jpg");
      $("#favorable").attr("src","resource/Youhui.jpg");
      $("#common").attr("src","resource/Changyong.jpg");
      $("#common1").attr("src","resource/ChangyongDetail.jpg"); 
      
      $("#content2").hide();
      $("#bt").show();
      $("#bt1").show();
      $("#bt2").show();
      $("#bt3").show();
      $("#bt4").show();
      $("#bt5").show();
      $("#bt6").show();
            
   });
   $("#common").hover(function(){
    if($(this).attr("src")=="resource/Changyong.jpg")
      $(this).attr("src","resource/ChangyongOnclick.jpg");
      
      $("#download").attr("src","resource/Xiazai.jpg");
      $("#favorable").attr("src","resource/Youhui.jpg");
      $("#management").attr("src","resource/Licai.jpg");
      $("#sunshine").attr("src","resource/Yangguang.jpg");
      $("#common1").attr("src","resource/ChangyongDetail.jpg"); 
      
      $("#content2").hide();
      $("#bt").show();
      $("#bt1").show();
      $("#bt2").show();
      $("#bt3").show();
      $("#bt4").show();
      $("#bt5").show();
      $("#bt6").show();
     
   });
    $("#download").hover(function(){
    if($(this).attr("src")=="resource/Xiazai.jpg")
      $(this).attr("src","resource/XiazaiOnclick.jpg");
      
      
      $("#common").attr("src","resource/Changyong.jpg");
      $("#favorable").attr("src","resource/Youhui.jpg");
      $("#management").attr("src","resource/Licai.jpg");
      $("#sunshine").attr("src","resource/Yangguang.jpg");
      $("#common1").attr("src","resource/ChangyongDetail.jpg"); 
      
      $("#content2").hide();
      $("#bt").show();
      $("#bt1").show();
      $("#bt2").show();
      $("#bt3").show();
      $("#bt4").show();
      $("#bt5").show();
      $("#bt6").show();
            
   });
   
   tabs(".tab-hd","active",".tab-bd");
   
   $("#bt2").click(function(){
      
      $("#content2").show();
      $("#bt").hide();
      $("#bt1").hide();
      $("#bt2").hide();
      $("#bt3").hide();
      $("#bt4").hide();
      $("#bt5").hide();
      $("#bt6").hide();
      
   });
  

  
});
