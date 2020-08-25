<?php

    // include
    require '../_inc/config.php';

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
        // just to be safe - tady nemáš co dělat
        if ( ! logged_in() ) {
            redirect('/');
        }
    }

    // c'mon baby do the locomo.. validation
    if ( ! $data = validate_comment() ) {

        $text = "Your comment is not valid...";
        if (is_ajax()) {
            $message = json_encode([
                'message' => $text
            ]);
            die($message);
        }else {
            flash()->error($text);
            redirect('back');
        }
    } else {

        $user = get_user();
        $uid = $user->uid;
        $nickname = plain($user->nickname);

        //We get $post_id, $comment_text and $link to post
        extract( $data );

        $insert = add_comment($uid, $post_id, $comment_text);

        //something went wrong
        if ($insert['error']) {
            $_SESSION['form_data'] = [
                'comment_text'  => $comment_text,
            ];
            if (is_ajax()) {
                $message = json_encode([
                    'message' => $insert['message']
                ]);
                die($message);
            }else {
                flash()->error($insert['message']);
                redirect('back');
            }
        }

        //Dál se dostane, jen když bylo uložení do DB úspěšné
        if ( is_ajax()) {   //Pokud request přišel AJAXem
            $message = json_encode([
                'status' => 'success',
                'nickname' => $nickname,
                'post_id' => $post_id,
                'comment_id' => $insert['id'],
                'comment_text' => $comment_text,
                'time' => date( 'j M Y, G:i', time()),
                'i' => count_comments($post_id),
                'message' => $insert['message']
            ]);
            die($message);

        }else {
            $link .='#commentlist';
            flash()->success($insert['message']);
            redirect($link);
        }
    }
//-------------------------------------------------------------------------------------------------//


