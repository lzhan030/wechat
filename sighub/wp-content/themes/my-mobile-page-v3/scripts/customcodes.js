(function() { 
		  
    tinymce.create('tinymce.plugins.subtitle', {  
        init : function(ed, url) {  
            ed.addButton('subtitle', {  
                title : 'Add a subtitle',  
                image : url+'/images/subtitle.gif',  
                onclick : function() {  
                     ed.selection.setContent('[subtitle]' + ed.selection.getContent() + '[/subtitle]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('subtitle', tinymce.plugins.subtitle);
	
    tinymce.create('tinymce.plugins.toogle', {  
        init : function(ed, url) {  
            ed.addButton('toogle', {  
                title : 'Add a toogle content',  
                image : url+'/images/toogle.gif',  
                onclick : function() {  
                     ed.selection.setContent('[toogle title=""]' + ed.selection.getContent() + '[/toogle]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('toogle', tinymce.plugins.toogle);
	
    tinymce.create('tinymce.plugins.tabsmenu', {  
        init : function(ed, url) {  
            ed.addButton('tabsmenu', {  
                title : 'Add 3 tabs content',  
                image : url+'/images/tabs.gif',  
                onclick : function() {  
                     ed.selection.setContent('[tabsmenu title1="" title2="" title3=""]' + ed.selection.getContent() + '[/tabsmenu][tabscontent content1="" content2="" content3=""][/tabscontent]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('tabsmenu', tinymce.plugins.tabsmenu);
	
    tinymce.create('tinymce.plugins.section', {  
        init : function(ed, url) {  
            ed.addButton('section', {  
                title : 'Add section content with left image and right text',  
                image : url+'/images/section.gif',  
                onclick : function() {  
                     ed.selection.setContent('[section imageurl="" title="" titleurl=""]' + ed.selection.getContent() + '[/section]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('section', tinymce.plugins.section);
		  
    tinymce.create('tinymce.plugins.imagefull', {  
        init : function(ed, url) {  
            ed.addButton('imagefull', {  
                title : 'Add a full width image',  
                image : url+'/images/imagefull.gif',  
                onclick : function() {  
                     ed.selection.setContent('[imagefull imagehover=""]' + ed.selection.getContent() + '[/imagefull]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('imagefull', tinymce.plugins.imagefull);
	
    tinymce.create('tinymce.plugins.imagehalf', {  
        init : function(ed, url) {  
            ed.addButton('imagehalf', {  
                title : 'Add a 2/row section images',  
                image : url+'/images/imagehalf.gif',  
                onclick : function() {  
                     ed.selection.setContent('[imagehalf][imageurl title="" imagehover=""]' + ed.selection.getContent() + '[/imageurl][imageurl title="" imagehover=""]' + ed.selection.getContent() + '[/imageurl][/imagehalf]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('imagehalf', tinymce.plugins.imagehalf);
	
    tinymce.create('tinymce.plugins.imagethird', {  
        init : function(ed, url) {  
            ed.addButton('imagethird', {  
                title : 'Add a 3/row section images',  
                image : url+'/images/imagethird.gif',  
                onclick : function() {  
                     ed.selection.setContent('[imagethird][imageurl title="" imagehover=""]' + ed.selection.getContent() + '[/imageurl][imageurl title="" imagehover=""]' + ed.selection.getContent() + '[/imageurl][imageurl title="" imagehover=""]' + ed.selection.getContent() + '[/imageurl][/imagethird]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('imagethird', tinymce.plugins.imagethird);
	
    tinymce.create('tinymce.plugins.videofull', {  
        init : function(ed, url) {  
            ed.addButton('videofull', {  
                title : 'Add a full width video',  
                image : url+'/images/videofull.gif',  
                onclick : function() {  
                     ed.selection.setContent('[videofull]' + ed.selection.getContent() + '[/videofull]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('videofull', tinymce.plugins.videofull);
	
    tinymce.create('tinymce.plugins.videohalf', {  
        init : function(ed, url) {  
            ed.addButton('videohalf', {  
                title : 'Add a 2/row section videos',  
                image : url+'/images/videohalf.gif',  
                onclick : function() {  
                     ed.selection.setContent('[videohalf][videohalfurl title=""]' + ed.selection.getContent() + '[/videohalfurl][/videohalf]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('videohalf', tinymce.plugins.videohalf);
	
    tinymce.create('tinymce.plugins.slideshow', {  
        init : function(ed, url) {  
            ed.addButton('slideshow', {  
                title : 'Add images slideshow',  
                image : url+'/images/slideshow.gif',  
                onclick : function() {  
                     ed.selection.setContent('[slideshow][slide-image]' + ed.selection.getContent() + '[/slide-image][/slideshow]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('slideshow', tinymce.plugins.slideshow);
	
    tinymce.create('tinymce.plugins.social', {  
        init : function(ed, url) {  
            ed.addButton('social', {  
                title : 'Add social icons',  
                image : url+'/images/social.gif',  
                onclick : function() {  
                     ed.selection.setContent('[social][socialicon url=""]' + ed.selection.getContent() + '[/socialicon][/social]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('social', tinymce.plugins.social);
	
    tinymce.create('tinymce.plugins.callbutton', {  
        init : function(ed, url) {  
            ed.addButton('callbutton', {  
                title : 'Add a phone call button',  
                image : url+'/images/call.gif',  
                onclick : function() {  
                     ed.selection.setContent('[callbutton phone=""]' + ed.selection.getContent() + '[/callbutton]'); 
                }  
            });  
        },  
        createControl : function(n, cm) {  
            return null;  
        },  
    });  
    tinymce.PluginManager.add('callbutton', tinymce.plugins.callbutton);
	

})();