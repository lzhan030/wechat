   
    $admincorejQ=jQuery.noConflict();
        
    function admincore_ajax(requests,appendto,loading) 
    {
    	$admincorejQ.ajax({
    		url: "admin-ajax.php",
    		type: "POST",
    		dataType: "html",
    		data: "action=admincore_ajax&_ajax_nonce="+admincore_nonce+"&"+requests+"",
    		success: function(response){$admincorejQ("#"+appendto+"").html(response);}
    	});
        
        if(loading) {
            admincore_loading(appendto);
        }
    	
    }
    
    function admincore_loading(appendto) 
    {
    	$admincorejQ("#"+appendto+"").empty();
        $admincorejQ("#"+appendto+"").show();
    	$admincorejQ("#"+appendto+"").append('<p style="padding:4px; margin:4px;"><img src="images/loading.gif" align="absmiddle"> <span style="font-size:11px; color: #999;">Loading, please wait...</span></p>');
    }
    
    
    function admincore_form(requests,formname,appendto,loading) 
    {
    	$admincorejQ.ajax({
    		url: "admin-ajax.php?action=admincore_ajax&_ajax_nonce="+admincore_nonce+"&act="+requests+"",
    		type: "POST",
    		dataType: "html",
    		data: $admincorejQ("#"+formname+"").serialize(),
    		success: function(response){$admincorejQ("#"+appendto+"").html(response);}
    	});
        
        if(loading) {
            admincore_loading(appendto);
        }
    	return false;
    }
    
    function admincore_savechanges(requests,formname,appendto) 
    {
    	$admincorejQ.ajax({
            url: "admin-ajax.php?action=admincore_ajax&_ajax_nonce="+admincore_nonce+"&act="+requests+"",
    		type: "POST",
    		dataType: "html",
    		data: $admincorejQ("#"+formname+"").serialize(),
    		success: function(response){
    		    $admincorejQ("#"+appendto+"").empty();
                $admincorejQ("#"+appendto+"").show();
                $admincorejQ("#"+appendto+"").html(response);
                $admincorejQ("#"+appendto+"").fadeIn(5000);
                $admincorejQ("#"+appendto+"").fadeOut(1000);
              }
    	});
        $admincorejQ("#"+appendto+"").empty();
        $admincorejQ("#"+appendto+"").show();
	    $admincorejQ("#"+appendto+"").append('<img src="images/loading.gif" align="absmiddle"> <span style="font-size:11px; color: #999;">Saving changes, please wait...</span>');
        return false;
    }
        
    function admincore_showHide(id)
    {
    	if ($admincorejQ("#"+id+"").is(":hidden")) {
            $admincorejQ("#"+id+"").slideDown('fast');
          } else {
        	  $admincorejQ("#"+id+"").slideUp('fast');
          }
    }
    
    function admincore_hide(id) 
    {
    	$admincorejQ("#"+id+"").empty();
    }
    
    function admincore_remove(id) 
    {
    	$admincorejQ("#"+id+"").remove();
    }
    
    function admincore_hoverShow(id) 
    {
    	$admincorejQ("#"+id+"").css("display","inline");
    }
    
    function admincore_hoverHide(id) 
    {
    	$admincorejQ("#"+id+"").css("display","none");
    } 
 	
    jQuery(document).ready(function($){
        
        // Navigation Tabs
        $("a.admin-tab").click(function () {
        	$(".admin-tab-active").removeClass("admin-tab-active");
        	$(this).addClass("admin-tab-active");
        	$(".admin-menu-content").hide();
        	var change_content= $(this).attr("id");
        	$("."+change_content).fadeIn();
           
        });
        
        // Image Upload
         $('.admin_imageupload').each(function(){
			
			var clickedObject = $(this);
            var clickedID = $(this).attr('id');
			var getClickedID = clickedID.replace("admincore_image_upload_", "");
            	
			new AjaxUpload(clickedID, {
			  action: 'admin-ajax.php?action=admincore_ajax&_ajax_nonce='+admincore_nonce+'&act=imageupload',
			  name: clickedID,
			  data: { 
				imgname: clickedID
                },
                
			  onChange: function(file, extension){},
              
			  onSubmit: function(file, extension){
					clickedObject.text('Uploading'); 
					this.disable(); 
					interval = window.setInterval(function(){
						var text = clickedObject.text();
						if (text.length < 13){	clickedObject.text(text + '.'); }
						else { clickedObject.text('Uploading'); } 
					}, 200);
			  },
              
			  onComplete: function(file, response) {
			   if(response.search('Upload Error') > -1){
			            window.clearInterval(interval);
    				    clickedObject.text('Upload Now');
                        this.enable(); 
			            $('#'+getClickedID+'_error').text(response);
						$('#'+getClickedID+'_error').show();
					
				} else{
    				window.clearInterval(interval);
    				clickedObject.text('Upload Now');	
    				this.enable(); 
                    $('#'+getClickedID+'_error').hide();
    				$('.'+clickedID+'').val(response);	
    				$('#'+getClickedID+'_reset').show();
                    var previewImage = '<a href="'+response+'" target="_blank"><img src="'+response+'" title="The image might be resized, click for full preview!" alt="" /></a><br /><span>The image might be resized, click for full preview!</span>';
                    $('#'+getClickedID+'_preview').html(previewImage);
                    $('#'+getClickedID+'_preview').show();
                } 
              }
              
			});
		});
        
        // Reset the image filed
        $('.admin_imageupload_reset').click(function(){
			
			var clickedObject = $(this);
			var clickedID = $(this).attr('id');
			var theID = $(this).attr('title');	

			$('.admincore_image_upload_'+theID+'').val('');	
			$('#'+clickedID+'').hide();
            $('#'+theID+'_preview').hide();
			return false; 
			
		}); 
        $('#admin-menuwrap ul').hide();
        $('.admin-first-menu').slideDown();
        
    });	 	
    