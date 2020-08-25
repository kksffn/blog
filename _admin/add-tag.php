<?php

//   include_once "_partials/header.php"; //to be able to check if user is admin
//    require_once "_inc/config.php";

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
    {
        // just to be safe - tady nemáš co dělat
        if ( ! logged_in() ) {
            redirect('/');
        }

        $new_tag = trim(plain(filter_var($_POST['new-tag'], FILTER_SANITIZE_STRING )));
        $odd = $_POST['odd'];

        if (!$new_tag || strlen($new_tag)>30)
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
        else
        {
            $add = add_new_tag($new_tag);

            if ($add['error']) {
                if (is_ajax()) {
                    $message = json_encode([
                        'message' => $add['message']
                    ]);
                    die($message);

                }else {
                    flash()->error($add['message']);
                    redirect('back');
                }
            }

            //Dál se dostane, jen když bylo uložení do DB úspěšné
            if ( is_ajax()) {   //Pokud request přišel AJAXem
                $message = json_encode([
                    'status' => 'success',
                    'id' => $add['id'],
                    'tag' => $new_tag,
                    'odd' => $odd
                ]);
                die($message);

            }else {
                flash()->success($add['message']);
                redirect('back');
            }
        }
    }