<?php get_header() ?>

	<div id="content">
		<div class="padder">
		
		<?php global $bp; ?>

			<?php if ( event_espresso_has_event( array('event_id' => $bp->current_action) ) ) : ?>
			
			<?php event_espresso_event(); ?>

			<h3><?php event_espresso_single_event_name() ?></h3>

			<?php do_action( 'bp_before_events_detail_content' ) ?>
			
			<div><?php echo event_espresso_single_event_dates(); ?></div>
			
			<div>Venue: <?php echo event_espresso_venue_title(); ?></div>
			<div><?php echo event_espresso_venue_address(); ?></div>
			<div><?php echo event_espresso_venue_city(); ?>, <?php echo event_espresso_venue_state(); ?> <?php echo event_espresso_venue_zip(); ?></div>
			<div style="margin: 10px 0;"><?php echo event_espresso_event_show_register_button(); ?></div>
			
			<hr/>
			
			<h4>Attendees (<?php echo event_espresso_event_attendees_count(); ?>)</h4>
			
			<div class="item-attendees"><?php echo event_espresso_event_attendees() ?></div>
			
			<hr/>
			
			<h4>Event Activity</h4>
					
			<div class="activity">
				<?php if ( bp_has_activities('action=created_event,register_event&primary_id=3') ) : ?>
				 
				    <div class="pagination">
				        <div class="pag-count"><?php bp_activity_pagination_count() ?></div>
				        <div class="pagination-links"><?php bp_activity_pagination_links() ?></div>
				    </div>
				 
				    <ul id="activity-stream" class="activity-list item-list">
				 
				    <?php while ( bp_activities() ) : bp_the_activity(); ?>
				 
				        <li class="<?php bp_activity_css_class() ?>" id="activity-<?php bp_activity_id() ?>">
				 
				            <div class="activity-avatar">
				                <a href="<?php bp_activity_user_link() ?>">
				                    <?php bp_activity_avatar( 'type=full&width=100&height=100' ) ?>
				                </a>
				            </div>
				 
				            <div class="activity-content">
				 
				                <div class="activity-header">
				                    <?php bp_activity_action() ?>
				                </div>
				 
				                <?php if ( bp_get_activity_content_body() ) : ?>
				                    <div class="activity-inner">
				                        <?php bp_activity_content_body() ?>
				                    </div>
				                <?php endif; ?>
				 
				                <?php do_action( 'bp_activity_entry_content' ) ?>
				 
				            </div>
				        </li>
				 
				    <?php endwhile; ?>
				 
				    </ul>
				 
				<?php else : ?>
				    <div id="message" class="info">
				        <p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ) ?></p>
				    </div>
				<?php endif; ?>
			</div><!-- .activity -->

			<?php do_action( 'bp_after_events_detail_content' ) ?>
			<?php endif; ?>


		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>