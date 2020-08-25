<?php
    /**
     * Get All Tags
     *
     * Grab tags, if we wanna add them to like an input/edit form
     * If $post_id is provided, we mark which tags belong to this user
     *
     * @param   integer  $post_id
     * @return  array    list of tags
     */
    function get_all_tags( $post_id = 0 )
    {
        global $db;

        $query = $db->query("
                    SELECT * FROM tags
                    ORDER BY tag ASC
                ");

        $results = $query->rowCount() ? $query->fetchAll( PDO::FETCH_OBJ ) : [];

        // grab tags for this user
        if ( $post_id )
        {
            $query = $db->prepare("
                        SELECT t.id FROM tags t
                        JOIN posts_tags pt ON t.id = pt.tag_id
                        WHERE pt.post_id = :pid
                    ");

            $query->execute([ $post_id ]);

            // mark tags that this user has as checked
            if ( $query->rowCount() )
            {
                $tags_for_post = $query->fetchAll( PDO::FETCH_COLUMN );

                foreach ( $results as $key => $tag )
                {
                    $checked = false;
                    if ( in_array( $tag->id, $tags_for_post ) ) {
                        $checked = true;
                    }

                    $results[$key]->checked = $checked;
                }
            }
        }

        return $results;
    }

    /**
     * Finds out whether the tags were changed for post in edit form
     *
     * @param $post_id
     * @param $tags
     * @return bool
     */
    function changes_in_tags($post_id, $tags)
    {
        global $db;
        $old_tags = $db->prepare("SELECT * FROM posts_tags WHERE post_id = :post_id");
        $old_tags->execute(['post_id' => $post_id]);
        $old_tags = $old_tags->fetchAll(PDO::FETCH_ASSOC);

        //Funguje i pro prázdnou množinu!
        if (count($tags) > count($old_tags)) {

            return  true;
        } else {
            foreach ($old_tags as $old_tag) {
                if (!in_array($old_tag['tag_id'], $tags)) {
                    return  true;
                }
            }
        }
        return false;
    }

    /**
     * Delete all tags for post
     * @param $post_id
     */
    function delete_tags_for_post($post_id)
    {
        global $db;
        $delete_tags = $db->prepare("
                            DELETE FROM posts_tags
                            WHERE post_id = :post_id
                        ");
        $delete_tags->execute([
            'post_id' => $post_id
        ]);
    }

    /**
     * Insert tags ($tags) for a post ($post_id) into DB using one query
     * @param $tags
     * @param $post_id
     *
     */
    function insert_tags_for_post($tags, $post_id)
    {
        global $db;
        $query = "INSERT INTO posts_tags VALUES ";
        $execution = [];
        $i = 0;
        foreach ($tags as $tag_id) {
            $query .= "(:post_id_$i, :tag_id_$i), ";
            $execution["post_id_$i"] = $post_id;
            $execution["tag_id_$i"] = $tag_id;
            $i++;
        }
        $query = substr($query, 0, -2);
        $insert_tags = $db->prepare($query);
        $insert_tags->execute($execution);
    }

    /**
     * Count posts with the tag $tag_id
     * @param $tag_id
     * @return mixed
     */
    function get_number_of_posts_for_tag($tag_id)
    {
        global $db;
        $query = $db->prepare("
                SELECT COUNT(*) AS count FROM posts_tags WHERE tag_id=?
            ");
        $count = $query->execute([$tag_id]);
        $count = $query->fetch(PDO::FETCH_OBJ);
        return $count->count;
    }

    /**
     * Add new tag into DB
     * @param $tag - already sanitized, lowercase
     * @return mixed
     */
    function add_new_tag($tag)
    {

        $state['error'] = true;
        global $db;
        $query = $db->prepare("
                SELECT COUNT(*) AS count FROM tags WHERE tag = :tag        
            ");
        $count = $query->execute(['tag' => $tag]);
        $count = $query->fetch(PDO::FETCH_OBJ);
        if ($count->count <>0)
        {

            $state['message'] = 'Couldn\'t create the tag. ' .$tag. ' already exists!';
            return $state;
        }

        $query = $db->prepare("
                INSERT INTO tags (tag) VALUES (:tag)
            ");
        $query->execute(['tag' => $tag]);


        $state['error'] = false;
        $state['message'] = 'New tag '.$tag. ' successfully added.';
        $state['id'] = $db->lastInsertId();
        return $state;

    }

    /**
     * Delete tag from DB and from posts
     * @param $id
     * @return mixed
     */
    function delete_tag($id)
    {
        $state['error'] = true;

        global $db;
        $query = $db->prepare("
                DELETE FROM tags WHERE id = :id        
            ");

        $query2 = $db->prepare("
                DELETE FROM posts_tags WHERE tag_id = :id
            ");
        $delete = $query->execute(['id' => $id]);
        $delete2 = $query2->execute(['id' => $id]);

        if (!$delete || !$delete2) {
            $state['message'] = "Ooops, něco se pokazilo.";
            return $state;
        }

        $state['error'] = false;
        $state['message'] = 'Tag deleted!';
        return $state;

    }

    /**
     * Change tag name in DB
     * @param $id
     * @param $tag
     * @return mixed
     */
    function edit_tag($id, $tag)
    {
        $state['error'] = true;

        global $db;

        $query = $db->prepare("
                SELECT COUNT(*) AS count FROM tags WHERE tag=:tag AND id<>:id
            ");
        $count = $query->execute(['tag'=>$tag, 'id'=>$id]);
        $count = $query->fetch(PDO::FETCH_OBJ);
        if ( $count->count <> 0 ) {
            $state['message'] = "The tag $tag already existuje!";
            return $state;
        };


        $query = $db->prepare("
                    UPDATE tags SET tag =:tag WHERE id = :id        
                ");
        $update = $query->execute(['tag' => $tag, 'id' => $id]);

        if (!$update) {
            $state['message'] = "Ooops, něco se pokazilo.";
            return $state;
        }

        $state['error'] = false;
        $state['message'] = "Tag updated to $tag!";
        return $state;
    }


    /**
     * Return id for the tag $tag
     * @param $tag
     * @return mixed
     */
    function get_id_for_tag($tag)
    {
        $state['error'] = true;
        global $db;
        $query = $db->prepare("
                SELECT id AS id FROM tags WHERE tag =:tag
            ");
        $query->execute(['tag'=>$tag]);
        $id = $query->fetch(PDO::FETCH_OBJ);
        if ( !$id->id) {
            $state['message'] = "Ooops, něco se pokazilo.";
            return $state;
        }
        $state['error'] = false;
        return $id->id;
    }

    /**
     * Return array of ids for the new tags aded by user
     * @param $input
     * @return array
     */
    function get_new_tags_ids($input)
    {
        $new_tags_ids = array();

        $new_tags = explode(',', $input);


        foreach ($new_tags as $new_tag) {

            $new_tag = trim(plain(filter_var($new_tag, FILTER_SANITIZE_STRING )));

            if (!$new_tag || strlen($new_tag) > 30 ) { // Nebudeme přidávat prázdný řetězec nebo delší než 30 znaků!
                continue;
            }else {
                $add = add_new_tag($new_tag);
            }

            if ($add['error']) {
                if ($add['message'] == 'Couldn\'t create the tag. ' . $new_tag . ' already exists!') {
                    $id = get_id_for_tag($new_tag);
                    if (!$id['error']) {
                        $new_tags_ids[] = $id;
                    }
                }
            } else {
                $new_tags_ids[] = $add['id'];
            }
        }
        return $new_tags_ids;
    }
