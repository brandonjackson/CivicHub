<?php 
function updateHeader()
{
	global $user_ID, $current_user;
	get_currentuserinfo();
	?>
	<div id="iRToppanel"> 
<?php 
	global $user_identity, $user_ID;	
	// If user is logged in or registered, show dashboard links in panel
	if (is_user_logged_in()) { 
?>
	<div id="iRPanel">
		<div class="content clearfix">
			
            <div class="left border">
			<img src="<?php bloginfo('wpurl') ?>/wp-content/plugins/buddypress-sliding-login-panel/images/logo.png"  alt="Logo" />
				<h2>Welcome back, <?php echo ucwords($user_identity) ?>!</h2>				
				<h2 style="border-top:1px dotted #fff;">My Messages</h2>
	<div class="msgs">			
	<?php if ( bp_has_message_threads('per_page=2') ) : ?>
 	<ul id="message-threads">
 		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>

    	<li<?php if ( bp_message_thread_has_unread() ) : ?> class="unread"<?php else: ?> class="read"<?php endif; ?>>
      
     		<div class="message-subject"> 
     			<?php bp_message_thread_avatar('type=full&width=35&height=35') ?>
      			<?php bp_message_thread_subject() ?>
    		 </div>
    
     		<div class="message-meta">
     			<p><a class="button view" title="View Message" href="<?php bp_message_thread_view_link() ?>">View Message</a> <a class="button view" title="Send Reply" href="<?php bp_message_thread_view_link() ?>/#send-reply">Reply</a></p>
   			</div>
    	</li>   
  		<?php endwhile; ?>
 
  	</ul>
	<?php else: ?>
 	<div>   
    <p class="msg"><img src="<?php bloginfo('wpurl') ?>/wp-content/plugins/buddypress-sliding-login-panel/images/msg.png"  alt="messages" class="msg" /> You have 0 new messages.</p>

  	</div>	
	<?php endif;?>
</div>				
				
                
			</div>
			
            <div class="left narrow">			
				<h2>My Avatar</h2>
				<a href="<?php echo bp_loggedin_user_domain() ?>profile">
			<?php bp_loggedin_user_avatar('width=117&height=117') ?>
			</a>
			
			<ul>
			<li>&nbsp; &nbsp; </li>
			<li><a id="avtext" href="<?php echo bp_loggedin_user_domain() . BP_XPROFILE_SLUG ?>/change-avatar">Change My Avatar &rarr; </a></li>
			</ul>
			</div>
		
			
            <div class="left narrow">			
				<h2>Profile</h2>				
				<ul>					
					<li><a href="<?php echo bp_loggedin_user_domain() . BP_XPROFILE_SLUG ?>">View My Profile</a></li>
					<li><a href="<?php echo bp_loggedin_user_domain() . BP_XPROFILE_SLUG ?>/edit">Edit My Profile</a></li>
						<li><a href="<?php echo bp_loggedin_user_domain() . BP_XPROFILE_SLUG ?>/change-avatar">Change My Avatar</a></li>
	        		<li><a href="<?php echo wp_logout_url(get_permalink()); ?>" rel="nofollow" title="<?php _e('Log out'); ?>"><?php _e('Log out'); ?></a></li>
				</ul>
				
				<h2>Activity</h2>				
				<ul>						
					<li><a href="<?php echo bp_loggedin_user_domain() . BP_ACTIVITY_SLUG ?>">Update My Status</a></li>
					<li><a href="/<?php echo BP_ACTIVITY_SLUG ?>">Sitewide Activity</a></li>
				
				</ul>
				
			
			</div>
		
			
			
            <div class="left narrow">
				<h2>Mentions</h2>				
			
<a href="<?php echo bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/mentions/' ?>" title="<?php _e( 'Activity that I have been mentioned in.', 'buddypress' ) ?>"><?php printf( __( '@%s Mentions', 'buddypress' ), bp_get_loggedin_user_username() ) ?></a>
					
				<h2>Groups</h2>	
				<ul>
					<li><a href="<?php echo bp_loggedin_user_domain() . BP_GROUPS_SLUG ?>">My Groups</a></li>
					<li><a href="/groups">Join Groups</a></li>
					<li><a href="<?php echo bp_loggedin_user_domain() . BP_GROUPS_SLUG ?>/create">Create a Group</a></li>
				</ul>
			
				
			</div>
			
	
			
            <div class="left narrow">			
				
 
<h2>Friends</h2>				
				<ul>		
					<li><a href="<?php echo bp_loggedin_user_domain() . BP_FRIENDS_SLUG ?>">My Friends</a></li>
					<li><a href="/<?php echo BP_MEMBERS_SLUG ?>">Meet Friends</a></li>
				</ul> 
 <h2>Friend Requests</h2>
<?php if ( bp_has_members( 'include=' . bp_get_friendship_requests() . '&per_page=1' ) ) : ?>

	<ul id="friend-list" class="item-list">
		<?php while ( bp_members() ) : bp_the_member(); ?>
		
				<div>
				<p><a href="<?php bp_member_link() ?>"><?php bp_member_name() ?></a></p>
				<p>	<a href="<?php bp_member_link() ?>"><?php bp_member_avatar() ?></a></p>
				</div>


				<?php do_action( 'bp_friend_requests_item' ) ?>

				<div class="action" style="float: right; padding: 4px;">
					<a class="accept" href="<?php bp_friend_accept_request_link() ?>"><?php _e( 'Accept', 'buddypress' ); ?></a><br/>
					<a class="reject" href="<?php bp_friend_reject_request_link() ?>"><?php _e( 'Reject', 'buddypress' ); ?></a>

					<?php do_action( 'bp_friend_requests_item_action' ) ?>
						<p><a id="whitetext" href="<?php echo bp_loggedin_user_domain() . BP_FRIENDS_SLUG ?>/requests">More &rarr; </a></p>
				</div>
			

		<?php endwhile; ?>
	
	</ul>

	<?php do_action( 'bp_friend_requests_content' ) ?>


<?php else: ?>

	<div>
		<p><?php _e( 'You have no pending friendship requests.', 'buddypress' ); ?></p>
	</div>

<?php endif;?>

			
			</div>
			
	
		</div>
         
	</div> <!-- /login -->	

    <!-- The tab on top -->	
	<div class="tab">
		<ul class="login" style="margin-right:-10%;">
	    	<li class="left">&nbsp;</li>
	    	<!-- Logout -->
	        <li><a class="close" style="width:50px;" href="<?php echo wp_logout_url(get_permalink()); ?>" rel="nofollow" title="<?php _e('Log out'); ?>"><?php _e('Log out'); ?></a></li>
			<li class="sep">|</li>
			<li id="toggle">
				<a id="open" class="open" href="#">My Account</a>
				<a id="close" style="display: none;" class="close" href="#">Close Panel</a>	
			</li>
	    	<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- / top -->

<?php 
	// Else if user is not logged in, show login and register forms
	} else {	
?>
	<div id="iRPanel">
		<div class="content clearfix">
			
            <div class="left border" style="width:250px;">
				<img src="<?php bloginfo('wpurl') ?>/wp-content/plugins/buddypress-sliding-login-panel/images/logo.png"  alt="Logo" />
				<h2>Welcome to <? bloginfo('name'); ?></h2>		
				<p>Login or Signup to meet new friends, find out what's going on, and connect with others on the site. </p><br/>
				
			</div>
			     
			
            <div class="left" style="width:195px;">
						<?php if (get_option('users_can_register')) : ?>	
				<!-- Register Form -->
				<form name="registerform" id="registerform" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>" method="post">
					<h2>Sign Up Now</h2>	
					Registering for this site is easy. Just fill in the fields on the registration page and we'll get a new account set up for you in no time. <br/>
					<input type="submit" name="wp-submit" id="wp-submit" value="<?php _e('Register'); ?>" class="bt_register" />
			     </form>
			<?php else : ?>
				<h1>Registration is closed</h1>
				<p>Sorry, you are not allowed to register by yourself on this site!</p>
				<p>You must either be invited by one of our team member or request an invitation by email.</b>.</p>
				
				<!-- Admin, delete text below later when you are done with configuring this panel -->
				<p style="border-top:1px solid #333;border-bottom:1px solid #333;padding:10px 0;margin-top:10px;color:white"><em>Note: If you are the admin and want to display the register form here, log in to your dashboard, and go to <b>Settings</b> > <b>General</b> and click "Anyone can register".</em></p>
			<?php endif ?>			
			</div>
            <div class="left right" style="width:195px;">
            <form class="clearfix" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>" method="post">
					<h2>Forgot Your Password?</h2>
					<label class="grey" for="user_login">Username or E-mail:</label>
					<input class="field" type="text" name="user_login" id="user_login_FP" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="23" />        			
                    <div class="clear"></div>
                     <p>A new password will be e-mailed to you.</p>
					<input type="submit" name="submit" value="Retrieve" class="bt_register" />
					<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
			</form>
            </div>
             
			<div class="left right" style="width:195px;">
				<!-- Login Form -->
				<form class="clearfix" action="<?php bloginfo('wpurl') ?>/wp-login.php" method="post">
					<h2>Member Login</h2>
					<label class="grey" for="log">Username:</label>
					<input class="field" type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="23" />
					<label class="grey" for="pwd">Password:</label>
					<input class="field" type="password" name="pwd" id="pwd" size="23" />
	            	<label><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me</label>
        			<div class="clear"></div>
					<input type="submit" name="submit" value="Login" class="bt_login" />
					<input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
				</form>
			</div>			 
		</div>
	</div> <!-- /login -->	

    <!-- The tab on top -->	
	<div class="tab">
		<ul class="login" style="margin-right:-10%;">
	    	<li class="left">&nbsp;</li>
	    	<!-- Login / Register -->
			<li id="toggle">
				<a id="open" class="open" href="#">Log In</a>
				<a id="close" style="display: none;" class="close" href="#">Close Panel</a>			
			</li>
	    	<li class="right">&nbsp;</li>
		</ul> 
	</div> <!-- / top -->	
    		
<?php } ?>	

</div> <!--END panel -->	

<!-- End of login page -->

<?php
}
?>