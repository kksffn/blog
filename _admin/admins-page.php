<?php

    $odd = 1;
    $rights = ['admin', 'editor', 'user'];
    $page_title = 'eDit sOme useRs';
    include_once "_partials/header.php";

    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user'])) {

        $users_ids = array_unique($_POST['user'], SORT_NUMERIC);
        foreach ($users_ids as $user_id) {
            $user_nickname = $_POST["nickname-$user_id"];
            $user_email = $_POST["email-$user_id"];
            $user_rights = $_POST["rights-$user_id"];//isset($_POST["rights-$user_id"]) ? $_POST["rights-$user_id"] : '';
            $user_ban = isset($_POST["ban-$user_id"]) ? 1 : 0;
            $data = [
                'nickname' => $user_nickname,
                'email' => $user_email,
                'ban' => $user_ban
            ];
            ($user_id == $logged_in->uid ? : $data['rights'] = $user_rights);

            $update = $auth->update_user($user_id, $data );

            if ( $update['error'] )
            {
                flash()->error( "Nezměněno! ".$update['message'] );
            }
            else
            {
                flash()->success('<strong>Successfully změněno! </strong>'. 'Nickname: '. plain($update['nickname']).
                    ', email: '. plain($update['email']).
                    (isset($update['rights']) ? ', rights: '. $update['rights'] : ''). ', ban: '. $update['ban'] );
            }
        }
        redirect(get_admins_link($logged_in,'users'));
    }

    //Check if the user is admin
    validateUser($is_admin);

    try {
        $results = get_users();
    }
    catch(PDOException $e) {
        $results = [];
    }
?>
    <h1 class="page-header">alL useRs InFo</h1>
<!------------------------------------------------TABLE with users info-------------------------------------------->
    <form action="" method="post">
            <table>
                <thead>
                    <tr>
                            <th> User name </th>
                            <th> User email </th>
                            <th> User rights </th>
                            <th> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $user) :?>
                        <tr class="<?=($odd == 1? 'odd' : '')?> user_row-<?=$user['id'] ?>" id="user_row-<?=$user['id'] ?>"
                            onchange="add_user_id_input(this)">
                            <?php foreach ($user as $key => $value) : ?>
                                <?php if ($key != 'id') : ?>
                                <td>
                                    <?php if($key == 'rights') :
                                            foreach ($rights as $right) : ?>
                                                <label>
                                                    <input type="radio" id="<?= $right ?>-<?= $user['id'] ?>"
                                                           name="rights-<?=$user['id']?>" value="<?=$right?>"
                                                           <?=($right == $value ? 'checked ' :  '')?>
                                                        <?=($user['id']==$logged_in->uid ?
                                                            'readonly class="form-muted"' : '')?>>
                                                    <?=$right?>
                                                </label>

                                            <?php endforeach ?>

                                    <?php elseif ($key == 'nickname') : ?>
                                                <input type="text" value="<?= $value?>" class="form-control"
                                                       name="nickname-<?=$user['id']?>" required>

                                    <?php elseif ($key == 'ban') : ?>
                                            <label class="ban">
                                                <input type="checkbox" name="ban-<?=$user['id']?>" <?= ($value ? ' checked ' : '')?>
                                                <?=($user['id']==$logged_in->uid ?
                                                    'readonly class="form-muted"' : '')?>>
                                                ban
                                            </label>

                                            <div class="delete-links">
                                            <a href="<?= get_admins_link( $logged_in, 'delete-user', $user['id'])?>"
                                               class="btn btn-xs delete-link">
                                                <small> &times delete user</small></a>
                                            <a href="<?= get_admins_link( $logged_in, 'delete-users-posts', $user['id'])?>"
                                               class="btn btn-xs delete-link">
                                                <small> &times delete all user's posts</small></a>
                                            </div>
                                    <?php elseif ($key == 'email') : ?>
                                            <input type="text" value="<?=$value?>"
                                            class="form-control" name="email-<?=$user['id']?>" required>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            <?php endforeach;
                            $odd = ($odd + 1) % 2;
                            ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
<!-----------------------------------submit all changes - all users in one form--------------------------------------->
                <tfoot>
                    <tr class="<?=($odd == 1? 'odd' : '')?>">
                        <td colspan="4">
                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary ">Submit all changes</button>
                                <span class="or">
                                    or <a href="<?= get_admins_link( $logged_in, 'users' ) ?>">cancel</a>
                                </span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
    </form>


    <?php include_once "_partials/footer.php" ?>