<?php

	if (! get_segment(2)) {
        show_404();
    }

    try {
        $user = get_user( get_segment(2) );
    }
    catch ( PDOException $e ) {
        $user = false;
    }

    if (  $user == false || !$user) {

        flash()->error("WHAT?? You can't do that!");
        redirect(get_admins_link($logged_in, 'users'));
    }

    $delete_form = get_segment(1);
    $page_title = 'deLeTE posts / ' . plain($user->nickname);
    $number_of_posts = get_number_of_users_posts($user->id);



	try {
		$results = get_posts_by_user( $user->uid );
	}
	catch ( PDOException $e ) {
		$results = [];
	}

    $page_title = "deleTe / " . plain( $user->nickname) . "'s pOstS";
	include_once "_partials/header.php";

?>
    <h1 class="page-header"> DeleTe <?= plain($user->nickname)?>'s pOstS </h1>
	<section class="box post-list">
		<h1 class="box-heading text-muted"><small>by</small> <?= plain( $user->nickname ).
            "<small> (".plain($user->email).")</small> " ?></h1>


        <form action="<?=
            ($is_admin ? get_admins_link($logged_in, 'delete-users-posts', $user->uid)
            : get_editors_link($logged_in, 'delete-users-posts', $user->uid)) ?>"
              id="delete-form" method="post">

<!--------------------------------------section of posts----------------------->

		<?php include "_partials/post-teaser.php" ?>

<!--------------------------------------buttons for form----------------------->
            <hr>
        <div class="form-group">
            <?php if (count($results)) : ?>
                <input name="user_id" value="<?=$user->uid?>" type="hidden">
                <button type="submit" class="btn btn-primary"
                        onclick="return askBeforeDelete('all these posts')">Delete all posts</button>
            <?php endif; ?>
            <span class="or">
                <?php if (count($results)) : ?>or<?php endif; ?>

                <?php if ($is_admin) : ?>
                    <a href="<?= get_admins_link($logged_in, 'users' ) ?>">cancel</a>
                <?php elseif ($is_editor) : ?>
                    <a href="<?= get_editors_link($logged_in, 'editor' ) ?>">cancel</a>
                <?php endif; ?>
            </span>
        </div>
        </form>
	</section>

<?php include_once "_partials/footer.php" ?>