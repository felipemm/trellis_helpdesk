<?php
/*
Plugin Name: Trellis HelpDesk Automator
Plugin URI: http://feliepmatos.com/loja
Description: Create an user to trellis whenever a new wordpress user is created.
Version: 1.0
Author: Felipe Matos Moreira
Author URI: http://felipematos.com
License: GPL2
*/

//hook to include the payment gateway function
add_action('user_register', 'trellis_helpdesk_register');
//hook function
function trellis_helpdesk_register($user_id){
	global $wpdb;
	$myrows = $wpdb->get_row("SELECT * FROM $wpdb->users where id = $user_id");

	//criar a requisição CURL para registrar o usuário
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://wooplugins.com.br/helpdesk/index.php?act=register&code=new");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	//cria uma senha temporária, que será resetada depois de criado o usuário
	//$temp_password = md5(microtime());
	//cria informações que serão enviadas via post
	$data = array(
		 'email'  => $myrows->user_email
		,'pass'   => $myrows->user_pass
		,'passb'  => $myrows->user_pass
		,'submit' => 'Criar conta'
		,'user'   => $myrows ->user_login
	);
	//envia uam requisição post
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	$output = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);

}



//hook to wordpress on a profile change, update the password in trellis
//beware that the password send to trellis is not the ORIGINAL password from wordpress
//we get the password after md5 hash stored in the database, and use this as password to trellis.
//users will not be able to login to trellis without a redirection login
add_action( 'profile_update', 'trellis_helpdesk_change_password' );
//hook function
function trellis_helpdesk_change_password( $user_id, $old_user_data ) {
	global $wpdb;
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users where id = $user_id");
	$user_trellis = $wpdb->get_row("SELECT * FROM td_members where name = '$user->user_login'");
	
	$wpdb->update( 
		'td_members', 
		array('password' => sha1( md5( $user->user_pass . $user_trellis->pass_salt ) ),
		      'login_key' => str_replace( "=", "", base64_encode( strrev( crypt( $user->user_pass ) ) ) )), 
		array( 'name' => $user->user_login ), 
		array( '%s'), 
		array( '%s') 
	);

}


/*
//wordpress hook to login to trellis everytime the user logs in wordpress
add_action('wp_login', 'trellis_helpdesk_login', 10, 2);
//hook function
function trellis_helpdesk_login($user_login, $user) {
	//se houver um usuário logado no wordpress
	if (is_user_logged_in()) { 
		
		//pegue as informações do usuário;
		$current_user = wp_get_current_user();
		//se as informações obtidas são do tipo usuário do wordpress
		if ($current_user instanceof WP_User){

			wp_enqueue_script('jquery');
			//cria o formulário post
			echo   '<form action="http://wooplugins.com.br/helpdesk/index.php?act=login" method="post" id="login">
						<input type="hidden" id="extra_I"  name="extra_I"  value="" /> 
						<input type="hidden" id="remember" name="remember" value="1" /> 
						<input type="hidden" id="username" name="username" value="'.$current_user->user_login.'" /> 
						<input type="hidden" id="password" name="password" value="'.$current_user->user_pass.'" /> 
						<input type="submit" id="submit"   name="submit"   value="Log In" /> 
					</form>';
			jQuery("#submit").click();
		}
	}
}
*/

?>