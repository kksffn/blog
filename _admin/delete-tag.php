<?php

    include_once "_partials/header.php"; //to be able to check if user is admin
    //require_once "_inc/config.php";


    // just to be safe - tady nemáš co dělat
    validateUser($is_admin);

    $tag_id = get_segment(2);

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $delete = delete_tag($tag_id);

        if ($delete['error']) {
            flash()->error($delete['message']);
        } else {
            flash()->success($delete['message']);
        }

        redirect('back');
    }

