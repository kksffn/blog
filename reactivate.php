<?php

// activation form submitted
if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
{
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $resend = $auth->resendActivation( $email, $config['use_email_activation'] );

    if ( $resend['error'] ) {
        flash()->error( $resend['message'] );
        redirect('reactivate');
    }
    else {
        flash()->success('Čekni email a máš pokus pro vložení activation key!');
        redirect('/activate');
    }
}

$page_title = 'bLoG / rEactivAtion';

include_once "_partials/header.php";

?>

    <form method="post" action="" class="box box-auth">
        <div class="text-center">
            <h2 class="box-auth-heading">
                Your email, please
            </h2>
        <p class="">Activation key will be sent to you.</p>
        </div>
        <input type="text" value="" class="form-control" name="email" placeholder="type your email here" required autofocus>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Send</button>
        <div class="alt-action text-center ">
            <p class="alt-action ">
               or <a class="" href="<?= BASE_URL ?>">cancel</a>
            </p>
        </div>
    </form>

<?php include_once "_partials/footer.php" ?>