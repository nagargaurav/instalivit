<?php

$client_id = get_option('instagram_client_id');
$access_token = get_option('instagram_access_token');

if ($_POST['submit'] == 'submit' && $_POST['client_id'] != '') {
	delete_option('instagram_client_id');
	add_option('instagram_client_id', $_POST['client_id']);
	$client_id = get_option('instagram_client_id');
}

if ($_POST['submit'] == 'submit' && $_POST['access_token'] != '') {
	delete_option('instagram_access_token');
	add_option('instagram_access_token', $_POST['access_token']);
	$access_token = get_option('instagram_access_token');
}

if (isset($_GET['code']) && $_GET['code'] != '') {
	delete_option('instagram_access_token');
	add_option('instagram_access_token', $_GET['code']);
	$access_token = get_option('instagram_access_token');
}

?>

<br/>
<h1>Instalivit Settings</h1>

<form id="form" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">

<!-- Text input-->
<div class="control-group">
  <label class="control-label" style="width:30%" for="client_id"><b>Instagram Client ID: </b></label>
    <input id="client_id" name="client_id" type="text" placeholder="Please Enter Client ID" style="width:50%" class="input-large" required="" value="<?php echo $client_id; ?>">
    <p class="help-block">You can get your Instagram Client ID from - <a target="_blank" href="https://instagram.com/developer">Instagram Developer Page</a>
    	<br/>
    	Enter <strong><?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page=instalivit/instalivit.php'; ?></strong> in the Redirect URI field.
	</p>
</div>

<div class="control-group">
  <label class="control-label" style="width:30%" for="access_token"><b>Instagram Access Token: </b></label>
    <input id="access_token" name="access_token" type="text" placeholder="Please Enter Access Token" style="width:50%" class="input-large" required="" value="<?php echo $access_token; ?>">
    <p class="help-block">
    	You can get an access token by clicking the link below, but please make sure to add your Client ID and redirect url before you click on it.
    	<br/>
    	<a href="https://instagram.com/oauth/authorize/?client_id=<?php echo $client_id; ?>&redirect_uri=http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']; ?>?page=instalivit/instalivit.php&response_type=code">Get Access Token</a>
    </p>
</div>

<!-- Button -->
<div class="control-group">
    <input id="submit" name="submit" value="Submit" type="submit" class="button button-primary">
</div>

</form>