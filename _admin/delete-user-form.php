<?php

    include_once "_partials/header.php";
    if (!$is_admin) {
        flash()->error("What are you trying to do?!?!?!?!");
        redirect('/');
    }

    try {
		$user = get_user( get_segment(2) );
	}
	catch ( PDOException $e ) {
		$user = false;
	}


	if (  $user == false || !$user|| $user->id == 3) {

		flash()->error("WHAT?? You can't do that!");
		redirect(get_admins_link($logged_in, 'users'));
	}

	$page_title = 'deLeTE / ' . plain($user->nickname);
	$number_of_posts = get_number_of_users_posts($user->id);
	$number_of_comments = get_number_of_users_comments($user->id);

	if (!$number_of_posts) {
	    $number_of_posts = 'no';
    }

    if (!$number_of_comments) {
        $number_of_comments = 'no';
    }


?>
    <h1 class="page-header">deleTe?</h1>
	<section class="box">
		<form action="<?= BASE_URL ?>/_admin/delete-user.php" method="post" class="post">
			<header class="post-header">
				<h1 class="box-heading">
					Sure you wanna delete &ldquo;<?= plain($user->nickname) ?>&rdquo; forever?
				</h1>
			</header>

			<blockquote class="form-group">
				<h3>&ldquo;<?= plain($user->nickname) ?>&rdquo; (<?=plain($user->email)?>)</h3>
                <p> ...has <?= $number_of_posts ?> article<?=($number_of_posts == 1) ? '': 's' ?> and
                    <?= $number_of_comments ?> comment<?=($number_of_comments == 1) ? '': 's' ?> on our bloG.
                    <?= ($number_of_posts == 'no'? '' :
                    ($number_of_posts == 1 ? ' The article'  : ' These articles') .' and '.
                    ($number_of_comments == 1 ? 'the comment'  : 'the comments').
                    ' <strong>will not </strong>  be deleted...' ) ?>
                    </p>

			</blockquote>

			<div class="form-group">
				<input name="user_id" value="<?= $user->id ?>" type="hidden">
				<input name="number_of_posts" value="<?= $number_of_posts ?>" type="hidden">
				<input name="number_of_comments" value="<?= $number_of_comments ?>" type="hidden">
				<button type="submit" class="btn btn-primary">Delete user</button>
				<span class="or">
					    or <a href="<?= get_admins_link($logged_in, 'users' ) ?>">cancel</a>
				</span>
			</div>
		</form>
	</section>

<?php include_once "_partials/footer.php" ?>