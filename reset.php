<?php
$page_title = 'bLoG / reset';
include_once "_partials/header.php";


// reset form submitted
if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
{
    $new_psw = $_POST['new-password'];
    $new_psw_again = $_POST['new-password-again'];
    $reset_key = $_POST['reset-key'];

    $user = getUserFromToken($reset_key);

    $reset = $auth->resetPass($reset_key, $new_psw, $new_psw_again);
    if ( $reset['error'] ) {
        flash()->error( $reset['message'] );
        redirect('back');
    }
    else {

        $return = sendNewLoginData($user, $new_psw);
        if ($return['error']){
            flash()->success($reset['message']);
            flash()->error($return['message']);
        }else {
            flash()->success($reset['message']." Nové přihlašovací údaje byly poslány na váš email.");
        }
        redirect('/');
    }
}
?>
    <form method="post" action="" class="box box-auth">
        <div class="text-center">
            <h2 class="box-auth-heading">
                Change your password
            </h2>
        </div>
        <input type="text" value="" class="form-control" name="reset-key" placeholder="Reset key from email" required autofocus>

        <input type="password" value="" class="form-control" name="new-password" placeholder="New Password" required>
        <input type="password" value="" class="form-control" name="new-password-again" placeholder="New Password again" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Reset psw</button>

        <div class="alt-action text-center">
             or
            <a class="" href="<?=BASE_URL?>">Cancel</a>
        </div>
    </form>

<?php include_once "_partials/footer.php" ?>