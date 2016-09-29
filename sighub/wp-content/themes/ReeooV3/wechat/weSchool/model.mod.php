<?
function test(){
    global $wpdb;
    return $wpdb -> get_results("SELECT * FROM wp_posts LIMIT 2");
    
}