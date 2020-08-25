<?php

$odd = 1;
$rights = ['admin', 'editor', 'user'];
$page_title = 'deLetE sOme postS';

include_once "_partials/header.php";

//Check if the user is logged in and is editor
validateUser($is_editor);

try {
    $results = get_users();
}
catch(PDOException $e) {
    $results = [];
}

?>
    <h1 class="page-header">alL useRs InFo</h1>

        <table>
            <thead>
            <tr>
                <th> User name </th>
                <th> User email </th>
                <th> Number of posts </th>
                <th> Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $user) :?>
                <tr class="<?=($odd == 1? 'odd' : '')?> user_row-<?=$user['id'] ?>" id="user_row-<?=$user['id'] ?>">
                    <td> <?=plain($user['nickname'])?> </td>
                    <td> <?=plain($user['email'])?> </td>
                    <td> <?php $number_of_posts = get_number_of_users_posts($user['id']); echo $number_of_posts;?></td>
                    <td>
                        <div class="delete-links">
                            <a href="<?= get_editors_link( $logged_in, 'delete-users-posts', $user['id'])?>"
                               class="btn btn-xs delete-link">
                                <small> &times delete all user's posts</small></a>
                        </div>
                    </td>
                    <?php $odd = ($odd + 1) % 2; ?>
                </tr>
            <?php endforeach ?>
            </tbody>
            <tfoot>

            </tfoot>
        </table>
    </form>


<?php include_once "_partials/footer.php" ?>