<?php

    $user = get_user( get_segment(2) );
    $page_title = plain($user->nickname) . "'s proFiLe";
    include_once "_partials/header.php";

    // just to be safe - tady nemáš co dělat
    if ( ! logged_in() ) {
        redirect('/');
    }

    //user can edit just his/her info
    if ($logged_in->uid != get_segment(2)) {
        flash()->error("What are you trying to do???????!");
        redirect('/');
    }



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // c'mon baby do the locomo.. validation
        if ($data = validate_user_info() ) {
            // Update user info in DB
            $update = $auth->update_user($logged_in->uid, $data );

            if ( $update['error'] )
            {
                flash()->error( "<strong>Nezměněno!</strong> ".$update['message'] );
                //To refill the form
                create_session_user_data($_POST['user_nickname'], $_POST['user_email'],$_POST['about_me']);
            }
            else
            {
                flash()->success('<strong> Successfully změněno! </strong>'. 'Nickname: '. plain($update['nickname']). ', email: '
                    . plain($update['email']). ', O tobě: '.  word_limiter(plain($update['about_me']), 20));
            }
        }
        redirect('back');
    }

    //We get $nickname, $email, $about_me from session -> refill form
    if ( isset( $_SESSION['user_data'] ) )
    {
        extract( $_SESSION['user_data'] );
        unset( $_SESSION['user_data'] );
    }


?>
    <h1 class="page-header"><?=plain( $user->nickname )?> <small class="text-muted">(<?=plain( $user->email )?>)</small></h1>
    <form class="profile"  action="" method="post">
        <h2 class="box-auth-heading">
            You can change your info here
        </h2>
        <div class="row m-b-50">
            <div class="form-group col-md-5">

                <label class="profile-label is-required" for="user_nickname">Nickname</label>
                <input type="text" value="<?= isset($nickname) ? $nickname : plain( $user->nickname )?>"
                       class="form-control user_info profile-info"
                       name="user_nickname" id="user_nickname" placeholder="Nickname" required>

                <label class="profile-label is-required" for="user_email">Email</label>
                <input type="text" value="<?= isset($email) ? $email : plain( $user->email )?>"
                       class="form-control user_info profile-info"
                       name="user_email" id="user_email" placeholder="email address" required>
                <div class="form-group">
                    <label class="profile-label" for="user_rights">Rights</label>
                    <p id="user_rights" class="profile-info"><?= ucfirst($user->rights)?></p>
                </div>
                <div class="form-group">
                    <label class="profile-label " for="user_registered">Registered</label>
                    <p id="user_registered" class="profile-info"><time datetime="<?= date( 'Y-m-d',(strtotime ( $user->dt )))?>">
                            <?= date( 'j M Y',(strtotime ( $user->dt )))?></time></p>
                </div>
                <div class="form-group">
                    <label class="profile-label " for="user_status">Status</label>
                    <p id="user_status"  class="profile-info"><?= $user->isactive == 1 ? "Active" : "Inactive" ?></p>
                </div>
            </div>
            <div class="form-group about_me col-md-5">
                <label class="profile-label" for="about_me">O mně</label>
                <textarea class="form-control profile-info " name="about_me" id="about_me" rows="14"><?=
                    isset($about_me) ? $about_me : plain( $user->about_me )?></textarea>
            </div>
        </div>
        <div class=" btn-group btn-group-sm btn-profile-left pull-left">
            <a href="<?= BASE_URL . '/user/' . $logged_in->uid ?>" class="btn btn-default">YouR posTs</a>
        </div>

        <div class="col-md-10">
            <button class="btn btn-lg btn-primary profile-btn col-span" type="submit">Potvrď changes</button>
        </div>
    </form>

<?php include_once "_partials/footer.php" ?>