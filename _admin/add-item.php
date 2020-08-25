<?php

    // include
    require '../_inc/config.php';

    // just to be safe - tady nemáš co dělat
    if ( ! logged_in() ) {
        redirect('/');
    }

    // c'mon baby do the locomo.. validation
    if ( ! $data = validate_post() ) {
        redirect('back');
    }

    $uid = get_user() -> uid;
    //We get $post_id, $title, $text, $tags
    extract( $data );

    list($slug, $insert) = add_new_post($title, $uid, $text);

    // something went wrong
    if ( ! $insert )
    {
        flash()->warning( 'Something went wrong with adding new post :(' );
        redirect('back');
    }

    // great success!
    $post_id = $db->lastInsertId();

    // if we have tags, add them
    if (isset($tags) && $tags = array_filter($tags))
    {
        insert_tags_for_post($tags, $post_id);
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


    // let's visit the new post
    flash()->success( 'Success, new post added!' );

    redirect(get_post_link([
        'id'   => $post_id,
        'slug' => $slug,
    ]));


