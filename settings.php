<?php

    $user = get_user( get_segment(2) );
    $page_title = plain($user->nickname) . "'s settings";
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
        if ($_POST['user_password'] == '' || $_POST['new_password'] == '') {
            flash()->message("C'mon, the weather is not so důležité as your password. 
            Everybody is talking about it but nobody does something with it.");
        } else {
            $update_psw = $auth->changePassword($logged_in->uid, $_POST['user_password'],
                $_POST['new_password'], $_POST['new_password']  );

            if ( $update_psw['error'] )
            {
                flash()->error( "<strong>Nezměněno!</strong> ".$update_psw['message'] );

            }
            else
            {
                flash()->success('<strong> Password successfully změněno! </strong>');
            }

        }
        redirect('back');
    }

?>
    <h1 class="page-header"><?=plain( $user->nickname )?> <small class="text-muted">(<?=plain( $user->email )?>)</small></h1>
    <form class="profile"  action="" method="post">
        <h2 class="box-auth-heading">
            You can change your settings here
        </h2>
        <div class="row m-b-50">
            <div class="form-group col-md-5">
                <h3>Change your password</h3>
                <label class="profile-label" for="user_pasword">Actual password</label>
                <input type="password" value=""
                       class="form-control user_info profile-info"
                       name="user_password" id="user_pasword" placeholder="Actual password">

                <label class="profile-label" for="new_password">New password</label>
                <input type="password" value=""
                       class="form-control user_info profile-info"
                       name="new_password" id="new_password" placeholder="New password">

            </div>
            <div class="form-group about_me col-md-5">
                <h3>Actual weather</h3>
                <label class="profile-label" for="actual_weather">What's the weather like in London?</label>
                <textarea class="form-control profile-info " name="actual_weather" id="actual_weather" rows="14">?</textarea>
            </div>
        </div>

        <div class="col-md-10">
            <button class="btn btn-lg btn-primary profile-btn col-span" type="submit">Potvrď changes</button>
        </div>
    </form>

<?php include_once "_partials/footer.php" ?>