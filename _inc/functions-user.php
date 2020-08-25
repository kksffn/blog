<?php

    use PHPMailer\PHPMailer\PHPMailer;

    /**
     * Get all users from DB
     * @return array
     */
    function get_users()
    {
        global $db;

        $query = $db->query("SELECT id, nickname, email, rights, ban  FROM phpauth_users
        WHERE id <> 3 ORDER BY rights, nickname");

        if ( $query->rowCount() )
        {
            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $result) {
                $result['nickname'] = filter_var($result['nickname'], FILTER_SANITIZE_STRING);
                $result['email'] = filter_var( $result['email'], FILTER_SANITIZE_EMAIL );
            }
        }
        else
        {
            $results = [];
        }

        return $results;
    }

    /**
     * Create link used for admin to edit other users rights and datas and to view the data
     * @param $user
     * @param $type
     * @param int $id
     * @return mixed
     */
    function get_admins_link($user, $type, $id = 0)
    {
        $link = '';

        if (is_admin($user->uid) && ($type == 'edit-tag' || $id != 3) ){
            $link = BASE_URL."/$type";

            if ($type !=='users' && $type !=='tags') {
                $link .="/$id";
            }

        }elseif (!is_admin($user->uid)) {
                $link = BASE_URL;
        }elseif ($id == 3) {
            $link = BASE_URL.'/users';
        }
        $link = filter_var( $link, FILTER_SANITIZE_URL );
        return $link;
    }

    /**
     * Create editor's link - to delete all user's post and to view the summary about user's posts
     * @param $user
     * @param $type
     * @param int $id
     * @return mixed
     */
    function get_editors_link($user, $type, $id = 0)
    {
        $link = '';
        if (is_editor($user->uid) && $id != 3 ){
            $link = BASE_URL."/$type";

            if ($type ==='delete-users-posts') {
                $link .="/$id";
            }
        }elseif (!is_editor($user->uid)) {
                $link = BASE_URL;

        }elseif ($id == 3) {
            $link = BASE_URL.'/editor';
        }

        $link = filter_var( $link, FILTER_SANITIZE_URL );
        return $link;
    }

    /**
     * Count user's posts
     * @param $id
     * @return false
     */
    function get_number_of_users_posts($id)
    {
        global $db;
        $query = "SELECT COUNT(*) AS count FROM posts WHERE user_id = :uid";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute(['uid' => $id]);

        if ( $query_prepared->rowCount() == 1 )
        {
            $result = $query_prepared->fetch(PDO::FETCH_OBJ);
            $result = $result->count;
        }
        else
        {
            $result = false;
        }
        return $result;
    }

    /**
     * Cout comments of the user
     * @param $user_id
     * @return false
     */
    function get_number_of_users_comments($user_id)
    {
        global $db;
        $query = "SELECT COUNT(*) AS count FROM comments WHERE user_id = :uid";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute(['uid' => $user_id]);

        if ( $query_prepared->rowCount() == 1 )
        {
            $result = $query_prepared->fetch(PDO::FETCH_OBJ);
            $result = $result->count;
        }
        else
        {
            $result = false;
        }
        return $result;
    }

    /**
     * Change author of the post to Anonymous after deleting a user
     * @param $id
     * @return false|int
     */
    function change_author_of_the_posts_of($id) {
        global $db;
        $query = "UPDATE posts SET user_id = 3 WHERE user_id = :uid";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute(['uid' => $id]);
        if ($query_prepared->rowCount() != 0)
        {
            $result = $query_prepared->rowCount();
        }else {
            $result = false;
        }
        return $result;
    }

    /**
     * Change author of the comments to Anonymous after deleting a user
     * @param $id
     * @return false|int
     */
    function change_author_of_the_comments_of($id) {
        global $db;
        $query = "UPDATE comments SET user_id = 3 WHERE user_id = :uid";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute(['uid' => $id]);
        if ($query_prepared->rowCount() != 0)
        {
            $result = $query_prepared->rowCount();
        }else {
            $result = false;
        }
        return $result;
    }

    /**
     * Count admins - at least one admin has to remain
     * @return mixed
     */
    function get_number_of_admins()
    {
        global $db;
        $query = "SELECT COUNT(*) AS count FROM phpauth_users WHERE rights = 'admin'";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute();

        $result = $query_prepared->fetch(PDO::FETCH_OBJ);
        echo $result->count;
        return  $result->count;

    }

    /**
     * Get all posts written by user
     * @param $id
     * @return false|int
     */
    function deleteAllUsersPosts($id)
    {
        global $db;
        $query = "DELETE FROM posts WHERE user_id = :uid";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute(['uid' => $id]);

        if ($query_prepared->rowCount() != 0)
        {
            $result = $query_prepared->rowCount();
        }else {
            $result = false;
        }
        return $result;
    }

    /**
     * validate info given by user in profile form, create session if not valid
     * @return array|false
     * @throws \Tamtamchik\SimpleFlash\Exceptions\FlashTemplateNotFoundException
     */
    function validate_user_info()
    {
        //sanitize & validate inputs; FILTER_FLAG_NO_ENCODE_QUOTES uloží normální apostrofy a uvozovky bez konverze na bezpečné znaky...
        $nickname = filter_input( INPUT_POST, 'user_nickname', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $email = filter_input( INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL, FILTER_FLAG_NO_ENCODE_QUOTES);
        $about_me = filter_input(INPUT_POST,'about_me', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);


        //nickname required
        if (! $nickname = trim($nickname))
        {
            flash() -> error("MATRIX ERROR!!! You don't have any <strong>nickname</strong>!");
            $nickname = '';
        }

        //email required
        if (! $email = trim($email))
        {
            flash() -> error('MATRIX ERROR!!! You don\'t have any <strong>email</strong>!');
            $email = '';
        }

        //Not to send false
        if (! $about_me = trim($about_me))
        {
            $about_me = '';
        }

//        $about_me = plain($about_me);
//        $about_me = filter_url($about_me);
//        $about_me = add_paragraphs($about_me);

        //Pokračujeme, pouze pokud nejsou chyby
        if (flash() -> hasMessages())
        {
            create_session_user_data($nickname, $email, $about_me);
            return false;
        }

        //return array of values
        return compact(
            'nickname', 'email', 'about_me',
             $nickname, $email, $about_me
        );
    }

    /**
     * Create session with user data from form
     * @param $nickname
     * @param $email
     * @param string $about_me
     */
    function create_session_user_data($nickname, $email, $about_me = ' ')
    {
        $_SESSION['user_data'] = [
            'nickname' => $nickname,
            'email' => $email,
            'about_me' => $about_me,
        ];
    }

    /**
     * Get user from token created as reset key
     * @param $reset_key
     * @return bool|mixed|object
     */
    function getUserFromToken($reset_key)
    {
        global $db;
        global $auth_config;
        $query = "SELECT uid as uid FROM {$auth_config->table_requests} WHERE token = ? AND type = ?";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute([$reset_key, 'reset']);

        if ($query_prepared->rowCount() <> 0) {
            $user_id = $query_prepared->fetchColumn();
        }
        $user = get_user($user_id);
        return $user;
    }

    /**
     * Send mail with nickname and new password
     * @param object $user
     * @param $new_psw
     * @return bool[]
     */
    function sendNewLoginData($user, $new_psw): array
    {
        global $auth_config;
        $mail = new PHPMailer(true);
        $return = [
            'error' => true
        ];

        // Check configuration for custom SMTP parameters
        try {
            // Server settings
            if ($auth_config->smtp) {

                if ($auth_config->smtp_debug) {
                    $mail->SMTPDebug = $auth_config->smtp_debug;
                }
                $mail->isSMTP();

                $mail->Host = $auth_config->smtp_host;
                $mail->SMTPAuth = $auth_config->smtp_auth;

                // set SMTP auth username/password
                if (!is_null($auth_config->smtp_auth)) {
                    $mail->Username = $auth_config->smtp_username;
                    $mail->Password = $auth_config->smtp_password;
                }

                // set SMTPSecure (tls|ssl)
                if (!is_null($auth_config->smtp_security)) {
                    $mail->SMTPSecure = $auth_config->smtp_security;
                }
                $mail->Port = $auth_config->smtp_port;
            } //without this params internal mailer will be used.

            //Recipients
            $mail->setFrom($auth_config->site_email, $auth_config->site_name);
            $mail->addAddress($user->email);

            $mail->CharSet = $auth_config->mail_charset;

            //Content
            $mail->isHTML(true);

            $mail->Subject = "Nové přihlašovací údaje pro bloG";
            $mail->Body = "Dear $user->nickname, <br> <br> Vaše nové přihlašovací údaje
                pro stránku $auth_config->site_url jsou: <br> <br> přezdívka: <strong>$user->nickname</strong><br> 
                heslo: <strong>$new_psw</strong> <br><br>
                 S pozdravem <br> Your bloG";

            if (!$mail->send())
                throw new \Exception($mail->ErrorInfo);
            $return['error'] = false;
        } catch (\Exception $e) {
            $return['message'] = $mail->ErrorInfo;
        }
        return $return;
    }