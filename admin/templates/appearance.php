<?php
    if( ! defined( 'ABSPATH' ) ) {
        exit;
    }
?>

<div id="settings-awsm-settings-appearance" class="awsm-admin-settings" style="display: none;" >
    <form method="POST" action="options.php#settings-awsm-settings-appearance" id="appearance_options_form">

        <?php
            settings_fields( 'awsm-jobs-appearance-settings' );
            $listing_view = get_option( 'awsm_jobs_listing_view' );
            $list_page = get_option( 'awsm_jobs_list_per_page' );
            $number_list_columns = get_option( 'awsm_jobs_number_of_columns' );
            $awsm_current_appearance_subtab = get_option( 'awsm_current_appearance_subtab' );
            $specifications = get_option( 'awsm_jobs_filter' );
            $hidden_class = ' class="awsm-hide"';
            $no_columns_options = apply_filters( 'awsm_jobs_number_of_columns_options', array( 1, 2, 3, 4 ) );
            $enable_filters = get_option( 'awsm_enable_job_filter_listing' );
            $spec_position = get_option( 'awsm_jobs_specs_position', 'below_content' );
            $job_specs_positions = apply_filters( 'awsm_jobs_specifications_position', array(
                'below_content'    => 'Below job description',
                'above_content'    => 'Above job description'
            ) );
        ?>

        <div class="awsm-nav-subtab-container clearfix">
            <ul class="subsubsub">
                <li>
                    <a href="#" class="awsm-nav-subtab current" id="awsm-job-listing-nav-subtab" data-target="#awsm-job-listing-options-container"><?php _e( 'Job Listing Page', 'wp-job-openings' ); ?></a>
                </li>
                <li>
                    <a href="#" class="awsm-nav-subtab" id="awsm-job-details-nav-subtab" data-target="#awsm-job-details-options-container"><?php _e( 'Job Detail Page', 'wp-job-openings' ); ?></a>
                </li>
            </ul>
            <input type="hidden" name="awsm_current_appearance_subtab" id="awsm_current_appearance_subtab" value="<?php echo $awsm_current_appearance_subtab; ?>" />
        </div>

        <div class="awsm-sub-options-container" id="awsm-job-listing-options-container">
            <div class="awsm-form-section-main">
                <table class="form-table">
                    <tbody>
                        <?php do_action( 'before_awsm_appearance_listing_settings' ); ?>
                        <tr>
                            <th scope="row" colspan="2" class="awsm-form-head-title">
                                <h2><?php _e( 'Job listing layout options', 'wp-job-openings' ); ?></h2>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Layout of job listing page', 'wp-job-openings' ); ?>
                            </th>
                            <td>
                               <ul class="awsm-list-inline">
                                    <li>
                                        <label for="awsm-list-view"><input type="radio" name="awsm_jobs_listing_view" value="list-view" class="awsm-check-toggle-control" id="awsm-list-view" <?php echo esc_attr( $this->is_settings_field_checked( $listing_view, 'list-view', true ) ); ?> data-toggle-target="#awsm_jobs_number_of_columns_row" /><?php _e( 'List view ', 'wp-job-openings' ); ?></label>
                                    </li>
                                    <li>
                                        <label for="awsm-grid-view"><input type="radio" name="awsm_jobs_listing_view" value="grid-view" class="awsm-check-toggle-control" id="awsm-grid-view" <?php echo esc_attr( $this->is_settings_field_checked( $listing_view, 'grid-view' ) ); ?> data-toggle="true" data-toggle-target="#awsm_jobs_number_of_columns_row" /> <?php _e( 'Grid view ', 'wp-job-openings' ); ?></label>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr<?php echo ( $listing_view === 'list-view' ) ? $hidden_class : ''; ?> id="awsm_jobs_number_of_columns_row">
                            <th scope="row">
                                <label for="awsm_jobs_number_of_columns"><?php _e( 'Number of columns ', 'wp-job-openings' ); ?></label>
                            </th>
                            <td>
                                <select name="awsm_jobs_number_of_columns" class="awsm-select-control regular-text" id="awsm_jobs_number_of_columns">
                                    <?php
                                        if( ! empty( $no_columns_options ) ) {
                                            foreach( $no_columns_options as $column ) {
                                                $text = sprintf( _n( '%d Column', '%d Columns', $column, 'wp-job-openings' ), $column );
                                                $selected = '';
                                                if( $number_list_columns == $column ) {
                                                    $selected = ' selected';
                                                }
                                                printf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $column ), esc_html( $text ), $selected );
                                            }
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="awsm_jobs_list_per_page"><?php _e( 'Listings per page ', 'wp-job-openings' ); ?></label>
                            </th>
                            <td>
                              <input type="number" class="regular-text" min="1" name="awsm_jobs_list_per_page" id="awsm_jobs_list_per_page" value="<?php echo esc_attr( $list_page ); ?>">
                            </td>
                        </tr>
                        <?php
                        if ( ! empty( $specifications ) ) : ?>
                            <tr>
                                <th scope="row" colspan="2" class="awsm-form-head-title">
                                    <h2><?php _e( 'Job filter options', 'wp-job-openings' ); ?></h2>
                                </th>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?php _e( 'Job filters', 'wp-job-openings' ); ?>
                                </th>
                                <td>
                                    <label for="awsm_enable_job_filter_listing">
                                        <input type="checkbox" class="awsm-check-toggle-control" id="awsm_enable_job_filter_listing" name="awsm_enable_job_filter_listing" value="enabled" <?php echo esc_attr( $this->is_settings_field_checked( $enable_filters, 'enabled' ) ); ?> data-toggle="true" data-toggle-target="#awsm_jobs_available_filters_row" /><?php _e( 'Enable job filters in job listing ', 'wp-job-openings' ); ?>
                                    </label>
                                    <p class="description"><?php esc_html_e( 'Check this option to show job filter options in the job listing page', 'wp-job-openings' ); ?></p>
                                </td>
                            </tr>
                            <tr<?php echo ( $enable_filters !== 'enabled' ) ? $hidden_class : ''; ?> id="awsm_jobs_available_filters_row">
                                <th scope="row">
                                    <?php _e( 'Available filters', 'wp-job-openings' ); ?>
                                </th>
                                <td>
                                    <ul class="awsm-check-list">
                                        <?php
                                            $available_filters = get_option( 'awsm_jobs_listing_available_filters' );
                                            foreach ( $specifications as $filters ) {
                                                $this->display_check_list( $filters['filter'], 'awsm_jobs_listing_available_filters', $filters['taxonomy'], $available_filters );
                                            }
                                        ?>
                                    </ul>
                                    <p class="description"> <?php esc_html_e( 'Check the job specs you want to enable as filters', 'wp-job-openings' ); ?> </p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th scope="row" colspan="2" class="awsm-form-head-title">
                                <h2><?php _e( 'Other options', 'wp-job-openings' ); ?></h2>
                            </th>
                        </tr>
                        <?php if ( ! empty( $specifications ) ) : ?>
                            <tr>
                                <th scope="row">
                                    <?php _e( 'Job specs in the listing', 'wp-job-openings' ); ?>
                                </th>
                                <td>
                                    <ul class="awsm-check-list">
                                        <?php
                                            $listing_specs = get_option( 'awsm_jobs_listing_specs' );
                                            foreach ( $specifications as $specs ) {
                                                $this->display_check_list( $specs['filter'], 'awsm_jobs_listing_specs', $specs['taxonomy'], $listing_specs );
                                            }
                                        ?>
                                    </ul>
                                    <p class="description"><?php esc_html_e( ' Check the job specs you want to show along with the listing view', 'wp-job-openings' ); ?></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th scope="row">
                               <?php _e( 'Expired Jobs', 'wp-job-openings' ); ?>
                            </th>
                            <td>
                                <label for="awsm-hide-jobs"><input type="checkbox" id="awsm-hide-jobs" name="awsm_jobs_expired_jobs_listings" value="expired" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_expired_jobs_listings' ), 'expired' ) ); ?> /><?php _e( 'Hide expired jobs from listing page', 'wp-job-openings' ); ?></label>
                            </td>
                        </tr>
                        <?php do_action( 'after_awsm_appearance_listing_settings' ); ?>
                    </tbody>
                </table>
            </div><!-- .awsm-form-section-main -->
        </div><!-- #awsm-job-listing-options-container -->

        <div class="awsm-sub-options-container" id="awsm-job-details-options-container" style="display: none;">
            <div class="awsm-form-section-main">
                <table class="form-table">
                    <tbody>
                        <?php do_action( 'before_awsm_appearance_details_settings' ); ?>
                        <tr>
                            <th scope="row" colspan="2" class="awsm-form-head-title">
                                <h2><?php _e( 'Job detail page layout options', 'wp-job-openings' ); ?></h2>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Layout of job detail page', 'wp-job-openings' ); ?>
                            </th>
                            <td>
                                <?php $job_details_layout = get_option( 'awsm_jobs_details_page_layout' ); ?>
                                    <label for="awsm-job-single-layout">
                                        <input type="radio" name="awsm_jobs_details_page_layout" value="single" id="awsm-job-single-layout" <?php echo esc_attr( $this->is_settings_field_checked( $job_details_layout, 'single', true ) ); ?> /><?php _e( 'Single Column ', 'wp-job-openings' ); ?>
                                    </label>
                                    <label for="awsm-job-two-columns-layout">
                                        <input type="radio" name="awsm_jobs_details_page_layout" value="two" id="awsm-job-two-columns-layout" <?php echo esc_attr( $this->is_settings_field_checked( $job_details_layout, 'two' ) ); ?> /> <?php _e( 'Two Columns ', 'wp-job-openings' ); ?>
                                    </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php _e( 'Job specifications', 'wp-job-openings' ); ?>
                            </th>
                            <td>
                                <ul class="awsm-check-list">
                                    <li>
                                        <label for="awsm_jobs_specification_job_detail">
                                            <input type="checkbox" id="awsm_jobs_specification_job_detail"  name="awsm_jobs_specification_job_detail" value="show_in_detail" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_specification_job_detail', 'show_in_detail' ), 'show_in_detail' ) ); ?> /><?php _e( 'Show job specifications in job detail page', 'wp-job-openings' ); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label for="awsm_jobs_show_specs_icon">
                                            <input type="checkbox" id="awsm_jobs_show_specs_icon" name="awsm_jobs_show_specs_icon" value="show_icon" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_show_specs_icon', 'show_icon' ), 'show_icon' ) ); ?> /><?php _e( 'Show icons for job specifications in job detail page', 'wp-job-openings' ); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr id="awsm_jobs_specification_position">
                            <th scope="row">
                                <label for="awsm_jobs_specs_position"><?php _e( 'Job spec position ', 'wp-job-openings' ); ?></label>
                            </th>
                            <td>
                                <select name="awsm_jobs_specs_position" class="awsm-select-control regular-text" id="awsm_jobs_specs_position">
                                    <?php
                                        if( ! empty( $job_specs_positions ) ) {
                                            foreach( $job_specs_positions as $position => $label ) {
                                                $selected = '';
                                                if( $spec_position == $position ) {
                                                    $selected = ' selected';
                                                }
                                                printf( '<option value="%1$s"%3$s>%2$s</option>', esc_attr( $position ), esc_html( $label ), $selected );
                                            }
                                        }
                                    ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php esc_html_e( 'Other display options', 'wp-job-openings' ); ?>
                            </th>
                            <td>
                                <ul class="awsm-check-list">
                                    <li>
                                        <label for="awsm-hide-content">
                                            <input type="checkbox" id="awsm-hide-content"  name="awsm_jobs_expired_jobs_content_details" value="content" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_expired_jobs_content_details' ), 'content' ) ); ?> /><?php _e( 'Hide content of expired listing from job detail page ', 'wp-job-openings' ); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label for="awsm_jobs_expired_jobs_block_search">
                                            <input type="checkbox" id="awsm_jobs_expired_jobs_block_search"  name="awsm_jobs_expired_jobs_block_search" value="block_expired" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_expired_jobs_block_search' ), 'block_expired' ) ); ?> /><?php _e( 'Block search engine robots to expired jobs', 'wp-job-openings' ); ?>
                                        </label>
                                    </li>
                                    <li>
                                        <label for="awsm_jobs_hide_expiry_date">
                                            <input type="checkbox" id="awsm_jobs_hide_expiry_date"  name="awsm_jobs_hide_expiry_date" value="hide_date" <?php echo esc_attr( $this->is_settings_field_checked( get_option( 'awsm_jobs_hide_expiry_date' ), 'hide_date' ) ); ?> /><?php _e( 'Hide expiry date from job detail page', 'wp-job-openings' ); ?>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <?php do_action( 'after_awsm_appearance_details_settings' ); ?>
                    </tbody>
                </table>
           </div><!-- .awsm-form-section-main -->
        </div><!-- #awsm-job-details-options-container -->

        <div class="awsm-form-footer">
            <?php echo apply_filters( 'awsm_job_settings_submit_btn', get_submit_button(), 'appearance' ); ?>
        </div><!-- .awsm-form-footer -->
    </form>
</div><!-- .awsm-admin-settings -->