<?php
//Build the user admin menu
//if (get_option('events_members_active') == 'true') {
    add_action('show_user_profile', 'event_espresso_show_extra_profile_fields');
    add_action('edit_user_profile', 'event_espresso_show_extra_profile_fields');
    //Show the user admin menu in the side menu
    add_action('admin_menu', 'add_member_event_espressotration_menus');

    function event_espresso_show_extra_profile_fields($user) {
        global $espresso_premium;
        if ($espresso_premium != true)
            return;
        ?>

<h3>
	<?php _e('Events Profile Information', 'event_espresso'); ?>
</h3>
<a name="event_espresso_profile" id="event_espresso_profile"></a>
<table class="form-table">
	<tr>
		<th><label for="event_espresso_address">
				<?php _e('Address/Street/Number', 'event_espresso'); ?>
			</label></th>
		<td><input type="text" name="event_espresso_address" id="event_espresso_address" value="<?php echo esc_attr(get_the_author_meta('event_espresso_address', $user->ID)); ?>" class="regular-text" />
			<br />
			<span class="description">
			<?php _e('Please enter your Address/Street/Number.', 'event_espresso'); ?>
			</span></td>
	</tr>
	<tr>
		<th><label for="event_espresso_address2">
				<?php _e('Address 2', 'event_espresso'); ?>
			</label></th>
		<td><input type="text" name="event_espresso_address2" id="event_espresso_address2" value="<?php echo esc_attr(get_the_author_meta('event_espresso_address2', $user->ID)); ?>" class="regular-text" />
			<br />
			<span class="description">
			<?php _e('Optional', 'event_espresso'); ?>
			</span></td>
	</tr>
	<tr>
		<th><label for="event_espresso_city">
				<?php _e('City/Town/Village', 'event_espresso'); ?>
			</label></th>
		<td><input type="text" name="event_espresso_city" id="event_espresso_city" value="<?php echo esc_attr(get_the_author_meta('event_espresso_city', $user->ID)); ?>" class="regular-text" />
			<br />
			<span class="description">
			<?php _e('Please enter your City/Town/Village.', 'event_espresso'); ?>
			</span></td>
	</tr>
	<tr>
		<th><label for="event_espresso_state">
				<?php _e('State/County/Province', 'event_espresso'); ?>
			</label></th>
		<td><input type="text" name="event_espresso_state" id="event_espresso_state" value="<?php echo esc_attr(get_the_author_meta('event_espresso_state', $user->ID)); ?>" class="regular-text" />
			<br />
			<span class="description">
			<?php _e('Please enter your State/County/Province.', 'event_espresso'); ?>
			</span></td>
	</tr>
	<tr>
		<th><label for="event_espresso_zip">
				<?php _e('Zip/Postal Code', 'event_espresso'); ?>
			</label></th>
		<td><input type="text" name="event_espresso_zip" id="event_espresso_zip" value="<?php echo esc_attr(get_the_author_meta('event_espresso_zip', $user->ID)); ?>" class="regular-text" />
			<br />
			<span class="description">
			<?php _e('Please enter your Zip/Postal Code.', 'event_espresso'); ?>
			</span></td>
	</tr>
	<tr>
		<th><label for="event_espresso_country">
				<?php _e('Country', 'event_espresso'); ?>
			</label></th>
		<td><input type="text" name="event_espresso_country" id="event_espresso_country" value="<?php echo esc_attr(get_the_author_meta('event_espresso_country', $user->ID)); ?>" class="regular-text" />
			<br />
			<span class="description">
			<?php _e('Please enter your Country.', 'event_espresso'); ?>
			</span></td>
	</tr>
	<tr>
		<th><label for="event_espresso_phone">
				<?php _e('Phone Number', 'event_espresso'); ?>
			</label></th>
		<td><input type="text" name="event_espresso_phone" id="event_espresso_phone" value="<?php echo esc_attr(get_the_author_meta('event_espresso_phone', $user->ID)); ?>" class="regular-text" />
			<br />
			<span class="description">
			<?php _e('Please enter your Phone Number.', 'event_espresso'); ?>
			</span></td>
	</tr>
</table>
<?php
    }

    add_action('personal_options_update', 'event_espresso_extra_profile_fields');
    add_action('edit_user_profile_update', 'event_espresso_extra_profile_fields');

    function event_espresso_extra_profile_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        update_user_meta($user_id, 'event_espresso_address', $_POST['event_espresso_address']);
        update_user_meta($user_id, 'event_espresso_address2', $_POST['event_espresso_address2']);
        update_user_meta($user_id, 'event_espresso_city', $_POST['event_espresso_city']);
        update_user_meta($user_id, 'event_espresso_state', $_POST['event_espresso_state']);
        update_user_meta($user_id, 'event_espresso_zip', $_POST['event_espresso_zip']);
        update_user_meta($user_id, 'event_espresso_country', $_POST['event_espresso_country']);
        update_user_meta($user_id, 'event_espresso_phone', $_POST['event_espresso_phone']);
    }

//}

function espresso_members_installed(){
	return true;
}
function event_espresso_get_current_user_role() {
    global $espresso_premium;
    if ($espresso_premium != true)
        return;
    global $current_user;
    get_currentuserinfo();
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    return $user_role;
}

function add_member_event_espressotration_menus() {
    global $espresso_premium;
    if ($espresso_premium != true)
        return;
    add_users_page(__('My Events', 'event_espresso'), __('My Events', 'event_espresso'), event_espresso_get_current_user_role(), 'my-events', 'event_espresso_my_events');
}

function event_espresso_user_login() {
    global $espresso_premium;
    if ($espresso_premium != true)
        return;
    $member_options = get_option('events_member_settings');

    //Get the member login page
    if ($member_options['login_page'] != '') {
        $login_page = $member_options['login_page'];
    } else {
        $login_page = get_option('siteurl') . '/wp-login.php';
    }

    //Get the member regsitration page
    if ($member_options['register_page'] != '') {
        $register_page = $member_options['register_page'];
    } else {
        $register_page = get_option('siteurl') . '/wp-login.php?action=register';
    }
    echo '<h3>' . __('You are not logged in.', 'event_espresso') . '</h3>';
    echo '<p>' . __('Before you can reserve a spot, you must register.', 'event_espresso') . '</p>';
    echo '<p>If you are a returning user please <a href="' . $login_page . '?redirect_to=' . urlencode(event_espresso_cur_pageURL()) . '">' . __('Login', 'event_espresso') . '</a></p>';
    if (get_option('users_can_register')) {
        echo '<p>' . __('New users please', 'event_espresso') . ' <a href="' . $register_page . '">' . __('Register', 'event_espresso') . '</a></p>';
    } else {
        _e('Member registration is closed for this site. Please contact the site owner.', 'event_espresso');
    }
}

function event_espresso_member_only($member_only = 'N') {
    global $espresso_premium;
    if ($espresso_premium != true)
        return;
    ?>
<p>
	<?php
    _e('Member only event? ', 'event_espresso');
    $values = array(
        array('id' => 'N', 'text' => __('No', 'event_espresso')),
        array('id' => 'Y', 'text' => __('Yes', 'event_espresso')));
    echo select_input('member_only', $values, $member_only);
    ?>
</p>
<?php
}

function event_espresso_user_login_link() {
    global $espresso_premium;
    if ($espresso_premium != true)
        return;
    //Get the member login page
    if ($member_options['login_page'] != '') {
        $login_page = $member_options['login_page'];
    } else {
        $login_page = get_option('siteurl') . '/wp-login.php';
    }
    echo '<a href="' . $login_page . '?redirect_to=' . urlencode(event_espresso_cur_pageURL()) . '">' . __('Login', 'event_espresso') . '</a>';
}

//Add the ids of the event, user, and attendee to the db
function event_espresso_add_user_to_event($event_id, $userid, $attendee_id) {
    global $espresso_premium;
    if ($espresso_premium != true)
        return;
    global $wpdb;
	global $bp;
    $user_role = event_espresso_get_current_user_role();
    $sql = "INSERT INTO " . EVENTS_MEMBER_REL_TABLE . "(event_id, user_id, attendee_id, user_role) VALUES ('" . $event_id . "', '" . $userid . "', '" . $attendee_id . "', '" . $user_role . "')";
    $wpdb->query($wpdb->prepare($sql));


	// If BuddyPress is installed, add an item to the activity stream
	if ( function_exists('bp_is_active') && bp_is_active( 'activity' ) ) {

		// get title of event to add into activity
		$event_name = $wpdb->get_var( $wpdb->prepare( "SELECT event_name FROM " . EVENTS_DETAIL_TABLE . " WHERE id = " .$event_id ) );

		$component = "event_espresso";
		$type = "register_event";

		$entry = array(
			'action' =>  sprintf( __( '%1$s registered for %2$s', 'event_espresso' ), bp_core_get_userlink( $bp->loggedin_user->id ), $event_name ),
			'component' => $component,
			'type' => $type,
			'primary_link' => bp_core_get_user_domain( $user_id ),
			'user_id' => $bp->loggedin_user->id,
			'item_id' => $event_id,
			'secondary_item_id' => $event_id
		);


		return bp_activity_add( apply_filters( 'event_espresso_record_activity', $entry ) );

	}
}

/*
  Returns the price of an event for members
 *
 * @params string $date
 */
//if (!function_exists('event_espresso_get_price')) {

    function event_espresso_get_price($event_id) {
        global $espresso_premium;
        if ($espresso_premium != true)
            return;
        $org_options = get_option('events_organization_settings');
        global $wpdb;
        if (is_user_logged_in()) {
            $prices = $wpdb->get_results("SELECT event_cost, member_price FROM " . EVENTS_PRICES_TABLE . " WHERE event_id='" . $event_id . "' ORDER BY id ASC LIMIT 1");
        } else {
            $prices = $wpdb->get_results("SELECT event_cost FROM " . EVENTS_PRICES_TABLE . " WHERE event_id='" . $event_id . "' ORDER BY id ASC LIMIT 1");
        }
        foreach ($prices as $price) {
            if ($wpdb->num_rows == 1) {
                $member_price = empty($price->member_price) ? $price->event_cost : $price->member_price;
                if (empty($member_price)) {
                    $event_cost = __('Free Event', 'event_espresso');
                } else {
                    $event_cost = $org_options['currency_symbol'] . $member_price;
                    $event_cost .= '<input type="hidden"name="event_cost" value="' . $member_price . '">';
                }
            } else if ($wpdb->num_rows == 0) {
                $event_cost = __('Free Event', 'event_espresso');
            }
        }

        return $event_cost;
    }

//}

function event_espresso_member_only_pricing($event_id = 'NULL') {
    global $espresso_premium;
    if ($espresso_premium != true)
        return;
    ?>
<fieldset id="members-pricing">
	<legend>
	<?php _e('Member Pricing', 'event_espresso'); ?>
	</legend>
	<?php
        if ($event_id == 0) {
            event_espresso_member_pricing_new();
        } else {
            event_espresso_member_pricing_update($event_id);
        }
        ?>
	<p>
		<input class="button" type="button" value="<?php _e('Add A Member Price', 'event_espresso'); ?>" onclick="addMemberPriceInput('dynamicMemberPriceInput');">
	</p>
</fieldset>
</td>
<?php
}

if (!function_exists('event_espresso_member_pricing_update')) {
    function event_espresso_member_pricing_update($event_id) {
        global $espresso_premium;
        if ($espresso_premium != true)
            return;
        $org_options = get_option('events_organization_settings');
        global $wpdb;
        $member_price_counter = 1;
        ?>
<ul id="dynamicMemberPriceInput">
	<?php
        $member_prices = $wpdb->get_results("SELECT member_price, member_price_type FROM " . EVENTS_PRICES_TABLE . " WHERE event_id = '" . $event_id . "' ORDER BY id");
        foreach ($member_prices as $member_price) {
            echo '<li><label for="add-member-name-"' . $member_price_counter++ . '">' . __('Name', 'event_espresso') . ' ' . $member_price_counter++ . ':</label><input id="add-member-name-' . $member_price_counter++ . '" size="10"  type="text" name="member_price_type[]" value="' . $member_price->member_price_type . '"> ';
            echo '<label for="add-member-price-' . $member_price_counter++ . '">' . __('Price', 'event_espresso') . ': ' . $org_options['currency_symbol'] . '</label><input id="add-member-price-' . $member_price_counter++ . '" size="5"  type="text" name="member_price[]" value="' . $member_price->member_price . '">';
            echo '<img class="remove-item" title="' . __('Remove this Attendee', 'event_espresso') . '" onclick="this.parentNode.parentNode.removeChild(this.parentNode);" src="' . EVENT_ESPRESSO_PLUGINFULLURL . 'images/icons/remove.gif" alt="' . __('Remove Attendee', 'event_espresso') . '" />';

            echo '</li>';
        }
        ?>
</ul>
<p>
	<?php _e('(enter 0.00 for free events, enter 2 place decimal i.e. ' . $org_options['currency_symbol'] . '7.00)', 'event_espresso'); ?>
</p>
<p>
	<?php _e('<span class="important">Note:</span> A non-member price MUST be entered for each row, even if this is a member only event.', 'event_espresso'); ?>
</p>
<script type="text/javascript">

            //Dynamic form fields
            var member_price_counter = '<?php echo $member_price_counter++ ?>';
            function addMemberPriceInput(divName){
                var newdiv = document.createElement('li');
                newdiv.innerHTML = "<label for='add-member-name-" + (member_price_counter) + "'><?php _e('Name', 'event_espresso'); ?> " + (member_price_counter) + ": </label><input id='add-member-name-" + (member_price_counter) + "' type='text' size='10' name='member_price_type[]'><label for='add-member-price-" + (member_price_counter) + "'> <?php _e('Price', 'event_espresso'); ?>: <?php echo $org_options['currency_symbol'] ?></label><input id='add-member-price-" + (member_price_counter) + "' type='text' size='5' name='member_price[]'> <?php echo "<img  class='remove-item' onclick='this.parentNode.parentNode.removeChild(this.parentNode);' title='" . __('Remove this Attendee', 'event_espresso') . "' src='" . EVENT_ESPRESSO_PLUGINFULLURL . "images/icons/remove.gif' alt='" . __('Remove Attendee', 'event_espresso') . "' />" ?>";
                document.getElementById(divName).appendChild(newdiv);
                member_price_counter++;
            }
        </script>
<?php
    }
}

if (!function_exists('event_espresso_member_pricing_new')) {
    function event_espresso_member_pricing_new() {
        global $espresso_premium, $org_options;
        if ($espresso_premium != true)
            return;
        $member_price_counter = 1;
        ?>
<ul id="dynamicMemberPriceInput">
	<li>
		<label for="add-member-name-<?php echo $member_price_counter ?>">
			<?php _e('Name ', 'event_espresso'); ?>
			<?php echo $member_price_counter++ ?>:</label>
		<input size="10" id="add-member-name-<?php echo $member_price_counter ?>" type="text"  name="member_price_type[]">
		<label for="add-member-price-<?php echo $member_price_counter ?>">
			<?php _e('Price', 'event_espresso'); ?>
			:<?php echo $org_options['currency_symbol'] ?></label>
		<input size="5" id="add-member-price-<?php echo $member_price_counter ?>" type="text"  name="member_price[]">
		<img class="remove-item" title="<?php echo __('Remove this Attendee', 'event_espresso') ?>" onclick="this.parentNode.parentNode.removeChild(this.parentNode);" src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL ?>images/icons/remove.gif" alt="<?php echo __('Remove Attendee', 'event_espresso') ?>" /> </li>
</ul>
<p>
	<?php _e('(enter 0.00 for free events, enter 2 place decimal i.e. 7.00)', 'event_espresso'); ?>
</p>
<p>
	<?php _e('<span class="important">Note:</span> A non-member price MUST be entered, even if this is a member only event.', 'event_espresso'); ?>
</p>
<script type="text/javascript">

            //Dynamic form fields
            var member_price_counter = <?php echo $member_price_counter++ ?>;
            function addMemberPriceInput(divName){
                var newdiv = document.createElement('li');
                newdiv.innerHTML = "<label for='add-member-name" + (member_price_counter) + "'><?php _e('Name', 'event_espresso'); ?> " + (member_price_counter) + ": </label><input id='add-member-name-" + (member_price_counter) + "' type='text' size='10' name='member_price_type[]'><label for='add-member-price-" + (member_price_counter) + "'> <?php _e('Price', 'event_espresso'); ?>: <?php echo $org_options['currency_symbol'] ?></label><input id='add-member-price-" + (member_price_counter) + "' type='text' size='5' name='member_price[]'> <?php echo "<img class='remove-item' onclick='this.parentNode.parentNode.removeChild(this.parentNode);' title='" . __('Remove this Attendee', 'event_espresso') . "'  src='" . EVENT_ESPRESSO_PLUGINFULLURL . "images/icons/remove.gif' alt='" . __('Remove Attendee', 'event_espresso') . "' />" ?>";
                document.getElementById(divName).appendChild(newdiv);
                member_price_counter++;
            }
        </script>
</li>
<?php
    }
}

//Creates dropdowns if multiple prices are associated with an event
//if (!function_exists('event_espresso_member_price_dropdown')) {

	function event_espresso_price_dropdown($event_id, $atts = '' ) {

		//Attention:
		//This is a copy of a core function. Any changes made here should be added to the core function of the same name

		global  $wpdb, $org_options, $espresso_premium;
		if ($espresso_premium != true)
            return;
		
		empty($atts) ? '' : extract($atts);
		//Debug
		//echo "<pre>atts -".print_r($atts,true)."</pre>";

		$show_label = $show_label == '' ? 1 : $show_label;
		$multi_reg = $multi_reg == '' ? 0 : $multi_reg;
		$option_name = $option_name == '' ? 'price_option' : $option_name;

		//Default values
		$html = '';
		$early_bird_message = '';
		$surcharge = '';
		$label = $label == '' ? __('Choose an Option: ', 'event_espresso') : $label;

		//Will make the name an array and put the time id as a key so we know which event this belongs to
        $multi_name_adjust = $multi_reg == 1 ? "[$event_id]" : '';

		//Gets the surcharge text
		$surcharge_text = isset($org_options['surcharge_text']) ? $org_options['surcharge_text'] : __('Surcharge', 'event_espresso');

		//Initial price query
		//If the user is looged in, create a special query to get the member price
        if (is_user_logged_in()) {
            $sql = "SELECT id, event_cost, surcharge, surcharge_type, member_price, member_price_type, price_type FROM " . EVENTS_PRICES_TABLE . " WHERE event_id='" . $event_id . "' ORDER BY id ASC";
        } else {
           $sql = "SELECT id, event_cost, surcharge, surcharge_type, price_type FROM " . EVENTS_PRICES_TABLE . " WHERE event_id='" . $event_id . "' ORDER BY id ASC";
        }
		
		$prices = $wpdb->get_results($sql);

		//If more than one price was added to an event, we need to create a drop down to select the price.
		if ($wpdb->num_rows > 1) {


			//Create the label for the drop down
			$html .= $show_label == 1 ? '<label for="price_option">' . $label . '</label>' : '';

			//Create a dropdown of prices
			$html .= '<select name="'.$option_name . $multi_name_adjust . '" id="price_option-' . $event_id . '">';
			
			if ( is_admin() )
				$html .= '<option ' . $selected . ' value="">None</option>';

			foreach ($prices as $price) {

				//Create the member price
				if (is_user_logged_in()) {
					$member_price = $price->member_price;
					$member_price_type = $price->member_price_type;
				} else {
					$member_price = $price->event_cost;
					$member_price_type = $price->price_type;
				}

				//Check for Early Registration discount
				if (early_discount_amount($event_id, $member_price) != false) {
					$early_price_data = array();
					$early_price_data = early_discount_amount($event_id, $member_price);
					$member_price = $early_price_data['event_price'];
					$early_bird_message = __(' Early Pricing', 'event_espresso');
				}

				//Calculate the surcharge
				if ($price->surcharge > 0 && $member_price > 0.00) {
					$surcharge = " + {$org_options['currency_symbol']}{$price->surcharge} " . $surcharge_text;
					if ($price->surcharge_type == 'pct') {
						$surcharge = " + {$price->surcharge}% " . $surcharge_text;
					}
				}

				//Using price ID
				//If the price id was passed to this function, we need need to select that price.
				$selected = $current_value == $price->id ? 'selected="selected" ' : '';
				
				if ( !empty($selected_price_type) ){
					if ( $member_price_type == $selected_price_type) {
						$selected  = 'selected="selected" ';
					}
				}

				//Create the drop down options
				$html .= '<option ' . $selected . ' value="' . $price->id . '|' . $member_price_type . '">' . $member_price_type . ' (' . $org_options['currency_symbol'] . number_format($member_price, 2, '.', '') . $early_bird_message . ') ' . $surcharge . ' </option>';

            }

			//Create a hidden field so that we know the price dropdown was used
            $html .= '</select><input type="hidden" name="price_select" id="price_select-' . $event_id . '" value="true">';

		//If a single price was added to an event, then create the price display and hidden fields to hold the additional information.
		} else if ($wpdb->num_rows == 1) {
			if ( is_admin() ){
				if ( isset($_REQUEST['event_admin_reports']) && $_REQUEST['event_admin_reports'] == 'edit_attendee_record' ){
					$member_label = __('Member', 'event_espresso').' ';
				}
			}
            foreach ($prices as $price) {

				//Convert to the member price if the user is logged in
                if (is_user_logged_in()) {
                    $member_price = $price->member_price;
                } else {
                    $member_price = $price->event_cost;
                }

                //Check for Early Registration discount
				if (early_discount_amount($event_id, $member_price) != false) {
					$early_price_data = array();
					$early_price_data = early_discount_amount($event_id, $member_price);
					$member_price = $early_price_data['event_price'];
					$early_bird_message = sprintf(__(' (including %s early discount) ', 'event_espresso'), $early_price_data['early_disc']);
				}

				//Calculate the surcharge
				if ($price->surcharge > 0 && $member_price > 0.00) {
					$surcharge = " + {$org_options['currency_symbol']}{$price->surcharge} " . $surcharge_text;
					if ($price->surcharge_type == 'pct') {
						$surcharge = " + {$price->surcharge}% " . $surcharge_text;
					}
				}

				//Create the single price display
				$html .= '<span class="event_price_label">' . $member_label . __('Price: ', 'event_espresso') . '</span> <span class="event_price_value">' . $org_options['currency_symbol'] . number_format($member_price, 2, '.', '') . $early_bird_message . $surcharge . '</span>';

				//Create hidden fields to pass additional information to the add_attendees_to_db function
				$html .= '<input type="hidden" name="price_id" id="price_id-' . $event_id . '" value="' . $price->id . '">';
				$html .= '<input type="hidden" name="event_cost' . $multi_name_adjust . '" id="event_cost-' . $price->id . '" value="' . number_format($price->event_cost, 2, '.', '') . '">';
			}

		//If no prices are found, display the free event message
		} else if ($wpdb->num_rows == 0) {
			$html .= '<span class="free_event">' . __('Free Event', 'event_espresso') . '</span>';
			$html .= '<input type="hidden" name="payment' . $multi_name_adjust . '" id="payment-' . $event_id . '" value="' . __('free event', 'event_espresso') . '">';
		}

		return  $html;
    }

//}

if ( !function_exists('espresso_member_price_select_action') ){
	function espresso_member_price_select_action($event_id, $atts = '' ){
		
		$html = '';
		$html .= is_admin() ? '' : '<p class="event_prices">';
		$html .= event_espresso_member_price_dropdown($event_id, $atts);
		$html .= is_admin() ? '' : '</p>';
		echo $html;
		return;
	}
	if (!is_admin() ){
		remove_action('espresso_price_select', 'espresso_price_select_action');
		add_action('espresso_price_select', 'espresso_member_price_select_action', 10, 2);
	}
	add_action('espresso_member_price_select_action', 'espresso_member_price_select_action', 10, 2);
}

/*
  Returns the final price of an event
 *
 * @params int $price_id
 * @params int $event_id
 */
if (!function_exists('event_espresso_get_final_price')) {
    function event_espresso_get_final_price($price_id, $event_id = 0) {
        global $wpdb, $org_options;
        $sql = "SELECT id, event_cost, surcharge, surcharge_type FROM " . EVENTS_PRICES_TABLE . " WHERE id='" . $price_id . "' ORDER BY id ASC LIMIT 1";
        if (is_user_logged_in()) {
            $sql = "SELECT id, event_cost, member_price, surcharge, surcharge_type FROM " . EVENTS_PRICES_TABLE . " WHERE id='" . $price_id . "' ORDER BY id ASC LIMIT 1";
        }
        $results = $wpdb->get_results($sql);
        foreach ($results as $result) {
            if ($wpdb->num_rows >= 1) {
                if ($result->event_cost > 0.00) {

                    $surcharge = number_format($result->surcharge, 2, '.', ''); //by default it's 0.  if flat rate, will just be formatted and atted to the total
                    if ($result->surcharge > 0 && $result->surcharge_type == 'pct') { //if >0 and is percent, calculate surcharg amount to be added to total
                        $surcharge = number_format($result->event_cost * $result->surcharge / 100, 2, '.', '');
                    }

                    $event_cost = ($result->member_price == "") ? $result->event_cost + $surcharge : $result->member_price + $surcharge;

                    // Addition for Early Registration discount
                    if (early_discount_amount($event_id, $event_cost) != false) {
                        $early_price_data = array();
                        $early_price_data = early_discount_amount($event_id, $event_cost);
                        $event_cost = $early_price_data['event_price'];
                    }
                } else {
                    $event_cost = __('0.00', 'event_espresso');
                }
            } else if ($wpdb->num_rows == 0) {
                $event_cost = __('0.00', 'event_espresso');
            }
        }
        return empty($event_cost) ? 0 : $event_cost;
    }
}
