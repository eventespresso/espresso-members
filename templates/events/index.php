<?php get_header() ?>

	<div id="content">
		<div class="padder">

		<form action="" method="post" id="events-directory-form" class="dir-form">

			<h3><?php _e( 'Events Directory', 'event_espresso' ) ?></h3>

			<?php do_action( 'bp_before_directory_events_content' ) ?>

			<div id="members-dir-search" class="dir-search">
				<?php bp_directory_members_search_form() ?>
			</div><!-- #members-dir-search -->

			<div class="item-list-tabs">
				<ul>
					<li class="selected" id="events-all"><a href="<?php bp_root_domain() ?>"><?php printf( __( 'All Events (%s)', 'buddypress' ), event_espresso_get_total_event_count() ) ?></a></li>

				</ul>
			</div><!-- .item-list-tabs -->

			<div id="members-dir-list" class="events dir-list">
				<?php include( 'events-loop.php' ); ?>
			</div><!-- #members-dir-list -->

			<?php do_action( 'bp_directory_events_content' ) ?>

			<?php wp_nonce_field( 'directory_events', '_wpnonce-event-filter' ) ?>

			<?php do_action( 'bp_after_directory_events_content' ) ?>

		</form><!-- #members-directory-form -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>