<?php

/*
Plugin Name: Barebones RSS
Description: This is a wordpress plugin to implement the functionality of niophoto.com's photoshelter-rss-thumbnail-gallery-widget.
Author: Andy - <a href="http://hypnoticzoo.com/" title="Visit author homepage">Hypnotic Zoo</a> | <a href="http://www.hypnoticzoo.com/freebies/wordpress/barebones-rss/?utm_source=wordpress&utm_medium=plugin&utm_content=v1&utm_campaign=wordpress" title="Visit plugin site">Visit plugin site</a>
Version: 1.0
*/

remove_action('wp_head','feed_links',2);
remove_action('wp_head','feed_links_extra',3);
add_action('wp_head', 'insert_rss');


if ( is_admin() ){
	add_action('admin_menu', 'feedburner');
	function feedburner() {
		add_menu_page( 'RSS URL', 'RSS URL', 'administrator', 'RSS URL' , 'feedburner_url','','10');
	}
}

if(get_option('barebones_feedburner_url')==''){
	add_option('barebones_feedburner_url',$value = '', $deprecated = '', $autoload = 'yes');
}

function insert_rss(){
	$rss_admin = get_option( 'barebones_feedburner_url' );
	if (!is_array($rss_admin)){
		$rss_admin =  unserialize($rss_admin);
	}
	$head_rss = '';
	foreach($rss_admin as $key => $rss){
		$head_rss .= '<link rel="alternate" type="application/rss+xml" title="'.$rss['rss_name'].'" href="'.$rss['rss_url'].'">';
	}
	echo ($head_rss);
}

function feedburner_url() {
	if (isset($_POST['rss_url'])) {
		$rss = $_POST['rss_url'];
		$rss_name = $_POST['rss_name'];
		$rss_remove = $_POST['rss_remove'];
		$rss_array = array();
		foreach($rss as $key => $r){
			if($r!=''&&$rss_name[$key]!=''&&(!is_array($rss_remove)||!in_array($key,$rss_remove))){
				array_push($rss_array,array('rss_name'=>$rss_name[$key],'rss_url'=>$r));
			}
		}
		update_option( 'barebones_feedburner_url', serialize($rss_array) );
	}
	$rss_admin = get_option( 'barebones_feedburner_url' );
	if (!is_array($rss_admin)){
		$rss_admin =  unserialize($rss_admin);
	}
	$form = '
		<h3>RSS URL</h3>
		<P>Enter your preferred RSS URL. (Feedburner or other)</P>
		<table>
		<form name="form1" method="post">';
		if(count($rss_admin)!=0){
		$form .= '	<tr>
				<th>
					Title
				</th>
				<th>
					URL
				</th>
				<th>
					Remove
				</th>
			</tr>';
		
		foreach($rss_admin as $k => $rss_item){
			$form .= '
				<tr>
					<td>
						<input type="text" size="15" name="rss_name[]" value='.$rss_item['rss_name'].'>
					</td>
					<td>
						<input type="text" size="50" name="rss_url[]" value='.$rss_item['rss_url'].'>
					</td>
					<td>
						<input type="checkbox" name="rss_remove[]" value = '.$k.'>
					</td>
				</tr>';
		}
	}
	
	$form .='
		<tr>
			<td><h4>Add new RSS</h4><td>
		</tr>
		<tr>
			<td>
				<label>Title</label>
				<input type="text" size="15" name="rss_name[]" value="">
			</td>
			<td>
				<label>URL</label>
				<input type="text" size="50" name="rss_url[]" value="">
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="button" id="button" value="Submit">
			</td>
		</tr>
		</form>
		</table>';
		
	echo $form;
}

?>