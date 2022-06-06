<?php

function saved_invalidate_cl_order() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/opticommerce-cl/assets/css/style-admin.css" rel="stylesheet" /> 
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/opticommerce-cl/assets/css/datepicker.min.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/opticommerce-cl/assets/css/cl_style.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/opticommerce-cl/assets/js/datepicker.min.js" /></script>
    <script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/opticommerce-cl/assets/js/form-validation.js?v=1"></script>
    <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <div class="wrap">
        <?php
        $order_id = filter_input(INPUT_GET, 'order_id');
        $cl_id = filter_input(INPUT_GET, 'cl_id');
        $filter_validated = filter_input(INPUT_GET, 'filter_validated');
        $pd_img_url = filter_input(INPUT_GET, 'pd_img_url');
        if (isset($order_id) && $order_id != '') {
            // delete pd image and remove it from db cl record
            /* if((isset($cl_id) && $cl_id != '') && (isset($pd_img_url) && $pd_img_url != '')) {
              if (remove_pd_img_from_db_clu($cl_id)) {
              unlink(getcwd().'/..'.$pd_img_url);
              echo '<span style="color:green;">PD image has been deleted</span>';
              // need to verify either its deleted or not
              /*if(!unlink($pd_img_url)) {
              echo '<span style="color:green;">PD image has been deleted</span>';
              } else {
              echo '<span style="color:red;">PD image cannot be deleted due to an error</span>';
              } */
            /* }
              } */
            // need to work on image upload
            ?>
            <h2>Verify Orders</h2><br>
            <?php
            $order_line_item_id_right = filter_input(INPUT_POST, 'order_line_item_id_right');
            $order_line_item_id_left = filter_input(INPUT_POST, 'order_line_item_id_left');
            if ((isset($order_line_item_id_right) && $order_line_item_id_right != '') ||
                            (isset($order_line_item_id_left) && $order_line_item_id_left != '')){
                $cl_data = filter_input_array(INPUT_POST);
                $cl_data['is_validated'] = 1;
                extract($cl_data);

                // save it under order line item
                $order = wc_get_order($order_id);
                //$order_data = $order->get_data();
                $order_items = $order->get_items();
                $is_matched = false;
                $i = 0;
                foreach ($order_items as $item_key => $item_values) {
                    if ($item_key == $cl_data['order_line_item_id_right']) {
                        $is_matched = true;
                        $eye_type = 'right';
                    }
                    if ($item_key == $cl_data['order_line_item_id_left']) {
                        $is_matched = true;
                        $eye_type = 'left';
                    }
                    if ($is_matched) {
                        if ($cl_data['eye_side_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_eye-type", $cl_data['eye_side_'.$eye_type]);
                        }
                        if ($cl_data['sphere_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_sphere", $cl_data['sphere_'.$eye_type]);
                        }
                        if ($cl_data['diameter_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_diameter", $cl_data['diameter_'.$eye_type]);
                        }
                        if ($cl_data['base_curve_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_base-curve", $cl_data['base_curve_'.$eye_type]);
                        }
                        if ($cl_data['cylinder_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_cylinder", $cl_data['cylinder_'.$eye_type]);
                        }
                        if ($cl_data['axis_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_axis", $cl_data['axis_'.$eye_type]);
                        }
                        if ($cl_data['addition_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_addition", $cl_data['addition_'.$eye_type]);
                        }
                        if ($cl_data['color_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_color", $cl_data['color_'.$eye_type]);
                        }
                        if ($cl_data['dominance_'.$eye_type] != '') {
                            wc_update_order_item_meta($item_key, "pa_dominance", $cl_data['dominance_'.$eye_type]);
                        }
                        if ($cl_data['first_name'] != '') {
                            wc_update_order_item_meta($item_key, "First Name", $cl_data['first_name']);
                        }
                        if ($cl_data['last_name'] != '') {
                            wc_update_order_item_meta($item_key, "Last Name", $cl_data['last_name']);
                        }
                        if ($cl_data['optician_name'] != '') {
                            wc_update_order_item_meta($item_key, "Opticians Name", $cl_data['optician_name']);
                        }
                        if ($cl_data['optician_phone'] != '') {
                            wc_update_order_item_meta($item_key, "Opticians Phone", $cl_data['optician_phone']);
                        }
                        if ($cl_data['optician_address'] != '') {
                            wc_update_order_item_meta($item_key, "Opticians Address", $cl_data['optician_address']);
                        }
                        if ($cl_data['patient_dob'] != '') {
                            wc_update_order_item_meta($item_key, "Patient DOB", $cl_data['patient_dob']);
                        }
                        if ($cl_data['practice_location'] != '') {
                            wc_update_order_item_meta($item_key, "Practice Location", $cl_data['practice_location']);
                        }
                        $is_matched = false;
                        $i++;
                    }
                }

                if ($i) {
                    // update cl in cl table
                    if (update_cl_prescription($cl_data) !== false) {
                        echo '<span style="color:green;">Prescription updated!</span><br>';
                        // remove cl ref from ref table
                        //remove_cl_user_ref($cl_data);
                    } else {
                        echo '<span style="color:red;">There is some issue while updating prescription. Please try again later.</span><br>';
                        //global $wpdb;
                        //echo "<br> ---------------- <br>";
                        //print_r($wpdb->last_error);
                    }
                }
            }
            $invalidate_order_items = get_invalidate_order_items_clu($order_id, $filter_validated);
            ?>
            <table class='wp-list-table widefat fixed striped posts'>               
                <?php
                $new_array = array();
                $grp_id =0;
                $lmn = 0;
                $is_pair = false;
                for($i=0; $i < count($invalidate_order_items); $i++) {
                    if($grp_id === $invalidate_order_items[$i]['cl_group_id']) { continue; }
                    for($j=$i+1; $j < count($invalidate_order_items); $j++) {
                        if($invalidate_order_items[$i]['cl_group_id'] === $invalidate_order_items[$j]['cl_group_id']) {
                            $is_pair = true;
                            $new_array[$lmn] = $invalidate_order_items[$i];
                            $eye_type = strtolower($invalidate_order_items[$j]['eye_side']);
                            $new_array[$lmn]['id_'.$eye_type] = $invalidate_order_items[$j]['id'];
                            $new_array[$lmn]['base_curve_'.$eye_type] = $invalidate_order_items[$j]['base_curve'];
                            $new_array[$lmn]['diameter_'.$eye_type] = $invalidate_order_items[$j]['diameter'];
                            $new_array[$lmn]['colour_'.$eye_type] = $invalidate_order_items[$j]['colour'];
                            $new_array[$lmn]['sphere_'.$eye_type] = $invalidate_order_items[$j]['sphere'];
                            $new_array[$lmn]['cylinder_'.$eye_type] = $invalidate_order_items[$j]['cylinder'];
                            $new_array[$lmn]['axis_'.$eye_type] = $invalidate_order_items[$j]['axis'];
                            $new_array[$lmn]['addition_'.$eye_type] = $invalidate_order_items[$j]['addition'];
                            $new_array[$lmn]['dominance_'.$eye_type] = $invalidate_order_items[$j]['dominance'];
                            $new_array[$lmn]['eye_side_'.$eye_type] = $invalidate_order_items[$j]['eye_side'];
                            $new_array[$lmn]['order_line_item_id_'.$eye_type] = $invalidate_order_items[$j]['order_line_item_id'];
                            $lmn++;
                            break;
                        }
                    }
                    if(!$is_pair) {
                        $new_array[$lmn] = $invalidate_order_items[$i];
                    }
                    $is_pair = false;
                    $grp_id = $invalidate_order_items[$i]['cl_group_id'];                    
                }
                $cnt = 0;
                //echo '<pre>'; print_r($new_array); echo '</pre>';
                foreach ($new_array as $item_obj) {                    
                    ?>
                    <tr>
                        <td class="manage-column ss-list-width">
                            <div class="cl-validation">
                                <div class="left-container">
                                    <h3><?= $item_obj['verification_type'] ?></h3>
                                    <?php
                                    if ($item_obj['product_reference'] && function_exists('post_id_by_meta_key_and_value')) {
                                        $pid = post_id_by_meta_key_and_value('_product_reference', $item_obj['product_reference']);
                                        echo '<h4>' . get_the_title($pid) . '</h4>';
                                    }
                                    ?>
                                    <?php
                                    addEditCLForm($item_obj, true, $cnt);
                                    ?>
                                </div>
                                <div class="right-container"><?php
                                    if ($item_obj['prescription_img_url'] != '') {
                                        //$filename = '/home/shopvcs/shopvcs_images/Loosen-1.jpg.txt'; 
                                        //$filename = '/home/shopvcs/shopvcs_images/Elizabeth-Murray-12-1-19.pdf.txt'; 
                                        $img_url = '';
                                        if (filter_var($item_obj['prescription_img_url'], FILTER_VALIDATE_URL)) {
                                            $img_url = $item_obj['prescription_img_url'];
                                            echo '<a href="' . $img_url . '" class="popup" target="_blank"><img src="' . $img_url . '" ></a><br>';
                                        } else {
                                            $ivlen = openssl_cipher_iv_length(CIPHERING);
                                            $iv = openssl_random_pseudo_bytes($ivlen);
                                            
                                            // Explode to break encrypted image path in array index 0 and the random key hex value in index 1
                                            // Now with encrypted file path we ar saving the hex value of random presudo bytes also
                                            $encrptexplode = explode('%RANDBYTES%', $item_obj['prescription_img_url']);
                                            if(count($encrptexplode) > 1){
                                                $filename = decryptstringcl($encrptexplode[0], CIPHERING, ENCRYPTSALT, 0, hex2bin($encrptexplode[1]));
                                                $filename .= 'EXTRAFILE' . $encrptexplode[1];
                                            } else{
                                                $filename = decryptstringcl($item_obj['prescription_img_url'], CIPHERING, ENCRYPTSALT, 0, $iv);
                                                $filename .= 'EXTRAFILE' . bin2hex($iv);
                                            }
                                            
                                            //$filename = decryptstringcl($item_obj['prescription_img_url'], CIPHERING, ENCRYPTSALT);
                                            //$filename = str_replace('public_html/', '', $filename);
                                            if (strpos($filename, '.pdf') !== false) {
                                                echo getencryptedimgbypathclwithicon($filename);
                                            } else {
                                                $img_stream = getencryptionofimgcl($filename);
                                                $img_url = getencryptedimgurlbypathcl($filename);
                                                //echo '<a href="' . $img_url . '" class="popup" target="_new"><img src="' . $img_url . '" ></a><br>';
                                                // fix for cl prescription image href
                                                echo  '<a href="' . $img_url . '" class="popup" target="_blank"><img src="' . $img_stream . '" ></a><br>';
                                            }
                                        }
                                    }
                                    $cnt++;
                                    ?>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <?php
                            if ($cnt < count($invalidate_order_items)) {
                                echo '<hr>';
                            }
                            ?>                            
                        </td>
                    </tr>
                    <?php
                }
                if (!count($invalidate_order_items)) {
                    $ord = new WC_Order($order_id);
                    if ($ord->get_status() == 'processing') {
                        $ord->update_status('in-lab');
                    }
                    ?>
                    <tr>
                        <td class="manage-column ss-list-width">
                            No more validation item left under this order, <a href="admin.php?page=filter_invalidate_cl_order">go back</a> to validate other orders.
                        </td>
                    </tr>
            <?php } ?>        
            </table>
    <?php } ?>
    </div>
    <?php
}

function remove_pd_img_from_db_clu($cl_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_cl_prescriptions';
    return $wpdb->update(
                    $table_name, array(
                'pd_img_url' => ''
                    ), array(
                'id' => $cl_id
                    )
    );
}

function get_invalidate_order_items_clu($order_id, $filter_validated) {
    global $wpdb;
    if ($filter_validated) {
        $order_query .= ' AND up.is_validated=1';
    } else {
        $order_query .= ' AND up.is_validated=0';
    }
    $results = $wpdb->get_results("SELECT up.*, upor.order_id, upor.order_line_item_id "
            . "FROM " . $wpdb->prefix . "user_cl_prescriptions AS up, "
            . $wpdb->prefix . "user_cl_prescriptions_order_ref AS upor "
            . "WHERE up.id=upor.cl_id AND upor.order_id=" . $order_id . $order_query, ARRAY_A);
    return $results;
}

function remove_cl_user_ref($cl_data) {
    global $wpdb;
    $table_name = $wpdb->prefix . "user_cl_prescriptions_order_ref";
    $is_deleted = $wpdb->query("DELETE FROM $table_name WHERE cl_id =" . $cl_data['cl_prescription_id'] . " AND "
            . "order_id=" . $cl_data['order_id'] . " AND order_line_item_id=" . $cl_data['order_line_item_id']);
    if ($is_deleted) {
        //echo '<span style="color:green;">Prescription reference deleted updated!</span>';
    } else {
        //echo '<span style="color:red;">There is some issue while deleting prescription reference. Please try again later.</span>';
    }
}

// delete pd images on order complete

add_action('woocommerce_order_status_completed', 'delete_order_pd_image_clu');
add_action('woocommerce_order_status_cancelled', 'delete_order_pd_image_clu');
add_action('woocommerce_order_status_refunded', 'delete_order_pd_image_clu');

function delete_order_pd_image_clu($order_id) {
    global $wpdb;
    $invalidate_order_items = $wpdb->get_results("SELECT up.id, up.pd_img_url, upor.order_id, upor.order_line_item_id "
            . "FROM " . $wpdb->prefix . "user_cl_prescriptions AS up, "
            . $wpdb->prefix . "user_cl_prescriptions_order_ref AS upor "
            . "WHERE up.id=upor.cl_id AND upor.order_id=" . $order_id, OBJECT);
    foreach ($invalidate_order_items as $item_obj) {
        if ($item_obj->pd_img_url != '') {
            if (remove_pd_img_from_db_clu($item_obj->id)) {
                unlink(getcwd() . '/..' . $item_obj->pd_img_url);
                echo '<span style="color:green;">PD image has been deleted</span>';
                // need to verify either its deleted or not
                /* if(!unlink($pd_img_url)) {
                  echo '<span style="color:green;">PD image has been deleted</span>';
                  } else {
                  echo '<span style="color:red;">PD image cannot be deleted due to an error</span>';
                  } */
            }
        }
    }
}
