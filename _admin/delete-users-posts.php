<?php

    // include
    require_once '_inc/config.php';
    include_once "_partials/header.php";



    // just to be safe - tady nemáš co dělat
    if ( ! logged_in() ) {
        redirect('/');
    }

    //only admin has rights to be here
    if (!$is_admin && !$is_editor) {
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


    //Delete posts of the $user
    $delete = deleteAllUsersPosts($user->id);

    //Delete tags for all posts deleted
    foreach ($_POST['post_id'] as $post_id) {
        delete_tags_for_post($post_id);
        delete_comments_for_the_post($post_id);
    }

    //redirect to user with flash messages
    if(!$delete)
    {
        flash() ->error("Something went špatně...");

    }else {
        flash()->success("All that great masterpieces were deleted!");
    }
    if ($is_admin){
        redirect(get_admins_link($logged_in, 'users'));
    }elseif ($is_editor){
        redirect(get_editors_link($logged_in, 'editor'));
    }




