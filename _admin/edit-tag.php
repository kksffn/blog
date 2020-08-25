<?php

    //include_once "_partials/header.php"; //to be able to check if user is admin
    //require_once "_inc/config.php";


    // just to be safe - tady nemáš co dělat
    //validateUser($is_admin);
    if ( ! logged_in() ) {
        redirect('/');
    }

    if (! $tag_id = get_segment(2)) {
        show_404();
    }

    $tag_id = filter_var($tag_id, FILTER_VALIDATE_INT);

    if (!$tag_id) {
        flash()->error("WHAT?? You can't do that!");
        redirect('/');
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        //$tag = mb_strtolower(explode(' ', $_POST['edit-tag-'.$tag_id] )[0], "UTF-8");
        $new_tag = trim(plain(filter_var($_POST['edit-tag-'.$tag_id], FILTER_SANITIZE_STRING )));

        if (!$new_tag)
        {
            $text = "c'mon, input some valid text";
            if (is_ajax()) {
                $message = json_encode([
                    'message' => $text
                ]);
                die($message);
            }else {
                flash()->error($text);
                redirect('back');
            }
        }
        else {
            if ($new_tag === $_POST['tagname-'.$tag_id]) {

                $text = "You haven't changed the name..";
                if (is_ajax()) {
                    $message = json_encode([
                        'message' => $text
                    ]);
                    die($message);
                }else {
                    flash()->message($text);
                    redirect('back');
                }
            }


            $edit = edit_tag($tag_id, $new_tag);


            if ($edit['error']) {
                if (is_ajax()) {
                    $message = json_encode([
                        'message' => $edit['message']
                    ]);
                    die($message);
                }else {
                    flash()->error($edit['message']);
                    redirect('back');
                }
            }
            //Dál se dostane, jen když byl update v DB úspěšný

            if ( is_ajax()) {   //Pokud request přišel AJAXem
                $message = json_encode([
                    'status' => 'success',
                    'message' => $edit['message'],
                    'id' => $tag_id,
                    'tag' => $new_tag,
                ]);
                die($message);

            }else {
                flash()->success($edit['message']);
                redirect('back');
            }
        }
    }

