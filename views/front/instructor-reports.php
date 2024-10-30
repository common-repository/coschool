<?php
	$filter_groups = [
		'course'		=> __( 'By Course', 'coschool' ),
		'category'		=> __( 'By Category', 'coschool' ),
	];
	$is_period_form	= true;//isset( $_GET['action'] ) && $_GET['action'] == 'coschool-periodic-filter';
	$is_share_form	= true;//isset( $_GET['action'] ) && $_GET['action'] == 'coschool-share-filter';
?>

<div class="wrap">
	<div id="coschool-reports">

		<h3 class="coschool-dashboard-title"><?php esc_html_e( 'Reports', 'coschool' ); ?></h3>
		<div class="coschool-report cx-shadow">
			<div class="coschool-filter">
				<form id="coschool-periodic-filter">
					<input type="hidden" name="page" value="reports">
					<input type="hidden" name="action" value="coschool-periodic-filter">
					<?php // wp_nonce_field(); ?>

					<label for="coschool-periodic-group-by"><?php esc_html_e( 'Filter', 'coschool' ); ?></label>
					
					<select id="coschool-periodic-group-by" name="group-by">
						<option value=""><?php esc_html_e( '- Choose -', 'coschool' ); ?></option>
						<?php
						foreach ( $filter_groups as $group => $label ) {
							$_selected = $is_period_form && isset( $_GET['group-by'] ) && selected( $_GET['group-by'], $group, false ) ? 'selected' : '';
							echo "<option value='" . esc_attr( $group ) . "' " . esc_attr( $_selected ) . ">" . esc_html( $label ) . "</option>";
						}
						?>
					</select>
					
					<select id="coschool-periodic-item" name="item" style="display:none;" disabled>
						<option value=""><?php esc_html_e( '- Choose -', 'coschool' ); ?></option>
					</select>

					<select id="coschool-periodic-date-range" class="coschool-date-range" name="date-range">
						<option value=""><?php esc_html_e( 'All', 'coschool' ); ?></option>
						<?php foreach ( coschool_intervals() as $interval => $label ) {
							$_selected = $is_period_form && isset( $_GET['date-range'] ) && selected( $_GET['date-range'], $interval, false ) ? 'selected' : '';
							echo "<option value='" . esc_attr( $interval ) . "' " . esc_attr( $_selected ) . ">" . esc_html( $label ) . "</option>";
						} ?>
					</select>

					<input type="date" class="coschool-date" name="from" style="display: none;" disabled value="<?php echo esc_attr( $is_period_form && isset( $_GET['from'] ) ? coschool_sanitize( $_GET['from'] ) : '' ); ?>" />
					<input type="date" class="coschool-date" name="to" style="display: none;" disabled value="<?php echo esc_attr( $is_period_form && isset( $_GET['to'] ) ? coschool_sanitize( $_GET['to'] ) : '' ); ?>" />
					<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Filter', 'coschool' ); ?>" />
				</form>
			</div>
			<div id="coschool-periodic-wrap">
				<canvas id="coschool-periodic-report" ></canvas>
			</div>
		</div>

		<h3 class="coschool-dashboard-title"><?php esc_html_e( 'Top Sellers', 'coschool' ); ?></h3>
		<div class="coschool-report cx-shadow">
			<div class="coschool-filter">
				<form id="coschool-share-filter">
					<input type="hidden" name="page" value="reports">
					<input type="hidden" name="action" value="coschool-share-filter">
					<?php // wp_nonce_field(); ?>
					
					<label for="coschool-share-group-by"><?php esc_html_e( 'Filter', 'coschool' ); ?></label>
					
					<select id="coschool-share-group-by" name="group-by">
						<option value=""><?php esc_html_e( '- Choose -', 'coschool' ); ?></option>
						<?php
						foreach ( $filter_groups as $group => $label ) {
							$_selected = $is_share_form && isset( $_GET['group-by'] ) && selected( $_GET['group-by'], $group, false ) ? 'selected' : '';
							echo "<option value='" . esc_attr( $group ) . "' " . esc_attr( $_selected ) . ">" . esc_html( $label ) . "</option>";
						}
						?>
					</select>

					<select id="coschool-share-date-range" class="coschool-date-range" name="date-range">
						<option value=""><?php esc_html_e( 'All', 'coschool' ); ?></option>
						<?php foreach ( coschool_intervals() as $interval => $label ) {
							$_selected = $is_share_form && isset( $_GET['date-range'] ) && selected( $_GET['date-range'], $interval, false ) ? 'selected' : '';
							echo "<option value='" . esc_attr( $interval ) . "' " . esc_attr( $_selected ) . ">" . esc_html( $label ) . "</option>";
						} ?>
					</select>

					<input type="date" class="coschool-date" name="from" style="display: none;" disabled value="<?php esc_attr_e( $is_share_form ) && isset( $_GET['from'] ) ? coschool_sanitize( $_GET['from'] ) : ''; ?>" />
					<input type="date" class="coschool-date" name="to" style="display: none;" disabled value="<?php esc_attr_e( $is_share_form ) && isset( $_GET['to'] ) ? coschool_sanitize( $_GET['to'] ) : ''; ?>" />
					<input type="submit" class="button button-primary" value="<?php esc_html_e( 'Filter', 'coschool' ); ?>" />
				</form>
			</div>
			<div style="clear:both;"></div>
			<div id="coschool-share-wrap">
				<div id="coschool-share-report-sales-wrap">
					<canvas id="coschool-share-report-sales" ></canvas>
				</div>
				<div id="coschool-share-report-earning-wrap">
					<canvas id="coschool-share-report-earning" ></canvas>
				</div>
			</div>
		</div>
	</div>
</div>