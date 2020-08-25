<?php

    // include
    require '../_inc/config.php';
    include_once "../_partials/header.php";


    // just to be safe - tady nemáš co dělat
    if ( ! logged_in() ) {
        redirect('/');
    }

    //only admin has rights to be here
    if (!$is_admin) {
        flash()->error("What are you trying to do?!?!?!?!");
        redirect('/');
    }

    //id has to be int
    $user_id = filter_input (INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    //Controls if it exists
    $user = get_user($user_id);


    if ( ! $user_id  || !$user )
    {
        flash() -> error("What are you trying to do?!?!?!?!");
        redirect('back');
    }
    if ($user->rights == 'admin' && get_number_of_admins() == 1)
    {
        flash()->error("You can't delete admin $user->nickname. At least one admin has to remain...");
        redirect(get_admins_link($logged_in, 'users'));
    }

    //Change author of posts of beeing deleted user
    if ($_POST['number_of_posts'] !='no'){
        $change = change_author_of_the_posts_of($user->id);
        if (!$change) {
            flash()->error("Chyba v Matrixu, něco went wrong...");
            redirect('back');
        }
    }

    //Change author of comments of beeing deleted user
    if ($_POST['number_of_comments'] !='no'){
        $change = change_author_of_the_comments_of($user->id);
        if (!$change) {
            flash()->error("Chyba v Matrixu, něco went wrong...");
            redirect('back');
        }
    }

    //Delete user
    $delete = $auth->deleteUserForced($user->id);


    //redirect to users page with flash messages
    if($delete['error'])
    {
        flash() ->error($delete['message']);

    }else {
        flash()->success($delete['message']);
    }

    $link = get_admins_link($logged_in, 'users');
    redirect($link);


