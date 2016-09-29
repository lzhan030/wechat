<?php

	
    $custom_colors = '';
    if($this->display('bg_color')) {
        $custom_colors .= "body { background-color: #" . $this->get_option('bg_color') ."; }\n";
    }
    if($this->display('bg_image')) {
        $custom_colors .= "#pagecontainer { background:url(" . $this->get_option('bg_image') .") no-repeat center top; }\n";
    }
    if($this->display('center_color')) {
        $custom_colors .= ".content { background-color: #" . $this->get_option('center_color') ."; }\n";
    }
    if($this->display('center_text_color')) {
        $custom_colors .= "body { color: #" . $this->get_option('center_text_color') ."; }\n";
		$custom_colors .= "span.subtitle_descr{ color: #" . $this->get_option('center_text_color') ."; }\n";
		$custom_colors .= ".trigger a{ color: #" . $this->get_option('center_text_color') ."; }\n";
		$custom_colors .= "input.search_input{ color: #" . $this->get_option('center_text_color') ."; }\n";
    }
    if($this->display('title_color')) {
        $custom_colors .= ".logo-title { color: #" . $this->get_option('title_color') ."; }\n";
    }
	if($this->display('links_colors')) {
        $custom_colors .= "a { color: #" . $this->get_option('links_colors') ."; }\n";
		$custom_colors .= "span.tag { color: #" . $this->get_option('links_colors') ."; }\n";
		$custom_colors .= ".trigger a:hover, .trigger a:hover:focus { color: #" . $this->get_option('links_colors') ."; }\n";
		$custom_colors .= "ul.tabsmenu li.active a { color: #" . $this->get_option('links_colors') ."; }\n";
		$custom_colors .= ".post_detail { color: #" . $this->get_option('links_colors') ."; }\n";
		$custom_colors .= ".form label { color: #" . $this->get_option('links_colors') ."; }\n";
    }
    if($this->display('headerfooter_color')) {
        $custom_colors .= "#header { color: #" . $this->get_option('headerfooter_color') ."; }\n";
		$custom_colors .= "#footer { color: #" . $this->get_option('headerfooter_color') ."; }\n";
    }
    if($this->display('headerfooterbuttons_color')) {
        $custom_colors .= "a.back_button { color: #" . $this->get_option('headerfooterbuttons_color') ."; }\n";
		$custom_colors .= "a#menu_open { color: #" . $this->get_option('headerfooterbuttons_color') ."; }\n";
		$custom_colors .= "a#top { color: #" . $this->get_option('headerfooterbuttons_color') ."; }\n";
    }

?>
