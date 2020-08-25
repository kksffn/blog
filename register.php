<?php

	// register form submitted
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
	{
		$nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_STRING);
	    $email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		$password = $_POST['password'];
		$password_repeat = $_POST['repeat'];

		$register = $auth->register( $nickname, $email, $password, $password_repeat, [], null, $config['use_email_activation'] );

		if ( $register['error'] ) {
			flash()->error( $register['message'] );
		}
		else {
			flash()->success($register['message']);
			redirect('/activate');
		}
	}
    $page_title = 'blOg / reGisteR';
	include_once "_partials/header.php";

?>

	<form method="post" action="" class="box box-auth">
		<h2 class="box-auth-heading">
			Register, please
		</h2>

        <input type="text" value="<?= isset($_POST['nickname']) ? $_POST['nickname'] : '' ?>"
               class="form-control" name="nickname" placeholder="Your Nickname" required autofocus>
        <input type="text" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>"
               class="form-control" name="email" placeholder="Email Address" required>
		<input type="password" class="form-control" name="password" placeholder="Password" required>
		<input type="password" class="form-control" name="repeat" placeholder="Password again, DO IT" required>
		<button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>

		<p class="alt-action text-center">
			or <a href="<?= BASE_URL ?>/login">login</a>
		</p>
	</form>

<?php include_once "_partials/footer.php" ?>