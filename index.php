<?php

    require '_inc/config.php';

    //routing
    $routes = [
        //HOMEPAGE
        '/' => [
            'GET' => 'home.php'
        ],
        // USER'S POSTS
        '/user' => [
            'GET'  => 'user.php'             // user's posts
        ],

        // LOGIN
        '/login' => [
            'GET'  => 'login.php',           // login form
            'POST' => 'login.php',           // do login
        ],

        // REGISTER
        '/register' => [
            'GET'  => 'register.php',        // register form
            'POST' => 'register.php',        // do register
        ],

        // LOGOUT
        '/logout' => [
            'GET'  => 'logout.php',          // logout user
        ],


        // POST PAGE OR NEW POST
        '/post' => [
            'GET' => 'post.php',
            'POST' => '_admin/add-item.php'
        ],

        // POSTS FOR TAG
        '/tag' => [
            'GET' => 'tag.php'
        ],

        // EDIT POST
        '/edit' => [
            'GET' => 'edit.php',
            'POST' => '_admin/edit-item.php'
        ],

        // DELETE POST
        '/delete' => [
            'GET' => 'delete.php',
            'POST' => '_admin/delete-item.php'
        ],

        // EDIT USERS DATA
        '/users' => [
            'GET' => '_admin/admins-page.php',
            'POST' => '_admin/admins-page.php'
        ],

        //DELETE USER
        '/delete-user' => [
            'GET' => '_admin/delete-user-form.php',
            'POST' => '_admin/delete-user.php'
        ],

        //EDITORS PAGE
        '/editor' => [
            'GET' => '_admin/editors-page.php'
        ],

        //DELETE USER'S POSTS
        '/delete-users-posts' => [
            'GET' => '_admin/delete-users-posts-form.php',
            'POST' => '_admin/delete-users-posts.php'
        ],

        //RESET REQUEST - FORGOTTEN PASSWORD
        '/reset-password' => [
          'GET' => 'reset-request.php',
            'POST' => 'reset-request.php'
        ],

        //RESET FORGOTTEN PASSWORD
        '/reset' => [
            'GET' => 'reset.php',
            'POST' => 'reset.php'
        ],

        //USER'S PROFILE
            '/profile' => [
                'GET' => 'profile.php',
                'POST' => 'profile.php',
            ],
        //USER SETTINGS
            '/settings' => [
                'GET' => 'settings.php',
                'POST' => 'settings.php',
            ],

        //ACTIVATING USER ACCOUNT
            '/activate' => [
                'GET' => 'activate.php',
                'POST' => 'activate.php',
            ],

            '/reactivate' => [
                'GET' => 'reactivate.php',
                'POST' => 'reactivate.php',
            ],

        //EDIT TAGS (admin only)
            '/tags' => [
                'GET' => '_admin/tags-control.php',
                'POST' => '_admin/tags-control.php',
            ],

        //ADD NEW TAG
            '/add-tag' => [
                'GET' => 'home.php',
                'POST' => '_admin/add-tag.php',
            ],

        //EDIT TAG
             '/edit-tag' => [
                'GET' => 'home.php',
                'POST' => '_admin/edit-tag.php',
            ],

        //DELETE TAG
        '/delete-tag' => [
            'GET' => 'home.php',
            'POST' => '_admin/delete-tag.php',
        ],

        //ADD COMMENT
        '/add-comment' => [
            'GET' => 'home.php',
            'POST' => '_admin/add-comment.php'
        ],


    ];

    $page = get_segment(1);
    $method = $_SERVER['REQUEST_METHOD'];

    // Veřejně přístupné adresy
    $public = [
        'login', 'register', 'reset-password', 'activate', 'reactivate', 'reset',
    ];

    // Pokud nejsi přihlášený, můžeš jít jen na registraci nebo login
    if ( !logged_in() && !in_array( $page, $public ) ) {
        redirect('/login');
    }

    //Pokud neexistuje adresa v $routes, vyhoď 404
    if ( !isset($routes["/$page"][$method])) {                   //! in_array(get_segment(1), $routes)) {
        show_404();
    }

    //jinak vyhoď tu stránku, kterou máš :)
    require $routes["/$page"][$method];

