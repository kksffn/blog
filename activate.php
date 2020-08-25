<?php

// register form submitted
if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
{
    $activation_key = filter_input(INPUT_POST, 'activation_key', FILTER_SANITIZE_STRING);
    $activate = $auth->activate( $activation_key );

    if ( $activate['error'] ) {
        flash()->error( $activate['message'] );
    }
    else {
        flash()->success($activate['message']);
        redirect('/login');
    }
}

$page_title = 'bLoG / activAtion';

include_once "_partials/header.php";

?>

    <form method="post" action="" class="box box-auth">
        <div class="text-center">
            <h2 class="box-auth-heading">
                Your key, please
            </h2>
        </div>

        <input type="text" value="" class="form-control" name="activation_key" placeholder="activation_key" required autofocus>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Activate</button>
        <div class="row  forgot-pass">
            <a class="col-md-10 pull-left " href="<?=BASE_URL.'/reactivate'?>">Resend activation_key</a>
            <p > or </p>
        </div>
            <a class="pull-right" href="<?=BASE_URL.'/login'?>">Cancel</a>
    </form>

<?php include_once "_partials/footer.php" ?>