<?php

	// login form submitted
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
	{
	    $username = trim($_POST['username']);
        $isEmail = strpos($username,'@');
        if ($isEmail === false) {
            $username = filter_var( $username, FILTER_SANITIZE_STRING );
        } else {
            $username = filter_var( $username, FILTER_SANITIZE_EMAIL );
        }

		$password = $_POST['password'];
		$remember = $_POST['rememberMe'] ? true : false;

		$login = $auth->login( $username, $password, $remember );

		if ( $login['error'] )
		{
			flash()->error( $login['message'] );
		}
		else
		{
			do_login( $login );

			flash()->success('Successfully logged dovnitř!');
			redirect('/');      //DŮLEŽITÉ, aby se cookie zapsala!
		}
	}

	$page_title = 'bLoG / loGin';
	include_once "_partials/header.php";

?>

	<form method="post" action="" class="box box-auth">
		<h2 class="box-auth-heading">
			Login, please
		</h2>

		<input type="text" value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>" class="form-control" name="username"
               placeholder="Nickname or Email Address" required autofocus>
		<input type="password" class="form-control" name="password" placeholder="Password" required>
		<label class="checkbox">
			<input type="checkbox" value="remember-me" id="rememberMe" name="rememberMe" checked>
			Remember me
		</label>

		<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
		<p class="alt-action text-center">
			or <a href="<?= BASE_URL ?>/register">create acount</a>
		</p>
        <div class="row  forgot-pass">
            <p class="alt-action ">
                <a class="pull-left"    href="<?= BASE_URL ?>/reactivate">rEActivate account?</a>
                <a class="pull-right" href="<?= BASE_URL ?>/reset-password">Forgot your Password?</a>
            </p>
        </div>
	</form>

<?php include_once "_partials/footer.php" ?>