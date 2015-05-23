<?php

global $wpdb;
$table_name = $wpdb->prefix . "instalivit";
$instadetail_url = get_permalink(get_option('instadetail_page_id'));

if($_GET['func'] == 'topComments' && $_GET['ajax']=='true') {
	if ($_GET['img_id'] != '') {
		$img_id = $_GET["img_id"];
		wp_send_json($wpdb->get_results("SELECT * FROM $table_name WHERE image_id='$img_id' ORDER BY time_stamp DESC LIMIT 0,3", ARRAY_A));
		exit;
	}
}

if($_GET['func'] == 'submitComments' && $_GET['ajax']=='true') {
	if ($_POST) {
		return $wpdb->insert($table_name, $_POST);
		exit;
	}
}

$comment_data = array();

if(isset($_GET['id']) && $_GET['id']!='') {

	$img_id = $_GET["id"];
	$comment_data = $wpdb->get_results("SELECT * FROM $table_name WHERE image_id='$img_id' ORDER BY time_stamp DESC", ARRAY_A);

} else {
	die('No Parameter Supplied');
}

$client_id = '';

if (get_option('instagram_client_id')) {
	$client_id = get_option('instagram_client_id');
} else {
	die('Please enter the Instagram Client ID in the plugin setting page in the admin section.');
}


$get_img = wp_remote_get("https://api.instagram.com/v1/media/" . $_GET["id"] . "?client_id=" . $client_id, array(
		'sslverify'	=> false,
		'timeout' => 30
	));

$media_data = array();

if($get_img) {

	$media_feed = json_decode($get_img["body"], 1);
	//echo "<pre>";
	//print_r($media_feed);
	if ($media_feed['data']['type'] == 'video') {
		$media_data = array(
			'url' => $media_feed['data']['videos']['standard_resolution']['url'],
			'desc' => $media_feed['data']['caption']['text'],
			'type' => 'video'
		);

	} elseif ($media_feed['data']['type'] == 'image') {
		$media_data = array(
			'url' => $media_feed['data']['images']['standard_resolution']['url'],
			'desc' => $media_feed['data']['caption']['text'],
			'type' => 'image'
		);
	}

} else {
	echo "Unable to retreive data for id". $_GET['id'];
}

?>
<script type="text/javascript">
	var instadetail_url = "<?php echo $instadetail_url; ?>";
</script>

<div class="page-wrap">

	<div class="img_details">
		<?php if ($media_data['type'] == 'video') {?>
			<video width="100%" controls>
			  <source src="<?php echo $media_data['url']; ?>" type="video/mp4">
			Your browser does not support the video tag.
			</video>
		<?php } elseif ($media_data['type'] == 'image') {?>
			<img width="100%" src="<?php echo $media_data['url']; ?>" alt="Yahoo! Hack 2012 India" />
		<?php }?>
    	
    </div>
    <div>
    	<p><?php echo $media_data['desc']; ?></p>
    </div>

    <div class="comment-section">

    	<div class="comments" id="list-comments">
			<p style="margin-bottom: 0;">Comments</p>
			<?php if(count($comment_data) > 0) { foreach ($comment_data as $key => $value) {
				$width = round($value['rating']/5*100);
				echo '<div id="comment"><span class="com-name"><b>'.$value['name'].'</b></span><div class="rating_bar"><div class="rating" style="width:'.$width.'%;"></div></div><p class="com-comment">'.$value['comment'].'</p></div>';
			}} else { echo '<div id="comment"><span class="com-name">No comments yet, be the first one to comment.</span><div></div><p></p></div>';} ?>
		</div>
		
		<div>
			<hr style="margin-bottom: 5px;"/>
			<p style="margin-bottom: 10px;">Rate and Comment</p>
		</div>
		<div class="add_comments">
			<form name="save_comment" id="save_comment" method="post" novalidate="novalidate">
				<p class="comment">Rate this image
					<span style="padding:0px;" id="rateYo"></span>
				</p>
				<div style="display: none;">
					<input name="image_id" id="image_id" value="<?php echo $_GET['id']; ?>" type="hidden">
					<input name="rating" id="rating" value="" type="hidden">
				</div>
				<p class="comment">Name<br/>
				    <span class=""><input name="name" id="name" value="" style="padding: 2px; width:100%; margin-left: 0px; margin-top: 0;" class="" type="text"></span> 
				</p>
				<p class="comment">Comments<br/>
				    <span class=""><textarea name="comment" id="user-comment" style="padding: 2px; width:100%; margin-left: 0px; margin-top: 0;" rows="2" class="" ></textarea></span> 
				</p class="comment">
				<p><input value="Submit" style="padding: 5px; font-size:14px; margin-left: 5px; margin-top: 0;" class="" type="submit">
				</p>
			</form>
		</div>

    </div>

</div>