<?php

//COMMENTS AND VIEWS
/**
 * Increase zhe number of views for the post
 * @param $id
 */
function update_views($id)
{
    global $db;
    $query = $db->prepare("
            UPDATE posts SET views = views + 1 WHERE id = :id
        ");
    $query->execute(['id'=>$id]);
}

/**
 * Return number of views for the post
 * @param $id
 * @return mixed
 */
function count_views($id)
{
    global $db;
    $query = $db->prepare("
            SELECT views as count FROM posts WHERE id = :id
        ");
    $query->execute(['id'=>$id]);
    $count = $query->fetch(PDO::FETCH_OBJ);

    return $count->count;
}

/**
 * Return nuber of comments for the post
 * @param $post_id
 * @return mixed
 */
function count_comments($post_id)
{
    global $db;
    $query = $db->prepare("
            SELECT COUNT(*) AS count FROM comments WHERE post_id=?
        ");
    $count = $query->execute([$post_id]);
    $count = $query->fetch(PDO::FETCH_OBJ);
    return $count->count;
}

/**
 * Gets all comments for the post
 * @param $post_id
 * @return array
 */
function get_comments_for_post($post_id)
{
    global $db;

    $query = $db->prepare( "
                SELECT c.text AS text, c.id AS id, c.created_at AS time, u.nickname AS nickname
                FROM comments c
                LEFT JOIN phpauth_users u ON (c.user_id = u.id)
                WHERE post_id = :post_id
            " );
    $query->execute([ 'post_id' => $post_id ]);

    if ( $query->rowCount() )
    {
        $results = $query->fetchAll( PDO::FETCH_OBJ );
    }
    else
    {
        $results = [];
    }
    return $results;
}

/**
 * Delete all comments for the post
 * @param $post_id
 */
function delete_comments_for_the_post($post_id)
{
    global $db;
    $delete = $db->prepare("
                        DELETE FROM comments
                        WHERE post_id = :post_id
                    ");
    $delete->execute([
        'post_id' => $post_id
    ]);
}

/**
 * Validate, sanitize comment - to be safe
 * @return array|false
 * @throws \Tamtamchik\SimpleFlash\Exceptions\FlashTemplateNotFoundException
 */
function validate_comment()
{
    //sanitize & validate inputs; FILTER_FLAG_NO_ENCODE_QUOTES uloží normální apostrofy a uvozovky bez konverze na bezpečné znaky...
    $comment_text = filter_input( INPUT_POST, 'comment_text', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    $link = filter_input(INPUT_POST,'post_link', FILTER_SANITIZE_URL);

    //id required & has to be int
    $post_id = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
    if ( ! $post_id)
    {
        flash() -> error("What are you trying to do?!?!?!?!");
    }
    //text of the comment required
    if (! $comment_text = trim($comment_text))
    {
        flash() -> error('MATRIX ERROR!!! You don\'t have any <strong>text</strong> in your comment!');
    }

    $comment_text = plain($comment_text);
    $comment_text = filter_url($comment_text);
    $comment_text = add_paragraphs($comment_text);

    //Pokračujeme, pouze pokud nejsou chyby
    if (flash() -> hasMessages())
    {
        $_SESSION['form_data'] = [
            'comment_text'  => $comment_text,
        ];
        return false;
    }

    //return array of values
    return compact(
        'post_id', 'comment_text', 'link',
        $post_id, $comment_text, $link
    );
}

/**
 * Insert comment into DB
 * @param $uid
 * @param string $post_id
 * @param $comment_text
 * @return mixed
 */
function add_comment($uid, $post_id, $comment_text)
{
    $state['error'] = true;
    global $db;
    $query = $db->prepare("
                INSERT INTO comments
                    ( user_id, post_id, text )
                VALUES
                    ( :uid, :post_id, :text )
            ");

    $insert = $query->execute([
        'uid' => $uid,
        'post_id' => $post_id,
        'text' => $comment_text
    ]);

    $state['error'] = false;
    $state['message'] = 'Your comment was successfully added.';
    $state['id'] = $db->lastInsertId();
    return $state;
}