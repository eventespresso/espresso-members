<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter() */ ?>

<?php do_action( 'bp_before_events_loop' ) ?>

<?php if ( bp_has_events( bp_ajax_querystring( 'events' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">
			<?php bp_events_pagination_count() ?>
		</div>

		<div class="pagination-links" id="member-dir-pag-top">
			<?php //bp_members_pagination_links() ?>
		</div>

	</div>

	<?php do_action( 'bp_before_directory_members_list' ) ?>

	<ul id="events-list" class="item-list">
	<?php while ( bp_events() ) : bp_the_event(); ?>

		<li>
			<div class="item-avatar">
				<a href="<?php // bp_member_permalink() ?>"><?php // bp_member_avatar() ?></a>
			</div>

			<div class="item">
				<div class="item-title">
					<a href="<?php bp_event_permalink() ?>"><?php bp_event_name() ?></a> <span class="event-attendees-count">(<?php bp_event_attendees_count() ?> attendees)</span>

					<?php  /* if ( bp_get_member_latest_update() ) : ?>

						<span class="update"> - <?php bp_member_latest_update( 'length=10' ) ?></span>

					<?php endif; */ ?>

				</div>
				
				<div class="item-meta"><span class="activity"><?php bp_event_event_dates() ?></span></div>
				
				<div class="item-event-location-information">
								
					<div class="item-event-address"><?php bp_event_address(); ?></div>
					
					<div class="item-event-venue"><?php bp_event_venue(); ?></div>
				
				</div>
				
				<div class="item-attendees"><?php echo bp_event_attendees() ?></div>
				
				

				<?php do_action( 'bp_directory_events_item' ) ?>

				<?php
				 /***
				  * If you want to show specific profile fields here you can,
				  * but it'll add an extra query for each member in the loop
				  * (only one regardless of the number of fields you show):
				  *
				  * bp_member_profile_data( 'field=the field name' );
				  */
				?>
			</div>

			<div class="action">

				<?php do_action( 'bp_directory_events_actions' ); ?>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>
	</ul>

	<?php do_action( 'bp_after_directory_events_list' ) ?>

	<?php bp_member_hidden_fields() ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">
			<?php //bp_members_pagination_count() ?>
		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">
			<?php //bp_members_pagination_links() ?>
		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no events were found.", 'buddypress' ) ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_events_loop' ) ?>
