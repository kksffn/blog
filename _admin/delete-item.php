<?php

    // include
    require '../_inc/config.php'; //o adresář výše, vejít do _inc, tam je config.php
    include_once "../_partials/header.php"; //to be able to check if user is admin or editor!


    // just to be safe - tady nemáš co dělat
    if ( ! logged_in() ) {
        redirect('/');
    }


    //id has to be int
    $post_id = filter_input (INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

    //Controls if it exists and is the author of the post
    if ( ! $post_id || !$post = get_post($post_id, false))
    {
        flash() -> error("What are you trying to do?!?!?!?!");
        redirect('back');
    }
    if (!can_edit( $post ) && !$is_admin  && !$is_editor)
    {
        flash() -> error("What are you trying to do?!?!?!?! You have no rights to delete this Masterpiece!");
        redirect('back');
    }

    //Delete post

    $delete = delete_post($post_id);

    //redirect to post with flash messages
    if(! $delete)
    {
        flash() -> warning("MATRIX ERROR! Post wasn't deleted!");
    }

    //Remove tags for the post
    delete_tags_for_post($post_id);
    //Remove comments for the post
    delete_comments_for_the_post($post_id);
    //Remove post directory with images
    deleteDir(IMAGE_PATH . "/$post_id");
    //go home
    flash()->success("Good night, my little sweet post!");
    redirect( '/' );
