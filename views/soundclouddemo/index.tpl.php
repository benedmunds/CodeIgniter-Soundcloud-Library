<h1>SoundCloud Demo</h1>

    <div id="wrapper">
        <div id="content">	
	        <?php if (isset($message)): ?>
	        	<div id="message">
	        		<p><?php echo $message; ?></p>
	        	</div>
	        <?php endif; ?>
	                
	        <?php if (isset($me) && !empty($me)): ?>
	        	<a class="logout" href="<?php echo site_url();?>/soundclouddemo/logout">logout</a>
	        <?php endif; ?>
	            
	        <?php if (isset($login)): ?>
	        	<?php $this->load->view('soundclouddemo/login.tpl.php');?>
	        <?php elseif (isset($me) && !empty($me)): ?>
		        <h2>Your profile</h2>
		        
		        <div id="profile">
			        <div class="left">
				        <p>
					        <img src="<?php echo $me['avatar-url']; ?>" width="75" height="75" alt="" />
					        <div class="avatar"></div>
				        </p>
			        </div>
			        <div class="right">
			        	<h2>
			        		<a href="<?php echo $me['permalink-url']; ?>"><?php echo $me['permalink']; ?></a>
			        	</h2>
			        	<p><?php echo $me['full-name'] ?>, <?php echo $me['city']; ?>, <?php echo $me['country']; ?></p>
			        	<p>You have <?php echo $me['track-count']; ?> <?php echo ($me['track-count'] == 1) ? 'track' : 'tracks'; ?>.</p>
			        </div>
			        <div class="clear"></div>
		        </div>
		                
		        <!-- load the upload form view -->
		        <?php $this->load->view('soundclouddemo/upload.tpl.php');?>
	        <?php endif; ?>
        </div>
    </div>

