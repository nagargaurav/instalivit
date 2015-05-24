<?php

$client_id = get_option('instagram_client_id');

if ($_POST['submit'] == 'submit' && $_POST['client_id'] != '') {
	delete_option('instagram_client_id');
	add_option('instagram_client_id', $_POST['client_id']);
	$client_id = get_option('instagram_client_id');
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
	</p>
</div>

<!-- Button -->
<div class="control-group">
    <input id="submit" name="submit" value="Submit" type="submit" class="button button-primary">
</div>

</form>
