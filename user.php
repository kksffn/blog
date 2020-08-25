<?php

	$user = get_user( get_segment(2) );

	try {
		$results = get_posts_by_user( $user->id);
	}
	catch ( PDOException $e ) {
		$results = [];
	}

    $page_title = plain( $user->nickname) . "'s pOstS";
	include_once "_partials/header.php";

?>
    <h1 class="page-header"> <?= plain($user->nickname)?>'s pOstS </h1>
        <section class="box post-list">
            <h1 class="box-heading text-muted"><small>by</small> <?= plain( $user->nickname ).
                "<small> (".plain($user->email).")</small> " ?>
            </h1>

        <!--------------------------------------section of posts----------------------->
				<?php include "_partials/post-teaser.php"?>
	    </section>

<?php include_once "_partials/footer.php" ?>