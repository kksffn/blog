<?php

    /**
     * Get Post
     *
     * Tries to fetch a DB item based on $_GET['id']
     * Returns false if unable
     *
     * @param  integer    id of the user to get
     * @param  bool|true  $auto_format  whether to format all the posts or not
     * @return DB item    or false
     */
    function get_post( $id = 0, $auto_format = true )
    {
        // we have no id
        if ( !$id && !$id = get_segment(2) ) {
            return false;
        }

        // $id must be integer
        if ( ! filter_var( $id, FILTER_VALIDATE_INT ) ) {
            return false;
        }

        global $db;

        $query = $db->prepare(create_posts_query("WHERE p.id = :id"));

        $query->execute([ 'id' => $id ]);

        if ( $query->rowCount() === 1 )
        {
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ( $auto_format ) {
                $result = format_post( $result, true);
            } else {
                $result = (object) $result;
            }
        }
        else
        {
            $result = false;
        }

        return $result;
    }

    /**
     * Get Posts
     *
     * Grabs all posts from the DB
     * And maybe formats them too, depending on $auto_format
     *
     * @param  bool|true  $auto_format  whether to format all the posts or not
     * @return array
     */
    function get_posts( $auto_format = true )
    {
        global $db;

        $query = $db->query(create_posts_query());

        if ( $query->rowCount() )
        {
            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            if ( $auto_format ) {
                $results = array_map('format_post', $results);
            }
        }
        else
        {
            $results = [];
        }

        return $results;
    }

    /**
     * Get all posts by tag name
     *
     * Tries to fetch a DB items based on $_GET['id']
     * Returns false if unable
     *
     * @param  string       tag
     * @param  bool|true  $auto_format  whether to format all the posts or not
     * @return array
     */
    function get_posts_by_tag( $tag = '', $auto_format = true )
    {
        // we have no tag
        if ( !$tag && !$tag = get_segment(2) ) {
            return false;
        }

        $tag = urldecode($tag);
        $tag = filter_var($tag, FILTER_SANITIZE_STRING);

        global $db;

        //Toto není dobré řešení: vypíše jen daný taf, i když post má více tagů
        //$query = $db -> prepare(create_posts_query("WHERE t.tag = :tag"));

        $query = $db->prepare("
            SELECT p.id FROM posts p
            LEFT JOIN posts_tags pt ON (p.id = pt.post_id)
            LEFT JOIN tags t ON (t.id = pt.tag_id)
            WHERE t.tag = :tag 
        ");
        $query->execute([ 'tag' => $tag ]);
        if ( !$query->rowCount() )
        {
            return [];
        }
        $posts_ids = $query->fetchAll(PDO::FETCH_ASSOC);



        $where ='';
        $execution = [];
        foreach ($posts_ids as $id)
        {
            $where .= '?,';
            $execution []= $id['id'];
        }
        $where = substr($where, 0, -1);


        $query = $db->prepare(create_posts_query("WHERE p.id IN ($where)"));
        $query->execute($execution);  //['ids' => $execution]);

        if ( $query->rowCount() )
        {

            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            if ( $auto_format ) {
                $results = array_map('format_post', $results);
            }
        }
        else
        {
            $results = [];
        }

        return $results;
}

    /**
     * Get Posts By User
     *
     * Grabs posts by user id, if no uid is provided, we get logged users id
     * And maybe formats them too, depending on $auto_format
     *
     * @param  integer    $user_id
     * @param  bool|true  $auto_format  whether to format all the posts or not
     * @return array
     */
    function get_posts_by_user( $user_id = 0, $auto_format = true )
    {
        // we have no id
        if ( !$user_id ) {  //&& !$user_id = get_user()->uid ) {
            return [];   //false;
        }

        global $db;

        $query = $db->prepare( create_posts_query("WHERE p.user_id = :uid") );
        $query->execute([ 'uid' => $user_id ]);

        if ( $query->rowCount() )
        {
            $results = $query->fetchAll( PDO::FETCH_ASSOC );

            if ( $auto_format ) {
                $results = array_map( 'format_post', $results );
            }
        }
        else
        {
            $results = [];
        }

        return $results;
    }

    /**
     * Add new post to DB
     * @param $title
     * @param $uid - user id of the author
     * @param $text
     * @return array
     */
    function add_new_post($title, $uid, $text): array
    {
        global $db;
        $slug = slugify($title);
        $query = $db->prepare("
                    INSERT INTO posts
                        ( user_id, title, text, slug )
                    VALUES
                        ( :uid, :title, :text, :slug )
                ");

        $insert = $query->execute([
            'uid' => $uid,
            'title' => $title,
            'text' => $text,
            'slug' => $slug
        ]);
        return array($slug, $insert);
    }

    /**
     * Create Posts Query
     *
     * Put together the query to get posts
     * We can add WHERE conditions too
     *
     * @param  string $where - WHERE condition
     * @return string
     */
    function create_posts_query( $where = '' )
    {
        $query = "
                SELECT p.*, u.nickname, u.email, GROUP_CONCAT(t.tag SEPARATOR '~||~') AS tags
                FROM posts p
                LEFT JOIN posts_tags pt ON (p.id = pt.post_id)
                LEFT JOIN tags t ON (t.id = pt.tag_id)
                LEFT JOIN phpauth_users u ON (p.user_id = u.id)
            ";

        if ( $where ) {
            $query .= $where;
        }

        $query .= " GROUP BY p.id";
        $query .= " ORDER BY p.created_at DESC";

        return trim( $query );
    }


    /**
     * Creates query for updating post, returns prepared statement & arrays of values into this statement
     * @param DB $post
     * @param $title
     * @param $text
     * @param $post_id
     * @return array $query, $execution
     */
    function create_update_query( $post, $title, $text, $post_id)
    {
        $query = '';
        $execution =[];
        $slug = '';
        $query .= "UPDATE posts SET ";
        if ($post->title !== $title) {
            $slug = slugify( $title );
            $query .= " title = :title, slug = :slug,";
            $execution['title'] = $title;
            $execution['slug'] = $slug;
        }
        if ($post->text !== $text) {
            $query .= " text = :text,";
            $execution['text'] = $text;
        }
        $query = substr($query, 0, -1); //odstraníme čárku na konci!
        $query .= " WHERE id = :post_id";
        $execution['post_id'] = $post_id;
        return array($query, $execution);
    }

    /**
         * Format Post
         *
         * Cleans up, sanitizes, formats and generally prepares DB user for displaying
         *
         * @param  $post
         * @param boolean $format_text should only be true on page of the user
         * @return object
         */
    function format_post( $post, $format_text = false )
    {
        // trim dat shit - ošetříme vše od uživatele
        $post = array_map('trim', $post);

        // clean it up
        $post['nickname'] = plain($post['nickname']);
        $post['title'] = plain( $post['title'] );
        $post['text']  = plain( $post['text'] );
        $post['tags']  = $post['tags'] ? explode('~||~', $post['tags']) : [];
        $post['tags']  = array_map('plain', $post['tags']);

        //add tag links
        if ($post['tags']) foreach ($post['tags'] as $tag) {
            //$tag naformátujeme jako url(např. "+" místo mezer)
            $post['tag_links'][$tag] = BASE_URL. '/tag/'. urlencode($tag);
            $post['tag_links'][$tag] = filter_var( $post['tag_links'][$tag], FILTER_SANITIZE_URL);
        }

        // create link to user [ /user/:id/:slug ]
        $post['link'] = get_post_link( $post );

        // let's go on some dates
        $post['timestamp'] = strtotime( $post['created_at'] );
        $post['time'] = str_replace( ' ', '&nbsp', date( 'j M Y, G:i', $post['timestamp'] ) );
        $post['date'] = date( 'Y-m-d', $post['timestamp'] );

        // don't tease me, bro
        $post['teaser'] = word_limiter( $post['text'], 40 );

        // format text
        if ($format_text){
            $post['text'] = filter_url($post['text']);
            $post['text'] = add_paragraphs($post['text']);
        }

        // user
        $post['nickname'] = filter_var($post['nickname'], FILTER_SANITIZE_STRING);
        $post['email'] = filter_var( $post['email'], FILTER_SANITIZE_EMAIL );
        $post['user_link'] = BASE_URL . '/user/' . $post['user_id'];
        $post['user_link'] = filter_var( $post['user_link'], FILTER_SANITIZE_URL );

        return (object) $post;
    }

    //této fci posíláme object i array
    /**
     * Create link to the post
     * @param $post
     * @param string $type
     * @return mixed
     */
    function get_post_link($post, $type = 'post' )
    {
        if (is_object($post)){
            $id = $post->id;
            $slug = $post->slug;
        }else {
            $id = $post['id'];
            $slug = $post['slug'];
        }

        $link = BASE_URL . "/$type/$id";
        if ($type === 'post'){
            $link .= "/$slug";
        }
        $link = filter_var( $link, FILTER_SANITIZE_URL );

        return $link;
    }

    /**
     * Create link to edit the post
     * @param $post
     * @return mixed
     */
    function get_edit_link($post){
        return get_post_link( $post, 'edit' );
    }

    /**
     * Create link to delete the post
     * @param $post
     * @return mixed
     */
    function get_delete_link($post){
        return get_post_link( $post, 'delete' );
    }

    /**
     * Validate, sanitize post
     * @return array|false
     * @throws \Tamtamchik\SimpleFlash\Exceptions\FlashTemplateNotFoundException
     */
    function validate_post()
    {
        //sanitize & validate inputs; FILTER_FLAG_NO_ENCODE_QUOTES uloží normální apostrofy a uvozovky bez konverze na bezpečné znaky...
        $title = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $text = filter_input( INPUT_POST, 'text', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $tags = filter_input(INPUT_POST,'tags', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        //každý tag v array je int

        //Maybe user creates new tag
        if (isset($_POST['tags-input']) && $_POST['tags-input']) {

            $new_tags_ids = get_new_tags_ids($_POST['tags-input']);

            foreach ($new_tags_ids as $id) {
                $tags[] = $id;
            }
        }

        //Remove duplicate values from $tags, maybe user wrote existing one => PDO exception
        if (isset($tags))
        {
            $tags = array_unique($tags);
        }

        // on edit, also validate post_id
        if (isset($_POST['post_id']))
        {
            //id required & has to be int
            $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);

            if ( ! $post_id)
            {
                flash() -> error("What are you trying to do?!?!?!?!");
            }
        } else
        {
            $post_id = false;
        }

        //title required
        if (! $title = trim($title))
        {
            flash() -> error("MATRIX ERROR!!! You don't have any <strong>title</strong> of your post!");
        }

        //text required
        if (! $text = trim($text))
        {
            flash() -> error("MATRIX ERROR!!! You don't have any <strong>text</strong> in your post!");
        }

        //Pokračujeme, pouze pokud nejsou chyby
        if (flash() -> hasMessages())
        {
            $_SESSION['form_data'] = [
                'title' => $title,
                'text'  => $text,
                'tags_input' => $_POST['tags-input'] ?:'',
                'tags'  => $tags ?: [],
            ];
            return false;
        }

        //return array of values
        return compact(
            'post_id', 'title', 'text', 'tags',
            $post_id, $title, $text, $tags
        );
    }

    /**
     * Delete post ($post_id)
     * @param $post_id
     * @return bool
     */
    function delete_post($post_id): bool
    {
        global $db;
        $query = $db->prepare("
            DELETE FROM posts
            WHERE id = :post_id
        ");
        $delete = $query->execute(['post_id' => $post_id]);
        return $delete;
    }
