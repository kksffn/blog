<?php

    /**
     * Show 404
     *
     * Sends 404 not found header
     * And shows 404 HTML page
     *
     * @return void
     */
    function show_404()
    {
        header("HTTP/1.0 404 Not Found");
        include_once "404.php";
        die();
    }

    /**
     * Is AJAX
     *
     * Determines if request was AJAXed into existence
     *
     * @return bool
     */
    function is_ajax()
    {
        return ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' );
    }


    /**
     * Is Even
     *
     * Returns TRUE if $number is even
     * FALSE if odd
     *
     * @param  integer  $number number in question
     * @return boolean          true if even
     */
    function is_even( $number )
    {
        return $number % 2 == 0;
    }


    /**
     * Get Parity
     *
     * Returns string "even" for even numbers
     * And, surprise, "odd" for odd numbers
     *
     * @param  integer $number number in question
     * @return string          "even" if true, "odd" if false
     */
    function get_parity( $number )
    {
        return is_even($number) ? 'even' : 'odd';
    }



    function redirect( $page, $status_code = 302, $message = '' )
    {
        if ( $page == 'back' )
        {
            $location = $_SERVER['HTTP_REFERER'];
        }
        else
        {
            $page = str_replace( BASE_URL, '', $page );
            $page = ltrim($page, '/');

            $location = BASE_URL . "/$page";
        }

        header("Location: $location", true, $status_code );
        die($message);
    }

    /**
     * Creates absolute URL to asset file
     * @param string $path path to asset file
     * @param string $base asset base url
     * @return string absolute url to asset file
     */
    function asset($path, $base = BASE_URL . '/assets/' )
    {
        $path = trim( $path, '/');
        return filter_var( $base . $path, FILTER_SANITIZE_URL);
    }


    /**
     * Get segments from url
     * ex.: http://.../blog/edit/2/ayay
     * returns array ['edit', '2', 'ayay']
     * @return false|string[]
     */
    function get_segments()
    {
        $current_url = 'http'.
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's://' : '://').
            $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; //celá adresa
        $path = str_replace(BASE_URL,'',$current_url);  //bez začátku
        $path = trim(parse_url( $path, PHP_URL_PATH), '/');    //?? super fce

        $segments = explode('/', $path);

        //Fce vrací segmenty bez ohledu na to, v jakém adresáři se naše aplikace nachází!
        return $segments;

    }

    /**
     * Returns the $i-th segment of url
     * @param $i
     * @return bool|mixed|string
     */
    function get_segment($i)
    {
        $segments = get_segments();
        return (isset($segments[$i-1]) ? $segments[$i-1] : false);

    }