<?php

if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
{
    $email = filter_var( $_POST['reset-pass'], FILTER_SANITIZE_EMAIL );
    $reset_request = $auth->requestReset( $email, true);

    if ( $reset_request['error'])
    {
        flash()->error( $reset_request['message'] );
    }
    else
    {
        flash()->success($reset_request['message']);
        redirect('/reset');
    }
}

include_once "_partials/header.php";

?>

    <form method="post" action="" class="box box-auth">
        <div class="text-center">
            <h2 class="box-auth-heading">
               Enter your email
            </h2>
            <p>Request key will be sent to you.</p>
        </div>

        <input type="text" value="<?= isset($_POST['reset']) ? $_POST['reset'] : '' ?>"
               class="form-control" name="reset-pass" placeholder="Your Email Address" required autofocus>

        <button class="btn btn-lg btn-primary btn-block" type="submit">Reset pswd</button>
        <p class="alt-action text-center">
            or <a href="<?= BASE_URL ?>/login">cancel</a>
        </p>
    </form>

<?php include_once "_partials/footer.php" ?>