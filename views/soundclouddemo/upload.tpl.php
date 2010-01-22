<h2>Upload a new track</h2>

<form action="<?php echo site_url() . '/soundclouddemo/upload/?oauth_token=' .$_GET['oauth_token'].'&oauth_verifier='.$_GET['oauth_verifier'];?>" method="post" enctype="multipart/form-data">
	<p>
		<label for="title">Track title</label>
		<input class="text" type="text" name="title" id="title" />
	</p>
	<p>
		<label for="file">File</label>
		<input type="file" name="userfile" class="file">
	</p>
	<p class="center">
		<input class="submit" type="submit" name="submit" value="Upload" id="submit" />
	</p>
</form>