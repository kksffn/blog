<?php

    /**
     * Logged In
     *
     * Is a user logged in?
     *
     * @return bool
     */
    function logged_in()
    {
        global $auth, $auth_config;

        return
            isset($_COOKIE[$auth_config->cookie_name]) &&
            $auth->checkSession($_COOKIE[$auth_config->cookie_name]);
    }

    /**
     * Do Login
     *
     * Create cookie after logging in
     *
     * @param   array  $data
     * @return  bool
     */
    function do_login( $data )
    {
        global $auth_config;

        return setcookie(
            $auth_config->cookie_name,
            $data['hash'],
            $data['expire'],
            $auth_config->cookie_path,
            $auth_config->cookie_domain,
            $auth_config->cookie_secure,
            $auth_config->cookie_http
        );
    }

    /**
     * Do Logout
     *
     * Log the user out
     *
     * @return bool
     */
    function do_logout()
    {
        if ( ! logged_in() ) return true;

        global $auth, $auth_config;

        return $auth->logout( $_COOKIE[$auth_config->cookie_name] );
    }

    /**
     * Get user
     *
     * Grab data for the logged in user
     *
     * @param  integer  $user_id
     * @return bool|mixed
     */
    function get_user( $user_id = 0 )
    {
        global $auth, $auth_config;

        if ( ! $user_id && logged_in() ) {
            $user_id = $auth->getSessionUID($_COOKIE[$auth_config->cookie_name]) ?: 0;
        }
        if (! ($user = $auth->getUser( $user_id )) )
        {
            return false;
        }
        return (object) $user;
    }

    /**
     * Can Edit
     *
     * True if this user was written by the logged in user
     *
     * @param  mixed  $post
     * @return bool
     */
    function can_edit( $post )
    {
        // must be logged in
        if ( ! logged_in() ) {
            return false;
        }

        if ( is_object( $post ) ) {
            $post_id = (int) $post->user_id;
        }
        else {
            $post_id = (int) $post['user_id'];
        }

        $user = get_user();

        return $post_id === $user->uid;
    }

    function is_admin($user_id)
    {
        // must be logged in
        if ( ! logged_in() ) {
            return false;
        }
        global $db;
        $query = "SELECT rights FROM phpauth_users
        WHERE id = :id";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute([ 'id' => $user_id ]);

        if ( $query_prepared->rowCount() ) {
            $result = $query_prepared->fetch(PDO::FETCH_COLUMN);
        }
        else {
            $result = 0;
        }

        return $result === 'admin';
    }

    function is_editor($user_id)
    {
        // must be logged in
        if ( ! logged_in() ) {
            return false;
        }
        global $db;
        $query = "SELECT rights FROM phpauth_users
            WHERE id = :id";
        $query_prepared = $db->prepare($query);
        $query_prepared->execute([ 'id' => $user_id ]);

        if ( $query_prepared->rowCount() ) {
            $result = $query_prepared->fetch(PDO::FETCH_COLUMN);
        }
        else {
            $result = 0;
        }

        return $result === 'editor';
    }

    /**
     * Finds out if the user is loggd in and is admin
     * @param $is_admin_or_editor
     * @throws \Tamtamchik\SimpleFlash\Exceptions\FlashTemplateNotFoundException
     */
    function validateUser($is_admin_or_editor)
    {
        if (!logged_in()) {
            redirect('/');
        }

        if (!$is_admin_or_editor) {
            flash()->error("What are you trying to do?!?!?!?!");
            redirect('/');
        }
    }