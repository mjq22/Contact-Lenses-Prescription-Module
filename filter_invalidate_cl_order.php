<?php

function filter_invalidate_cl_order() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/opticommerce-cl/assets/css/style-admin.css" rel="stylesheet" />
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    
    <div class="wrap">
        <h2><?php _e('CL Verification', 'woocommerce'); ?></h2><br>
        <?php
        $valiated_url = '';
        $s_order_id = trim(filter_input(INPUT_GET, 's_order_id'));
        $s_verification_type = trim(filter_input(INPUT_GET, 's_verification_type'));
        $filter_validated = trim(filter_input(INPUT_GET, 'filter_validated'));
        $order_query = '';
        if($s_order_id != '') {
            $order_query .= ' AND upor.order_id='.$s_order_id;
        }
        if($s_verification_type != '') {
            $order_query .= ' AND up.verification_type="'.$s_verification_type.'"';
        }
        $s_customer_name = trim(filter_input(INPUT_GET, 's_customer_name'));
        $customer_fname_query = '';
        $customer_lname_query = '';
        if($s_customer_name != '') {
            $s_customer_name_arr = explode(' ', $s_customer_name);
            if(count($s_customer_name_arr) == 1) {
                $customer_fname_query = " AND meta_value LIKE '%{$s_customer_name_arr[0]}%'";
                //$customer_lname_query = " AND (meta_value LIKE '%{$s_customer_name_arr[0]}%')";
            } else if(count($s_customer_name_arr) > 1) {
                $customer_fname_query = " AND meta_value LIKE '%{$s_customer_name_arr[0]}%'";
                $customer_lname_query = " AND (meta_value LIKE '%{$s_customer_name_arr[1]}%')";
            }
            
        }
        if($filter_validated) {
            $order_query .= ' AND up.is_validated=1';
            $valiated_url = '&filter_validated=1';
            
        } else {
            $order_query .= ' AND up.is_validated=0';
        }
        global $wpdb;
    // need to add join to get customer name
    $validate_orders = $wpdb->get_results("SELECT up.user_id AS customer_id, up.verification_type, um1.meta_value AS first_name, um2.meta_value AS last_name, upor.order_id "
            . "FROM " . $wpdb->prefix . "user_cl_prescriptions AS up, "
            . $wpdb->prefix . "user_cl_prescriptions_order_ref AS upor, "
            . $wpdb->prefix . "usermeta AS um  JOIN
                (SELECT user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key IN ('first_name'){$customer_fname_query}) AS um1 USING(user_id)
                    JOIN
                    (SELECT user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key IN ('last_name'){$customer_lname_query}) AS um2 USING(user_id) "
            . "WHERE up.id=upor.cl_id AND up.user_id=um.user_id{$order_query} "
            . "GROUP BY order_id", OBJECT);
        ?>
        <form id="classes_register" method="get">
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
                    <label for="s_customer_name" class="screen-reader-text"><?php _e('Customer Name', 'woocommerce'); ?></label>                    
                    <input type="text" name="s_customer_name" id="s_customer_name" value="<?php echo $s_customer_name; ?>" placeholder="Customer Name">
                    <label for="s_order_id" class="screen-reader-text"><?php _e('Order ID', 'woocommerce'); ?></label>
                    <input type="text" name="s_order_id" id="s_order_id" value="<?php echo $s_order_id; ?>" placeholder="Order ID">
                    <label for="s_verification_type" class="screen-reader-text"><?php _e('Verification Type', 'woocommerce'); ?></label>
                    <select name="s_verification_type" id="s_verification_type">
                        <option value="">Choose Verification Type</option>
                        <option value="Existing Patient"<?php if($s_verification_type == 'Existing Patient') { echo ' selected="selected"'; } ?>>Existing Patient</option>
                        <option value="Send Later"<?php if($s_verification_type == 'Send Later') { echo ' selected="selected"'; } ?>>Send Later</option>
                        <option value="Upload RX"<?php if($s_verification_type == 'Upload RX') { echo ' selected="selected"'; } ?>>Upload RX</option>
                        <option value="RX Entry With Opticians Details"<?php if($s_verification_type == 'RX Entry With Opticians Details') { echo ' selected="selected"'; } ?>>RX Entry With Opticians Details</option>
                        <option value="RX Entry With Photo"<?php if($s_verification_type == 'RX Entry With Photo') { echo ' selected="selected"'; } ?>>RX Entry With Photo</option>
                    </select>
                    <input id="filter_invalidated" class="button action" name="filter_invalidated" value="Unverified Orders" type="submit">
                    <input id="filter_validated" class="button action" name="filter_validated" value="Verified Orders" type="submit">
                </div>
            </div>
            <table class='wp-list-table widefat fixed striped posts'>
                <tr>
                    <th class="manage-column ss-list-width"><?php _e('Customer ID', 'woocommerce'); ?></th>
                    <th class="manage-column ss-list-width"><?php _e('Customer Name', 'woocommerce'); ?></th>
                    <th class="manage-column ss-list-width"><?php _e('Verification Type', 'woocommerce'); ?></th>
                    <th class="manage-column ss-list-width"><?php _e('Order ID', 'woocommerce'); ?></th>
                    <th class="manage-column ss-list-width"><?php _e('Status', 'woocommerce'); ?></th>
                </tr>
                <?php
                foreach ($validate_orders as $sorder) {                    
                    ?>
                    <tr>
                        <td class="manage-column ss-list-width"><?php echo $sorder->customer_id; ?></td>
                        <td class="manage-column ss-list-width"><?php echo $sorder->first_name.' '.$sorder->last_name; ?></td>
                        <td class="manage-column ss-list-width"><?php echo $sorder->verification_type; ?></td>
                        <td class="manage-column ss-list-width"><a href="<?php echo get_edit_post_link($sorder->order_id); ?>" target="_new"><?php echo $sorder->order_id; ?></a></td>
                        <td><a href="<?php echo admin_url('admin.php?page=saved_invalidate_cl_order&order_id=' . $sorder->order_id.$valiated_url); ?>"><?php ((!empty($_GET['filter_validated']) && stripos($_GET['filter_validated'], 'Verified') !== false) ?  _e('Re-verify', 'woocommerce') : _e('Verify', 'woocommerce') ); ?></a></td>
                    </tr>
                <?php } 
                if(!count($validate_orders)) { ?>
                    <tr>
                        <td class="manage-column ss-list-width" colspan="4" align="center">
                            <?php _e('No Order Found!', 'woocommerce'); ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </form>
    </div>
    <?php
}
