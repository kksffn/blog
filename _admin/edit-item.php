<?php

	// include
	require '../_inc/config.php'; //o adresář výše, vejít do _inc, tam je config.php
    include_once "../_partials/header.php"; //to be able to check if user is admin or editor!



    // just to be safe - tady nemáš co dělat
    if ( ! logged_in() ) {
        redirect('/');
    }


    // c'mon baby do the locomo.. validation
    if ( ! $data = validate_post() ) {
        redirect('back');
    }


    //We get $post_id, $title, $text, $tags
    extract( $data );


    //Controls if it exists and is the author of the post
    if ( ! $post_id || !$post = get_post($post_id, false))
    {
        flash() -> error("What are you trying to do?!?!?!?!");
        redirect('back');
    }

    if (!can_edit( $post ) && !$is_admin  && !$is_editor )
    {
        flash() -> error("What are you trying to do?!?!?!?! You have no rights to edit this Masterpiece!");
        redirect('back');
    }

    //UPDATE TITLE OR TEXT IF NEEDED

    //variables needed to control the process
    $post = get_post($post_id, false);
    //boolean: whether changes are needed
    $change_tag = changes_in_tags( $post_id, $tags);
    $update_post = false;

    $exists_image = exists_image_for_post($post_id);
    $change_image = ( $exists_image && empty($_FILES["image"]['name']) ) ||
        ( !empty($_FILES["image"]['name']) ) ;



    //redirect back if no changes were made
    //else prepare statement and update title or text
    if ($post->title === $title && $post->text === $text) {
            if ( ! $change_tag && ! $change_image ) {
                flash() -> warning("Hey! YOU changed nothing. You have a new try...");
                redirect('back');
            }
    }else {
        list($query, $execution) = create_update_query( $post, $title, $text, $post_id);
        $update_post = $db -> prepare($query);
        $update_post -> execute($execution);
        $update_post = true;
    }

    //UPDATE TAGS IF NEEDED

    if ($change_tag) {
        // remove all tags for the post
        delete_tags_for_post($post_id);

        //If we have tags add them
        if (isset($tags) && $tags = array_filter($tags))
        {
            insert_tags_for_post($tags, $post_id);
        }
    }

    // if the image is set, upload it and store in DB
    if (!empty($_FILES["image"]) && $_FILES['image']['error'] == 0)
    {
        $upload = upload_image_for_post($post_id);
        foreach ($upload as $key => $message)
        {
            if (!empty($message))
            {
                if (isset($upload['error']) && $upload['error']) {
                    if ($key != 'error') {flash()->error($message) ;}
                }else {
                    flash()->success($message);
                }
            }
        }
    }
    //if user wishes to delete image for the post
    if ($change_image && empty($_FILES['image']['name']))
    {
        $delete = delete_image_for_post($post_id);
        if (! $delete) {
            flash()->error("Deleting the image was not successful.");
        }
    }

    //redirect to post with flash messages
    if($update_post)
    {
        flash() -> success('Post successfully changed!');
    }
    if  ($change_tag){
        flash() -> success('Tags changed!');
    }

    redirect( get_post_link($post) );
