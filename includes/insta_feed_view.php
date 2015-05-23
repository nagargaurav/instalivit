<?php

$instadetail_url = get_permalink(get_option('instadetail_page_id'));
$client_id = '';

if (get_option('instagram_client_id')) {
	$client_id = get_option('instagram_client_id');
} else {
	die('Please enter the Instagram Client ID in the plugin setting page in the admin section.');
}

$user_ids = array();
$insta_image_data = array();

if (count($agrs_users) == 0 && count($agrs_tags) == 0) {
	die("No Arguments supplied");
}


if(count($agrs_users > 0)) {

	foreach ($agrs_users as $username) {

		$get_user_id = wp_remote_get("https://api.instagram.com/v1/users/search?q=".$username."&client_id=".$client_id , array(
			'sslverify'	=> false,
			'timeout' => 30
		));
		
		if (is_array($get_user_id)) {
			$user_data = json_decode($get_user_id["body"], 1);

			if (isset($user_data['meta']) && isset($user_data['meta']['error_message'])) {
				echo $user_data['meta']['error_message'];
				continue;
			}

			foreach ($user_data['data'] as $data) {

				if ($data['username'] == $username) {
					array_push($user_ids, $data['id']);
					break;
				}
			}

		} else {
			echo "Problem in connection with Instagram";
		}
	}

}

foreach ($user_ids as $user_id) {
	
	$feed_data = wp_remote_get("https://api.instagram.com/v1/users/".$user_id."/media/recent?&client_id=".$client_id, array(
			'sslverify'	=> false,
			'timeout' => 30
		));

	if (is_array($feed_data)){

		$feed_body = json_decode($feed_data["body"], 1);

		if (isset($feed_body['meta']) && isset($feed_body['meta']['error_message'])) {
			echo $feed_body['meta']['error_message'];
			continue;
		}

		foreach($feed_body['data'] as $value) {

			if(count($agrs_tags) > 0) {

				$flag = 0;

				foreach ($agrs_tags as $tag){

					foreach ($value['tags'] as $feed_tag){
						if (strtolower($feed_tag) == strtolower($tag)){
							$flag = 1;
							break;
						}
					}

					if ($flag == 1) { break; }

				}
				
				if ($flag == 0) { continue; }
			}

			$feed = array();

			$feed['id'] = $value['id'];
			$feed['img_thumb'] = $value['images']['low_resolution']['url'];
			$feed['img_url'] = $value['images']['standard_resolution']['url'];
			$feed['likes'] = $value['likes']['count'];
			$feed['tags'] = $value['tags'];

			array_push($insta_image_data, $feed);

		}
	} else {
		echo "Problem in connection with Instagram";
	}
}

if (count($user_ids) == 0 && count($agrs_tags) > 0) {
	
	foreach ($agrs_tags as $tag) {

		$feed_data = wp_remote_get("https://api.instagram.com/v1/tags/".strtolower($tag)."/media/recent?client_id=".$client_id, array(
			'sslverify'	=> false,
			'timeout' => 30
		));
		
		if (is_array($feed_data)){

			$feed_body = json_decode($feed_data["body"], 1);

			if (isset($feed_body['meta']) && isset($feed_body['meta']['error_message'])) {
				echo $feed_body['meta']['error_message'];
				continue;
			}
			
			foreach($feed_body['data'] as $value) {
				$feed = array();

				$feed['id'] = $value['id'];
				$feed['img_thumb'] = $value['images']['low_resolution']['url'];
				$feed['img_url'] = $value['images']['standard_resolution']['url'];
				$feed['likes'] = $value['likes']['count'];
				$feed['tags'] = $value['tags'];

				array_push($insta_image_data, $feed);
			}	
		} else {
			echo "Problem in connection with Instagram";
		}
	}
}

?>

<script type="text/javascript">
	var instadetail_url = "<?php echo $instadetail_url; ?>";
</script>


<div id="container">
    <!-- start your query before the .brick element -->
    <?php if(count($insta_image_data) == 0) { echo "No Data found."; } foreach ($insta_image_data as $key => $value) { ?>
    <div class="brick" id="<?php echo $value['id']; ?>">
        <!-- Post Content -->
        <a class="box" id="<?php echo $value['id']; ?>">
        	<img class="feed-img" src="<?php echo $value['img_thumb']; ?>" alt="" />
        </a>
    </div>
    <?php } ?>
    <!-- end query-->
</div>


<div id="pop_up">
	<span class="button b-close"><span>X</span></span>

	<div class="comments" id="list-comments">
		<p style="margin-bottom: 0;">Comments</p>
	</div>
	<p style="margin-bottom: 0;" class="comments"><a class="comments" id="detail-link" href="">Read More</a></p>
	<div>
		<hr style="margin-bottom: 5px;"/>
		<p style="margin-bottom: 10px;">Rate and Comment</p>
	</div>
	<div class="add_comments">
		<form name="save_comment_ajax" id="save_comment" method="post" novalidate="novalidate">
			<p class="comment">Rate this image
				<span style="padding:0px;" id="rateYo"></span>
			</p>
			<div style="display: none;">
				<input name="image_id" id="image_id" value="" type="hidden">
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