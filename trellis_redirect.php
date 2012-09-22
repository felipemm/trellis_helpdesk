<?php

define('WP_USE_THEMES', false);
require('../../../wp-blog-header.php'); 

?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <title>Redirecionando para o helpdesk...</title>
        <script type="text/javascript" charset="utf-8" src="http://code.jquery.com/jquery-1.8.1.min.js"></script>
    <script>
      $(document).ready(function() {
        // do stuff when DOM is ready


        //$("#submit").click();
        

        dataString = $("input#postdata").val();
        //alert(dataString);
        $.ajax({  
			type: "POST",  
			url: "http://wooplugins.com.br/helpdesk/index.php?act=login",  
			data: dataString,  
			success: function() {  
				window.location = "http://wooplugins.com.br/helpdesk";
			}  
        });
      });
    </script>
    </head>
    <body>
    <?php
        //se houver um usuário logado no wordpress
        if (is_user_logged_in()) { 
          
			//pegue as informações do usuário;
			$current_user = wp_get_current_user();
			//se as informações obtidas são do tipo usuário do wordpress
			if ($current_user instanceof WP_User){
				$data = array(
					'extra_I' => '',
					'remember' => '1',
					'username' => $current_user->user_login,
					'password' => $current_user->user_pass,
					'submit' => 'Log In'
				);

				//cria o formulário post
				/*
				echo   '<form action="http://wooplugins.com.br/helpdesk/index.php?act=login" method="post" id="login">
							<input type="hidden" id="extra_I"  name="extra_I"  value="" /> 
							<input type="hidden" id="remember" name="remember" value="1" /> 
							<input type="hidden" id="username" name="username" value="'.$current_user->user_login.'" /> 
							<input type="hidden" id="password" name="password" value="'.$current_user->user_pass.'" /> 
							<input type="submit" id="submit"   name="submit"   value="Log In" /> 
						</form>';
				*/
				echo '<input type="hidden" id="postdata" name="postdata" value="'.http_build_query($data).'" /> ';
			}
        } else {
			header("Location: http://wooplugins.com.br/minha-conta/");
		}
    ?>
    </body>
</html>