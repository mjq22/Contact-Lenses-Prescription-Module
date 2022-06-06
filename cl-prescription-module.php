<?php
/**
  Plugin Name: Custom functionality for CL
  Description: This plugin contains all the custom functionality for CL module including save prescription.
  Author: MJ
  Version: 1.0
 */
defined('ABSPATH') || exit;
// for encrypting prescription images
define('ENCRYPTSALT', 'vcspres');
define('CIPHERING', 'AES-128-CTR');
global $cl_db_version;
$cl_db_version = '1.0';
if (!define('cl_plugin_path', plugin_dir_path(__FILE__))) {
    define('cl_plugin_path', plugin_dir_path(__FILE__));
}

function cl_install() {
    global $wpdb;
    global $cl_db_version;
    $installed_ver = get_option("cl_db_version");
    if ($installed_ver != $cl_db_version) {
        $table_name_prescription = $wpdb->prefix . 'user_cl_prescriptions';
        $table_name_prescription_order_ref = $wpdb->prefix . 'user_cl_prescriptions_order_ref';
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name_prescription (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `first_name` varchar(200) DEFAULT NULL,
            `last_name` varchar(200) DEFAULT NULL,
            `optician_name` varchar(200) DEFAULT NULL,
            `optician_phone` varchar(200) DEFAULT NULL,
            `cl_group` varchar(400) DEFAULT NULL,
            `optician_address` varchar(200) DEFAULT NULL,
            `patient_dob` date DEFAULT NULL,
            `prescription_img_url` varchar(400) DEFAULT NULL,
            `practice_location` varchar(255) DEFAULT NULL,
            `base_curve` varchar(22) DEFAULT NULL,
            `diameter` varchar(22) DEFAULT NULL,
            `colour` varchar(22) DEFAULT NULL,
            `sphere` varchar(22) DEFAULT NULL,
            `cylinder` varchar(22) DEFAULT NULL,
            `axis` varchar(22) DEFAULT NULL,
            `addition` varchar(22) DEFAULT NULL,
            `dominance` varchar(22) DEFAULT NULL,
            `eye_side` varchar(22) DEFAULT NULL,
            `product_reference` varchar(30) DEFAULT NULL,
            `verification_type` varchar(50) DEFAULT NULL,
            `rx_date` date DEFAULT NULL,
            `renewal` varchar(22) DEFAULT NULL,
            `rx_expiry_date` date DEFAULT NULL,
            `cl_group_id` varchar(100) DEFAULT NULL,
            `is_validated` tinyint(1) DEFAULT 0,
            PRIMARY KEY (`id`)
        ) $charset_collate;
        CREATE TABLE $table_name_prescription_order_ref (
            id int(11) NOT NULL AUTO_INCREMENT,
            `cl_id` int(11) NOT NULL,
            `order_id` int(11) NOT NULL,
            `order_line_item_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `cl_id` (`cl_id`),
            KEY `order_id` (`order_id`),
            KEY `order_line_item_id` (`order_line_item_id`)
        ) $charset_collate;";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
        update_option('cl_db_version', $cl_db_version);
    }
}

register_activation_hook(__FILE__, 'cl_install');

function myplugin_update_db_check_clu() {
    global $cl_db_version;
    if (get_option('cl_db_version') != $cl_db_version) {
        cl_install();
    }
}

add_action('plugins_loaded', 'myplugin_update_db_check_clu', 99, 2);
/* RX Module functionality start point */

// Style for Admin CL Verification
add_action('admin_enqueue_scripts', 'cl_products_scripts_admin', 2000);
function cl_products_scripts_admin() {
        wp_enqueue_style('cl-styles-admin', plugin_dir_url(__FILE__) . 'assets/css/admin_style.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/admin_style.css'), false);
}

add_action('wp_enqueue_scripts', 'cl_products_scripts', 2000);

function cl_products_scripts() {
    //wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), filemtime(get_stylesheet_directory() . '/assets/css/bootstrap.min.css') );
    if ( is_product() && has_term( 'contact-lenses', 'product_cat' ) ) {
        wp_enqueue_style('cl-styles', plugin_dir_url(__FILE__) . 'assets/css/cl_style.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/cl_style.css'), false);
        wp_enqueue_style('ui-datepicker-style', plugin_dir_url(__FILE__) . 'assets/css/datepicker.min.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/datepicker.min.css'), false);
        // wp_register_script('ui-datepicker-js', plugin_dir_url(__FILE__) . 'assets/js/datepicker.min.js', array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'assets/js/datepicker.min.js'), true);
        // wp_enqueue_script('ui-datepicker-js');
        //wp_register_script('script-cl-prescription', plugin_dir_url(__FILE__) . 'assets/js/cl-prescription.js', array('jquery'), filemtime(plugin_dir_path(__FILE__) . 'assets/js/cl-prescription.js'), true);
        //wp_enqueue_script('script-cl-prescription');
        wp_enqueue_script( 'cl-ui-datepicker-js', plugin_dir_url(__FILE__) . 'assets/js/datepicker.min.js', array(), '1.0.0', true );
        wp_enqueue_script( 'cl-script-cl-prescription', plugin_dir_url(__FILE__) . 'assets/js/cl-prescription.js', array(), '5.0.0', true );   
    }
}

// login, register and prescription saving and its relevant functionality
/**
 * Register new endpoint to use inside My Account page.
 *
 * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
 */
function cl_prescriptions_endpoints() {
    add_rewrite_endpoint('cl_prescriptions', EP_ROOT | EP_PAGES);
}

add_action('init', 'cl_prescriptions_endpoints', 20);

/**
 * Add new query var.
 *
 * @param array $vars
 * @return array
 */
function cl_prescriptions_query_vars($vars) {
    $vars[] = 'cl_prescriptions';
    return $vars;
}

add_filter('query_vars', 'cl_prescriptions_query_vars', 10);

/**
 * Insert the new endpoint into the My Account menu.
 *
 * @param array $items
 * @return array
 */
function cl_prescriptions_my_account_menu_items($items) {
    $logout_item = $items['customer-logout'];
    unset($items['customer-logout']);
    $items['cl_prescriptions'] = __('Contact Lens Prescriptions', 'woocommerce');
    $items['customer-logout'] = $logout_item;
    // Add the new item after `prescriptions`.
    return $items; //cl_prescriptions_insert_after_helper($items, $new_items, 'prescriptions');
}

add_filter('woocommerce_account_menu_items', 'cl_prescriptions_my_account_menu_items', 10);

/**
 * Custom help to add new items into an array after a selected item.
 *
 * @param array $items
 * @param array $new_items
 * @param string $after
 * @return array
 */
function cl_prescriptions_insert_after_helper($items, $new_items, $after) {
    // Search for the item position and +1 since is after the selected item key.
    $position = array_search($after, array_keys($items)) + 2;
    // Insert the new item.
    $array = array_slice($items, 1, $position, true);
    $array += $new_items;
    $array += array_slice($items, $position, count($items) - $position, true);
    return $array;
}

/**
 * Endpoint HTML content.
 */
function cl_prescriptions_endpoint_content() {
    $user_id = get_current_user_id();
    echo get_list_cl_prescription($user_id);
}

add_action('woocommerce_account_cl_prescriptions_endpoint', 'cl_prescriptions_endpoint_content', 10);

function update_cl_prescription($arr) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_cl_prescriptions';
    extract($arr);
    if (isset($patient_dob) && $patient_dob != '') {
        // changing the date formakte from d/m/Y to m/d/Y
        $arrdob = explode("/", $patient_dob);
        $patient_dob = $arrdob[1]. '/' . $arrdob[0] . '/' . $arrdob[2];
        $patient_dob = date('Y-m-d', strtotime($patient_dob));
    }
    if (isset($rx_date) && $rx_date != '') {
        // changing the date formakte from d/m/Y to m/d/Y
        $arrrxdate = explode("/", $rx_date);
        $rx_date = $arrrxdate[1]. '/' . $arrrxdate[0] . '/' . $arrrxdate[2];
        $rx_date = date('Y-m-d', strtotime($rx_date));
    }
    if (isset($rx_expiry_date) && $rx_expiry_date != '') {
        // changing the date formakte from d/m/Y to m/d/Y
        $arrrxexpdate = explode("/", $rx_expiry_date);
        $rx_expiry_date = $arrrxexpdate[1]. '/' . $arrrxexpdate[0] . '/' . $arrrxexpdate[2];
        $rx_expiry_date = date('Y-m-d', strtotime($rx_expiry_date));
    }
    //echo "<br> --------Exprity date = " . $rx_expiry_date . "---------- <br>";
    //exit();
    //right eye
    if (isset($eye_side_right)) {
        $is_updated = $wpdb->update(
                $table_name, array(
            'first_name' => trim($first_name),
            'last_name' => trim($last_name),
            'optician_name' => trim($optician_name),
            'optician_phone' => trim($optician_phone),
            'cl_group' => trim($first_name) . ' ' . trim($last_name),
            'optician_address' => $optician_address,
            'patient_dob' => $patient_dob,
            //No need to manipulate this index
            //'prescription_img_url' => $arr['prescription_img_url'],
            'practice_location' => $practice_location,
            'base_curve' => $base_curve_right,
            'diameter' => $diameter_right,
            'colour' => $color_right,
            'sphere' => $sphere_right,
            'cylinder' => $cylinder_right,
            'axis' => $axis_right,
            'addition' => $addition_right,
            'dominance' => $dominance_right,
            'eye_side' => $eye_side_right,
            'rx_date' => $rx_date,
            'renewal' => $renewal,
            'rx_expiry_date' => $rx_expiry_date,
            'is_validated' => $is_validated
                ), array(
            'id' => $cl_prescription_id_right,
            'user_id' => $user_id
                )
        );
    }
    if (isset($eye_side_left)) {
        // left eye
        $is_updated = $wpdb->update(
                $table_name, array(
            'first_name' => trim($first_name),
            'last_name' => trim($last_name),
            'optician_name' => trim($optician_name),
            'optician_phone' => trim($optician_phone),
            'cl_group' => trim($first_name) . ' ' . trim($last_name),
            'optician_address' => $optician_address,
            'patient_dob' => $patient_dob,
            //No need to manipulate this index
            //'prescription_img_url' => $arr['prescription_img_url'],
            'practice_location' => $practice_location,
            'base_curve' => $base_curve_left,
            'diameter' => $diameter_left,
            'colour' => $color_left,
            'sphere' => $sphere_left,
            'cylinder' => $cylinder_left,
            'axis' => $axis_left,
            'addition' => $addition_left,
            'dominance' => $dominance_left,
            'eye_side' => $eye_side_left,
            'rx_date' => $rx_date,
            'renewal' => $renewal,
            'rx_expiry_date' => $rx_expiry_date,
            'is_validated' => $is_validated
                ), array(
            'id' => $cl_prescription_id_left,
            'user_id' => $user_id
                )
        );
    }
    return $is_updated;
}

function cl_prescription_exist($arr) {
    extract($arr);
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if (!$user_id) {
        return 0;
    }
    global $wpdb;
    //$formated_date = str_replace('/', '-', $patient_dob);
    $uid = $wpdb->get_var(
            $wpdb->prepare(
                    "SELECT id FROM " . $wpdb->prefix . "user_cl_prescriptions
                    WHERE user_id = %d AND first_name = %s AND last_name = %s AND optician_name = %s AND optician_phone = %s AND optician_address = %s AND patient_dob = %s AND base_curve = %s
                    AND diameter = %s AND colour = %s AND sphere = %s AND cylinder = %s AND axis = %s
                    AND addition = %s AND dominance = %s AND eye_side = %s AND product_reference = %s AND verification_type = %s AND cl_group_id = %s
                    LIMIT 1", $user_id, $first_name, $last_name, $optician_name, $optician_phone, $optician_address, date('Y-m-d', strtotime($patient_dob)), $base_curve, $diameter, $colour, $sphere, $cylinder, $axis, $addition, $dominance, $eye_side, $product_reference, $verification_type, $cl_group_id
            )
    );
    if ($uid > 0) {
        return $uid;
    }
    return 0;
}

function add_cl_prescription($arr) {
    extract($arr);
    if (!$user_id) {
        $user_id = get_current_user_id();
    }
    if (!$user_id) {
        return 0;
    }
    if (!isset($is_validated)) {
        $is_validated = 0;
    }
    $arr['is_validated'] = $is_validated;
    $cl_prescription_id = cl_prescription_exist($arr);
    if ($cl_prescription_id) {
        return $cl_prescription_id; //$cl_prescription_id;
    }
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_cl_prescriptions';
    //$formated_date = str_replace('/', '-', $patient_dob);
    if (isset($patient_dob) && $patient_dob != '') {
        $patient_dob = date('Y-m-d', strtotime($patient_dob));
    }
    $num_of_inserted_rows = $wpdb->insert(
            $table_name, array(
        'user_id' => $user_id,
        'first_name' => trim($first_name),
        'last_name' => trim($last_name),
        'optician_name' => trim($optician_name),
        'optician_phone' => trim($optician_phone),
        'cl_group' => trim($first_name) . ' ' . trim($last_name),
        'optician_address' => $optician_address,
        'patient_dob' => $patient_dob,
        'prescription_img_url' => $prescription_img_url,
        'practice_location' => $practice_location,
        'base_curve' => $base_curve,
        'diameter' => $diameter,
        'colour' => $colour,
        'sphere' => $sphere,
        'cylinder' => $cylinder,
        'axis' => $axis,
        'addition' => $addition,
        'dominance' => $dominance,
        'eye_side' => $eye_side,
        'product_reference' => $product_reference,
        'verification_type' => $verification_type,
        'cl_group_id' => $cl_group_id,
        'is_validated' => $is_validated
            )
    );

    if ($num_of_inserted_rows) {
        return $wpdb->insert_id;
    }
    return 0;
}

function is_cl_prescription_ref_already_exist($cl_prescription_id, $order_id, $item_id) {
    global $wpdb;
    $uid = $wpdb->get_var(
            $wpdb->prepare(
                    "SELECT id FROM " . $wpdb->prefix . "user_cl_prescriptions_order_ref
                    WHERE cl_id = %d AND order_id = %s AND order_line_item_id = %s
                    LIMIT 1", $cl_prescription_id, $order_id, $item_id
            )
    );
    if ($uid > 0) {
        return $uid;
    }
    return 0;
}

function save_cl_prescription_order_ref($cl_prescription_id, $order_id, $item_id) {
    if ($cl_prescription_id && $order_id && $item_id) {
        $num_of_inserted_rows = is_cl_prescription_ref_already_exist($cl_prescription_id, $order_id, $item_id);
        if (!$num_of_inserted_rows) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'user_cl_prescriptions_order_ref';
            $num_of_inserted_rows = $wpdb->insert(
                    $table_name, array(
                'cl_id' => $cl_prescription_id,
                'order_id' => $order_id,
                'order_line_item_id' => $item_id
                    )
            );
        }
        if ($num_of_inserted_rows) {
            return $wpdb->insert_id;
        }
    }
    return 0;
}

function get_list_cl_prescription($user_id, $product_detail_page = false) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_cl_prescriptions';

    if (isset($_GET['pageno'])) {
        $pageno = $_GET['pageno'];
    } else {
        $pageno = 1;
    }
    $no_of_records_per_page = 10;
    $offset = ($pageno - 1) * $no_of_records_per_page;

    $allprescriptionqry = 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE user_id =' . $user_id . ' AND is_validated=1';
    if (isset($_GET['filter_by_name']) && $_GET['filter_by_name'] != '') {
        $allprescriptionqry .= ' AND cl_group="' . $_GET['filter_by_name'] . '"';
    }

    $total_rows = $wpdb->get_var($allprescriptionqry);
    $total_pages = ceil($total_rows / $no_of_records_per_page);

    $prescription_query = 'SELECT * FROM ' . $table_name . ' WHERE user_id =' . $user_id . ' AND is_validated=1';
    if (isset($_GET['filter_by_name']) && $_GET['filter_by_name'] != '') {
        $prescription_query .= ' AND cl_group="' . $_GET['filter_by_name'] . '"';
    }
    if ($product_detail_page) {
        $prescription_query .= " ORDER BY cl_group, id";
    } else {
        $prescription_query .= " ORDER BY cl_group, id LIMIT $offset, $no_of_records_per_page";
    }
    $prescriptions = $wpdb->get_results($prescription_query, OBJECT);
    $prescriptions_filter = $wpdb->get_results('SELECT cl_group FROM ' . $table_name . ' WHERE user_id =' . $user_id . ' AND is_validated=0 GROUP BY cl_group ORDER BY cl_group', OBJECT);
    ob_start();

    //echo "Site Image path = " . $climagepath . " <br> --------------- <br>";
    ?>
    <h3><?php _e('YOUR SAVED PRESCRIPTIONS', "prescriptions"); ?></h3>
    <div class="panel panel-default cl-panel">
        <div class="js-table-responsive">
            <?php if (!$product_detail_page) { ?>
                <div class="filter-area">
                    <form action="" method="get">
                        <label for="filter_by_name">Filter by name</label>
                        <select name="filter_by_name" id="filter_by_name">
                            <option value="">Select</option>
                            <?php foreach ($prescriptions_filter as $pf): ?>
                                <option value="<?= $pf->cl_group ?>"
                                <?php if (isset($_GET['filter_by_name']) && $_GET['filter_by_name'] == $pf->cl_group) { ?>
                                            selected="selected"
                                        <?php } ?>
                                        ><?= $pf->cl_group ?></option>
                                    <?php endforeach; ?>
                        </select>
                        <input type="submit" name="sub_ffm" value="Filter">
                    </form>
                </div>
            <?php } ?>
            <table class="shop_table_responsive">
                <!-- <thead>
                <th><?php _e('Prescription Name', "prescriptions") ?></th>
                <th><?php _e('Date', "prescriptions") ?></th>
                <th cols="4">Your Prescription</th>
                <th><?php
                if (!$product_detail_page) {
                    _e('Action', "prescriptions");
                }
                ?></th>
                </thead> -->
                <tbody>
                    <?php if (!empty($prescriptions)): ?>
                        <?php foreach ($prescriptions as $p): ?>
                            <tr>
                                <td>
                                    <div class="presc-header">
                                        <div class="cl-uname"><?= $p->first_name ?> <?= $p->last_name ?></div>
                                        <div class="pres-date"><span><?php if ($p->patient_dob != '') { ?>Date:<?php } ?>&nbsp;</span><?php echo $p->patient_dob; ?></div>
                                    </div><?php
                                    /*
                                      <div class="pres-img">
                                      <?php
                                      if ($p->prescription_img_url != '') { ?>
                                      <?php if (filter_var($p->prescription_img_url, FILTER_VALIDATE_URL)) { ?>
                                      <img src="<?= $p->prescription_img_url ?>" />
                                      <?php } else {
                                      $filename = decryptstringcl($p->prescription_img_url, CIPHERING, ENCRYPTSALT);
                                      $filename = str_replace('public_html/', '', $filename);
                                      if (strpos($filename, '.pdf') !== false) {
                                      echo getencryptedimgbypathclwithicon($filename);
                                      } else {
                                      $img_url = getencryptionofimgcl($filename);
                                      echo '<img src="' . $img_url . '" >';
                                      }

                                      ?>
                                      <?php } ?>
                                      <?php } ?>
                                      </div>
                                      <?php */
                                    $sphere_right = explode('_', $p->axis);
                                    $sphere_left = explode('_', $p->base_curve);
                                    $cylinder_right = explode('_', $p->addition);
                                    $cylinder_left = explode('_', $p->diameter);
                                    $dominance = explode('_', $p->dominance);
                                    $colour = explode('_', $p->colour);
                                    $sphere = explode('_', $p->sphere);
                                    $cylinder = explode('_', $p->cylinder);
                                    if ($sphere_right[0] != '' || $cylinder_right[0] != '' || $dominance[0] != '' ||
                                            $sphere_left[0] != '' || $cylinder_left[0] != '' || $colour[0] != '' || $sphere[0] != '') {
                                        ?>
                                        <div class="pres-values">
                                            <div class="js-prescription-list">
                                                <ul class="nonlist">
                                                    <li class="js-heading">
                                                        <span class="js-box1"><?php _e('EYE', "prescriptions") ?></span>
                                                        <span class="js-box2"><?php _e('SPH', "prescriptions") ?></span>
                                                        <span class="js-box3"><?php _e('DIA', "prescriptions") ?></span>
                                                        <span class="js-box4"><?php _e('BC', "prescriptions") ?></span>
                                                        <span class="js-box5"><?php _e('CYL', "prescriptions") ?></span>
                                                        <span class="js-box5"><?php _e('AXIS', "prescriptions") ?></span>
                                                        <span class="js-box5"><?php _e('ADD', "prescriptions") ?></span>
                                                        <span class="js-box5"><?php _e('CLR', "prescriptions") ?></span>
                                                        <span class="js-box5"><?php _e('D/N', "prescriptions") ?></span>
                                                    </li>
                                                    <li>
                                                        <span class="js-box1"><?= $p->eye_side ?></span>
                                                        <span class="js-box2"><?= $p->sphere ?></span>
                                                        <span class="js-box3"><?= $p->diameter ?></span>
                                                        <span class="js-box4"><?= $p->base_curve ?></span>
                                                        <span class="js-box5"><?= $p->cylinder ?></span>
                                                        <span class="js-box5"><?= $p->axis ?></span>
                                                        <span class="js-box5"><?= $p->addition ?></span>
                                                        <span class="js-box5"><?= $p->colour ?></span>
                                                        <span class="js-box5"><?= $p->dominance ?></span>                                                     
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="pres-edit">
                                        <?php
                                        if (!$product_detail_page) {
                                            if (!$p->is_validated) {
                                                ?>
                                                <a href="?cl_prescription_id=<?= $p->id ?>"
                                                   class="button prescription-add-to-cart"><?php _e('Delete', "prescriptions") ?></a>
                                                   <?php
                                               }
                                           } else {
                                               $arr_pres = htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8');
                                               echo "<input type='radio' name='cl_prescription_id' id='cl_prescription_id_" . $p->id . "' onclick='populate_prescription($arr_pres)' class='prescription-radio'> <label for='cl_prescription_id_" . $p->id . "'>Select</label>";
                                           }
                                           ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center !important;">
        <!--                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/ex-mark.png" class="no-pres-img">-->
                                <p class="no-pres-text">You have no saved prescriptions. </p>
        <!--                                <span class="add-pres-span" onclick="enterNewPrescription();"><?php _e('Add New Prescription', "prescriptions") ?></span>-->
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody> 
            </table>
            <?php if($total_pages > 1 && !$product_detail_page ) { ?>
            <ul class="pagination">
                <!-- <li><a href="?pageno=1">First</a></li> -->
                <?php //if($pageno > 1){ ?>
                <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
                    <a href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
                </li>
                <?php //}?>
                
                <?php 
                    for($i=1; $i <= $total_pages; ++$i){
                        ?>
                        <li class="<?php if($pageno === $i){ echo 'active'; } ?>">
                            <a href="<?php echo "?pageno=".$i; ?>"><?php echo $i;?></a>
                        </li>
                        <?php
                    }
                ?>
                
                <?php //if($pageno < $total_pages){ ?>
                <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                    <a href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
                </li>
                <?php //}?>
                <!-- <li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li> -->
            </ul>
            <?php } ?> 
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function load_cl_options($tax_slug) {
    $tax_values = get_terms($tax_slug, array(
        'hide_empty' => false,
    ));
    $options_html = '';
    foreach ($tax_values as $val) {
        $options_html .= '<option value="' . $val->name . '">' . $val->name . '</option>';
    }
    return $options_html;
}

function load_attribute_html($label, $key, $eye_side_val, $index, $tax_slug) {
    $values = load_cl_options($tax_slug);
    ?>
    <div class="cpf_hide_element tm-cell col-3 cpf-type-select <?= $key ?>">
        <label class="tm-epo-field-label tm-epo-element-label tm-has-required"><?= $label ?></label>
        <div class="tm-extra-product-options-container">
            <ul class="tmcp-ul-wrap tmcp-elements tm-extra-product-options-select tm-element-ul-select element_2 nonlist">
                <li class="tmcp-field-wrap">
                    <label class="ms-select tm-epo-field-label">
                        <?php if ($key == 'eye_side') { ?> 
                            <span class="eye_label"><?php echo $eye_side_val; ?></span>

                        <?php } ?>
                        <select class="tmcp-field tm-epo-field tmcp-select" name="<?= $key ?>_<?= $eye_side_val ?>"
                                id="<?= $key ?>_<?= $eye_side_val ?>_<?= $index ?>"<?php if ($key == 'eye_side') { ?> style="display: none;" <?php } ?>>
                            <option value="">Select</option>
                            <?php echo $values; ?>
                        </select>
                    </label>
                </li>
            </ul>
        </div>
    </div> <?php
}

/*
 * Returns matched post IDs for a pair of meta key and meta value from database
 *
 * @param string $meta_key
 * @param mixed $meta_value
 *
 * @return array|int Post ID(s)
 */

function post_id_by_meta_key_and_value($meta_key, $meta_value) {
    global $wpdb;
    $id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %d", $meta_key, $meta_value));
    return $id;
}

function addEditCLForm($presc_arr = array(), $is_admin = false, $index = 0) {
    ?>
    <div class="sp-add-prescription my-account-add-prescription">
        <div class="tc-extra-product-options tm-extra-product-options">
            <form name="add_prescription" action="" method="post" enctype="multipart/form-data">
                <div class="cpf-section tm-row sp-prescription-options  iscpfdependson is-epo-depend">
                    <?php if (!$is_admin) { ?>
                        <div class="cpf_hide_element cpf-type-header">
                            <div class="prescription-type-area">
                                <div class="type-wrapper">
                                    <input type="radio" id="prescription_for_existing_member" value="prescription_for_existing_member" name="prescription_type">
                                    <label for="prescription_for_existing_member"><?php _e('Prescription for existing member', 'woocommerce'); ?></label>
                                    <div id="existing_memebers">
                                        <select name="filter_by_name" id="filter_by_name" onchange="populateCLPrescriptionData(this, <?= $index ?>);">
                                            <option value="">Select</option>
                                            <?php foreach ($presc_arr as $pf): ?>
                                                <option value="<?= $pf['first_name'] . '*' . $pf['last_name'] ?>"><?= $pf['cl_group'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="type-wrapper">
                                    <input type="radio" id="prescription_for_new_member" value="prescription_for_new_member" name="prescription_type" checked="checked">
                                    <label for="prescription_for_new_member"><?php _e('Prescription for new member', 'woocommerce'); ?></label>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                        <?php
                        $pid = 0;
                        if ($presc_arr['product_reference']) {
                            $pid = post_id_by_meta_key_and_value('_product_reference', trim(strip_tags($presc_arr['product_reference'])));
                            if ($pid) {
                                $product_obj = wc_get_product($pid);
                                $attributes = $product_obj->get_attributes();
                                $eye_side_val = strtolower($presc_arr['eye_side']);
                                $alt_eye_side = '';
                                if (isset($presc_arr['eye_side_left']) || isset($presc_arr['eye_side_right'])) {
                                    if ($eye_side_val == 'left') {
                                        $alt_eye_side = 'right';
                                    } else {
                                        $alt_eye_side = 'left';
                                    }
                                }
                                for ($i = 0; $i < 2; $i++) {
                                    if ($i > 0) {
                                        if ($alt_eye_side != '') {
                                            $eye_side_val_loop = $alt_eye_side;
                                        } else {
                                            continue;
                                        }
                                    } else {
                                        $eye_side_val_loop = $eye_side_val;
                                    }
                                    echo '<div class="eye-wrapper cl-eye-wrapper">';
                                    foreach ($attributes as $attr => $attr_deets) {
                                        if ($attr == 'pa_eye-type') {
                                            load_attribute_html(__('Eye Side', 'woocommerce'), 'eye_side', $eye_side_val_loop, $index, 'pa_eye-type');
                                        }
                                        if ($attr == 'pa_sphere') {
                                            load_attribute_html(__('Sphere', 'woocommerce'), 'sphere', $eye_side_val_loop, $index, 'pa_sphere');
                                        }
                                        if ($attr == 'pa_cylinder') {
                                            load_attribute_html(__('Cylinder', 'woocommerce'), 'cylinder', $eye_side_val_loop, $index, 'pa_cylinder');
                                        }
                                        if ($attr == 'pa_axis') {
                                            load_attribute_html(__('Axis', 'woocommerce'), 'axis', $eye_side_val_loop, $index, 'pa_axis');
                                        }
                                        if ($attr == 'pa_addition') {
                                            load_attribute_html(__('Addition', 'woocommerce'), 'addition', $eye_side_val_loop, $index, 'pa_addition');
                                        }
                                        if ($attr == 'pa_base-curve') {
                                            load_attribute_html(__('Base Curve', 'woocommerce'), 'base_curve', $eye_side_val_loop, $index, 'pa_base-curve');
                                        }
                                        if ($attr == 'pa_color') {
                                            load_attribute_html(__('Color', 'woocommerce'), 'color', $eye_side_val_loop, $index, 'pa_color');
                                        }
                                        if ($attr == 'pa_diameter') {
                                            load_attribute_html(__('Diameter', 'woocommerce'), 'diameter', $eye_side_val_loop, $index, 'pa_diameter');
                                        }
                                        if ($attr == 'pa_dominance') {
                                            load_attribute_html(__('Dominance', 'woocommerce'), 'dominance', $eye_side_val_loop, $index, 'pa_dominance');
                                        }
                                    }
                                    echo '</div>';
                                }
                            }
                        }
                        ?>                   
                    <div class="separator"></div>
					<div class="clearfix"></div>
                    <div class="prescription-user-info">
                        <div class="presc-name nameclass" id="fname">
                            <label for="first_name_<?= $index ?>"><?php _e('First Name', 'woocommerce'); ?></label>
                            <input type="text" id="first_name_<?= $index ?>" name="first_name" value="<?= $presc_arr['first_name'] ?>" required="">
                        </div>
                        <div class="presc-name nameclass" id="lname">
                            <label for="last_name_<?= $index ?>"><?php _e('Last Name', 'woocommerce'); ?></label>
                            <input type="text" id="last_name_<?= $index ?>" name="last_name" value="<?= $presc_arr['last_name'] ?>" required="">
                        </div>
                        <div class="presc-name nameclass" id="dob">
                            <?php
                            $dob_date = '';
                            if ($presc_arr['patient_dob'] == '0000-00-00') {
                                $dob_date = '';
                            }else{
                                $dob_date = date('m/d/Y', strtotime($presc_arr['patient_dob']));
                            }
                            ?>
                            <label for="patient_dob_<?= $index ?>"><?php _e('Date Of Birth', 'woocommerce'); ?></label>
                            <input type="text" id="patient_dob_<?= $index ?>" name="patient_dob" value="<?= $dob_date ?>">
                        </div>
                        <div class="presc-name nameclass" id="optician_name">
                            <label for="optician_name_<?= $index ?>"><?php _e('Optician Name', 'woocommerce'); ?></label>
                            <input type="text" id="optician_name_<?= $index ?>" name="optician_name" value="<?= $presc_arr['optician_name'] ?>">
                        </div>
                        <div class="presc-name nameclass" id="optician_phone">
                            <label for="optician_phone_<?= $index ?>"><?php _e('Optician Phone', 'woocommerce'); ?></label>
                            <input type="text" id="optician_phone_<?= $index ?>" name="optician_phone" value="<?= $presc_arr['optician_phone'] ?>">
                        </div>
                        <div class="presc-name nameclass" id="optician_address">
                            <label for="optician_address_<?= $index ?>"><?php _e('Optician Address', 'woocommerce'); ?></label>
                            <input type="text" id="optician_address_<?= $index ?>" name="optician_address" value="<?= $presc_arr['optician_address'] ?>">
                        </div>
                        <div class="presc-name nameclass" id="practice_location">
                        <label for="practice_location_<?= $index ?>"><?php _e('Practice Location', 'woocommerce'); ?></label>
                        <input type="text" id="practice_location_<?= $index ?>" name="practice_location" value="<?= $presc_arr['practice_location'] ?>">
                    </div>
                    </div>
                    <div class="presc-date">
                        <label for="rx_date_<?= $index ?>"><?php _e('RX Date', 'woocommerce'); ?></label>
                        <?php
                        $prs_date = '';
                        if ($presc_arr['rx_date'] == '0000-00-00') {
                            $prs_date = '';
                        }else{
                            $prs_date = date('m/d/Y', strtotime($presc_arr['rx_date']));
                        } ?>
                        <input type="text" name="rx_date" id="rx_date_<?= $index ?>" value="<?= $prs_date ?>"
                               class="full_date" placeholder="mm/dd/yyyy">
                        <div class="presc-name nameclass">
                            <label for="renewal_period_<?= $index ?>">Renewal Period</label>
                            <?php
                            $prescription_renewal = $presc_arr['renewal'];
                            $prescription_renewal_options = array
                            (
                                '3m' => '3 Months',
                                '6m' => '6 Months',
                                '1y' => '1 Year',
                                '2y' => '2 Years'
                            );
                            echo '<select name="renewal" id="renewal_'.$index.'">';
                            foreach ($prescription_renewal_options as $value => $option)
                            {
                                $selected = $value == $prescription_renewal ? 'selected="selected"' : '';
                                echo "<option {$selected} value=\"{$value}\">{$option}</option>";
                            }
                            echo '</select>';
                            ?>
                        </div>
                        <div class="presc-name nameclass">
                            <label for="rx_expiry_date_<?= $index ?>">RX Expiry Date</label>
                            <?php
                            $prs_expiry_date = '';
                            if ($presc_arr['rx_expiry_date'] == '0000-00-00') {
                                $prs_expiry_date = '';
                            }else{
                                $prs_expiry_date = date('m/d/Y', strtotime($presc_arr['rx_expiry_date']));
                            }
                            ?>
                            <input type="text" name="rx_expiry_date" id="rx_expiry_date_<?= $index ?>" value="<?= $prs_expiry_date ?>"
                               class="full_date" placeholder="mm/dd/yyyy"> 
                        </div> <div class="clear"></div>
                    </div>
                    <div class="presc-upload">
                        <?php if ($is_admin) { ?>
                            <input type="hidden" name="presc_upload" value="<?= $presc_arr['prescription_img_url'] ?>">
                        <?php } else { ?>
                            <label for="cl-date"><?php _e('Upload Prescription', 'woocommerce'); ?></label>
                            <input type="file" name="presc_upload" accept="image/*, application/pdf" >
                            <div class="file-preview"><span class="clear-img">x</span></div>
                        <?php } ?>
                    </div>                    
                    <?php if ($is_admin) { ?> <div class="clear"></div>                        
                        <input type="hidden" name="order_id" value="<?= $presc_arr['order_id'] ?>">
                        <input type="hidden" name="order_line_item_id_<?= $eye_side_val ?>" value="<?= $presc_arr['order_line_item_id'] ?>">
                        <?php if ($alt_eye_side != '') { ?>
                            <input type="hidden" name="order_line_item_id_<?= $alt_eye_side ?>" value="<?= $presc_arr['order_line_item_id_' . $alt_eye_side] ?>">
                        <?php } ?>
                        <input type="hidden" name="user_id" value="<?= $presc_arr['user_id'] ?>">
                    <?php } ?>
                </div>
                <div class="cpfclear"></div>
                <input type="hidden" name="cl_prescription_id_<?= $eye_side_val ?>" id="cl_prescription_id_<?= $eye_side_val ?>_<?= $index ?>" value="<?= $presc_arr['id'] ?>">
                <?php if ($alt_eye_side != '') { ?>
                    <input type="hidden" name="cl_prescription_id_<?= $alt_eye_side ?>" id="cl_prescription_id_<?= $alt_eye_side ?>_<?= $index ?>" value="<?= $presc_arr['id_' . $alt_eye_side] ?>">
                <?php } ?>
                <input type="submit" name="add_pres_btn" class="button" onclick="return validate_prescription('<?= $index ?>')" value="<?php
                if ($presc_arr['id'] != '') {
                    if ($presc_arr['is_validated']== 1) {
                        echo 'Reverify';
                    } else {
                        echo 'Verify Now';
                    }
                } else {
                    echo 'Add';
                }
                ?>">
            </form>
        </div>
    </div>
    <?php
    if ($presc_arr['id']) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#eye_side_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['eye_side'] ?>"]').attr('selected', 'selected');
                $('#axis_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['axis'] ?>"]').attr('selected', 'selected');
                $('#dominance_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['dominance'] ?>"]').attr('selected', 'selected');
                $('#color_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['colour'] ?>"]').attr('selected', 'selected');
                $('#addition_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['addition'] ?>"]').attr('selected', 'selected');
                $('#base_curve_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['base_curve'] ?>"]').attr('selected', 'selected');
                $('#diameter_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['diameter'] ?>"]').attr('selected', 'selected');
                $('#sphere_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['sphere'] ?>"]').attr('selected', 'selected');
                $('#cylinder_<?= $eye_side_val ?>_<?= $index ?> option[value="<?= $presc_arr['cylinder'] ?>"]').attr('selected', 'selected');
        <?php if ($alt_eye_side != '') { ?>
                    $('#eye_side_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['eye_side_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
                    $('#axis_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['axis_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
                    $('#dominance_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['dominance_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
                    $('#color_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['colour_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
                    $('#addition_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['addition_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
                    $('#base_curve_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['base_curve_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
                    $('#diameter_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['diameter_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
                    $('#sphere_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['sphere_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
                    $('#cylinder_<?= $alt_eye_side ?>_<?= $index ?> option[value="<?= $presc_arr['cylinder_' . $alt_eye_side] ?>"]').attr('selected', 'selected');
        <?php } ?>
            });
        </script>
        <?php
    }
    ?>
    <script type="text/javascript">
        function populateCLPrescriptionData(obj, indx) {
            var cname = obj.value;
            var fields = cname.split('*');
            jQuery('#first_name_' + indx).val(fields[0]);
            jQuery('#last_name_' + indx).val(fields[1]);
        }
    </script>
    <?php
}

// ajax prescription
function ajax_cl_prescription_init() {
    // Enable the user with no privileges to run ajax_prescription() in AJAX
    add_action('wp_ajax_nopriv_ajaxclprescription', 'ajax_cl_prescription', 10);
    add_action("wp_ajax_ajaxclprescription", 'ajax_cl_prescription', 10);
}

// Execute the action only if the user isn't logged in
//if (is_user_logged_in()) {
add_action('init', 'ajax_cl_prescription_init', 10);

//}
function ajax_cl_prescription() {
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : apply_filters('woocommerce_stock_amount', $_POST['quantity']);
    $variation_id = $_POST['variation_id'];
    $variation = $_POST['variation'];
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $cart_item_key_frame = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation);
    if ($passed_validation && $cart_item_key_frame) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);
        //if (get_option('woocommerce_cart_redirect_after_add') == 'yes') {
        //wc_add_to_cart_message($product_id);
        //}
        $data = array(
            'error' => false,
            'frame_key' => $cart_item_key_frame,
            'frame_message' => 'frame is added!'
        );
        // Return fragments
        //WC_AJAX::get_refreshed_fragments();
    } else {
        //$this->json_headers();
        header('Content-Type: application/json');
        // If there was an error adding to the cart, redirect to the product page to show any errors
        $data = array(
            'error' => true,
            'frame_message' => 'frame is not added!' //'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
        );
    }
    $inserted_id = 1; //add_cl_prescription($_POST);
    if ($inserted_id) {
        echo json_encode(array_merge(array('datasave' => true, 'message' => 'prescription saved'), $data));
    } else {
        echo json_encode(array_merge(array('datasave' => false, 'message' => "You can't add more than 20! Or missing prescription values!"), $data));
    }
    die();
}

function getUserCLPrescriptionCount($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_cl_prescriptions';
    $user_prescription_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE user_id=$user_id");
    return $user_prescription_count;
}

// ajax login form
function ajax_cl_login_init() {
    wp_register_script('ajax-login-script', get_stylesheet_directory_uri() . '/assets/js/ajax-login-script.js', array('jquery'), filemtime(get_stylesheet_directory() . '/assets/js/ajax-login-script.js'), true);
    wp_enqueue_script('ajax-login-script');
    wp_localize_script('ajax-login-script', 'ajax_login_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'redirecturl' => home_url(),
        'loadingmessage' => __('Sending user info, please wait...')
    ));
    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action('wp_ajax_nopriv_ajaxcllogin', 'ajax_cl_login', 10);
}

// Execute the action only if the user isn't logged in
function execute_if_cl_user_login() {
    if (!is_user_logged_in()) {
        add_action('init', 'ajax_cl_login_init', 20);
    }
}

//add_action('init', 'execute_if_cl_user_login');

function ajax_cl_login() {
    // First check the nonce, if it fails the function will break
    check_ajax_referer('ajax-login-nonce', 'security');
    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $is_reservation_check = $_POST['is_reservation_check'];
    $info['remember'] = true;
    $user_signon = wp_signon($info, false);
    if (is_wp_error($user_signon)) {
        echo json_encode(array('loggedin' => false, 'message' => __('Wrong username or password.')));
    } else {
        $user_prescription = get_list_cl_prescription($user_signon->ID, true);
        echo json_encode(array('loggedin' => true, 'is_reservation_check' => $is_reservation_check, 'current_user_id' => $user_signon->ID, 'prescription' => $user_prescription, 'message' => __('Login successful, please select the prescription...')));
    }
    die();
}

/* RX Module functionality end point */
// attribute label as first value in dropdown.
add_filter('woocommerce_dropdown_variation_attribute_options_html', 'cl_size_options', 10); //Select Woocommerce hook from wc-template-functions.php

function cl_size_options($html) { //Run Arguements
    //$attr = get_taxonomy( $args['attribute'] ); //Select the attribute from the taxonomy
    //$label = $attr->labels->name; //Select the label
    // if Size is label of attribute then add
    /* if(trim($label) === 'Size'){
      $html = __('Testing...', 'fl-builder');
      } */
    global $product;
    //$prdattributes = get_post_meta( $product->id , '_product_attributes', true );
    $discontinuattr = $product->get_attribute('discontinued');
    $arrdisvalues = explode("|", $discontinuattr);
    $arrattrisdiscon = [];
    foreach ($arrdisvalues as $disval) {
        $disval = trim($disval);
        $arrkeyval = explode("--", $disval);
        $arrattrisdiscon['wpid_' . $arrkeyval[0]] = $arrkeyval[1];
    }
// test if product is variable
    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
        foreach ($available_variations as $key => $value) {
            //get values HERE
            if ($value['attributes'] && !empty($value['attributes']['attribute_pa_size']))
                $html = str_replace('value="' . $value['attributes']['attribute_pa_size'] . '"', 'value="' . $value['attributes']['attribute_pa_size'] . '" data-discontinued="' . $arrattrisdiscon[$value['sku']] . '"', $html);
        }
    }
    return $html; //Returns "Select a size" or "Select a color" depending on what your attribute name is.
}

/* add product cart/order meta */
add_filter('woocommerce_add_cart_item_data', 'wdm_add_cl_item_data', 99, 2);
if (!function_exists('wdm_add_cl_item_data')) {

    function wdm_add_cl_item_data($cart_item_data, $product_id) {
        /* Here, We are adding item in WooCommerce session with, opticians detail */
        extract(filter_input_array(INPUT_POST));
        $cl_group_id = $_POST['cl_group_id'];
        $cl_prescription_img = $_POST['cl_prescription_img'];
        
        session_start();
        $values_arr = array();
        if (isset($first_name)) {
            $values_arr['first_name_value'] = $_SESSION['first_name'] = $first_name;
        }
        if (isset($last_name)) {
            $values_arr['last_name_value'] = $_SESSION['last_name'] = $last_name;
        }
        if (isset($cl_type)) {
            if($cl_type == 'Enter RX') {
                $cl_type = $verification_type;
            }
            $values_arr['verification_type_value'] = $_SESSION['verification_type'] = $cl_type;
        }
        if (isset($opticians_name)) {
            $values_arr['opticians_name_value'] = $_SESSION['opticians_name'] = $opticians_name;
        }
        if (isset($opticians_phone)) {
            $values_arr['opticians_phone_value'] = $_SESSION['opticians_phone'] = $opticians_phone;
        }
        if (isset($opticians_address)) {
            $values_arr['opticians_address_value'] = $_SESSION['opticians_address'] = $opticians_address;
        }
        if (isset($patient_name)) {
            $values_arr['patient_name_value'] = $_SESSION['patient_name'] = $patient_name;
        }
        if (isset($existing_patient_dob)) {
            $values_arr['existing_patient_dob_value'] = $_SESSION['existing_patient_dob'] = $existing_patient_dob;
        }
        if (isset($cl_prescription_img)) {
            $values_arr['cl_prescription_img_value'] = $_SESSION['cl_prescription_img'] = $cl_prescription_img;
        }
        if (isset($cl_group_id)) {
            $values_arr['cl_group_id_value'] = $_SESSION['cl_group_id'] = $cl_group_id;
        }
        if ((isset($opticians_name) && empty($opticians_name)) || (isset($existing_patient_dob) && empty($existing_patient_dob))) {
            return $cart_item_data;
        } else {
            if (empty($cart_item_data)) {
                return $values_arr;
            }
            else {
                return array_merge($cart_item_data, $values_arr);
            }
        }
        unset($_SESSION['first_name']);
        unset($_SESSION['last_name']);
        unset($_SESSION['verification_type']);
        unset($_SESSION['opticians_name']);
        unset($_SESSION['opticians_phone']);
        unset($_SESSION['opticians_address']);
        unset($_SESSION['patient_name']);
        unset($_SESSION['existing_patient_dob']);
        unset($_SESSION['cl_prescription_img']);
        unset($_SESSION['cl_group_id']);

        //Unset our custom session variable, as it is no longer needed.
    }

}



add_filter('woocommerce_cart_item_name', 'wdm_add_user_custom_option_from_session_into_cart_on_file', 99, 3);
if (!function_exists('wdm_add_user_custom_option_from_session_into_cart_on_file')) {

    function wdm_add_user_custom_option_from_session_into_cart_on_file($product_name, $values, $cart_item_key) {
        /* code to add custom data on Cart & checkout Page */
        if (!empty($values['existing_patient_dob_value'])) {
            $return_string = $product_name;
            if ($values['verification_type_value'] == 'Existing Patient' && !empty($values['existing_patient_dob_value'])) {
                $return_string .= " (Prescription on File)";
            }
            return $return_string;
        } else {
            return $product_name;
        }
    }

}
add_filter('woocommerce_get_cart_item_from_session', 'wdm_get_cart_cl_items_from_session', 99, 3);
if (!function_exists('wdm_get_cart_cl_items_from_session')) {

    function wdm_get_cart_cl_items_from_session($item, $values, $key) {
        if (array_key_exists('first_name_value', $values)) {
            $item['first_name_value'] = $values['first_name_value'];
        }
        if (array_key_exists('last_name_value', $values)) {
            $item['last_name_value'] = $values['last_name_value'];
        }
        if (array_key_exists('verification_type_value', $values)) {
            $item['verification_type_value'] = $values['verification_type_value'];
        }
        if (array_key_exists('opticians_name_value', $values)) {
            $item['opticians_name_value'] = $values['opticians_name_value'];
        }
        if (array_key_exists('opticians_phone_value', $values)) {
            $item['opticians_phone_value'] = $values['opticians_phone_value'];
        }
        if (array_key_exists('opticians_address_value', $values)) {
            $item['opticians_address_value'] = $values['opticians_address_value'];
        }
        if (array_key_exists('patient_name_value', $values)) {
            $item['patient_name_value'] = $values['patient_name_value'];
        }
        if (array_key_exists('existing_patient_dob_value', $values)) {
            $item['existing_patient_dob_value'] = $values['existing_patient_dob_value'];
        }
        if (array_key_exists('cl_prescription_img_value', $values)) {
            $item['cl_prescription_img_value'] = $values['cl_prescription_img_value'];
        }
        if (array_key_exists('cl_group_id_value', $values)) {
            $item['cl_group_id_value'] = $values['cl_group_id_value'];
        }
        return $item;
    }

}
add_filter('woocommerce_checkout_cart_item_child', 'wdm_add_user_custom_option_from_session_into_cart_peterivins', 99, 3);
add_filter('woocommerce_cart_item_child', 'wdm_add_user_custom_option_from_session_into_cart_peterivins', 99, 3);
if (!function_exists('wdm_add_user_custom_option_from_session_into_cart_peterivins')) {

    function wdm_add_user_custom_option_from_session_into_cart_peterivins($product_name, $values, $cart_item_key) {
        /* code to add custom data on Cart & checkout Page */
        if (!empty($values['opticians_name_value']) || !empty($values['practice_name_value'])) {
            $return_string = $product_name . "</a><dl class='variation1'>";
            //$return_string .= "<table class='wdm_options_table' id='" . $values['product_id'] . "'>";

            if (!empty($values['first_name_value'])) {
                $return_string .= "<p><strong>Name:</strong> " . $values['first_name_value'] . " " . $values['last_name_value'] . "</p>";
            }
            if (!empty($values['opticians_name_value'])) {
                $return_string .= "<p><strong>Opticians Name:</strong> " . $values['opticians_name_value'] . "</p>";
            }
            if (!empty($values['patient_name_value'])) {
                $return_string .= "<p><strong>Name:</strong> " . $values['patient_name_value'] . "</p>";
            }
            //if(!empty($values['opticians_phone_value'])) { $return_string .= "<tr><td><strong>Opticians Phone:</strong> " . $values['opticians_phone_value'] . "</td></tr>"; }
            //if(!empty($values['opticians_address_value'])) { $return_string .= "<tr><td><strong>Opticians Address:</strong> " . $values['opticians_address_value'] . "</td></tr>"; }
            if (!empty($values['existing_patient_dob_value'])) {
                $return_string .= "<p><strong>Paitent DOB:</strong> " . $values['existing_patient_dob_value'] . "</p>";
            }
            if (!empty($values['cl_prescription_img_value'])) {
                $img_cl_url = $values['cl_prescription_img_value'];
                if (strpos($img_cl_url, '.pdf') !== false) {
                    $img_cl_url = FL_MODULE_OPTICOMMERCE_WOOCOMMERCE_URL . 'modules/woocommerce-contact-lenses-peterivins/images/pdf-icon.jpg';
                }
                $return_string .= '<p><strong>Prescription Image:</strong> <a href="' . $values['cl_prescription_img_value'] . '" target="_blank"><img src="' . $img_cl_url . '" alt="" class="img-responsive" width="150" height="150" ></a></p>';
            }
            $return_string .= "</dl>";
            return $return_string;
        } else {
            return $product_name;
        }
    }

}
add_action('woocommerce_add_order_item_meta', 'wdm_add_values_to_order_cl_item_meta', 99, 2);
if (!function_exists('wdm_add_values_to_order_cl_item_meta')) {

    function wdm_add_values_to_order_cl_item_meta($item_id, $values) {
        global $woocommerce, $wpdb;
        $first_name_value = $values['first_name_value'];
        $last_name_value = $values['last_name_value'];
        $verification_type_value = $values['verification_type_value'];
        $cl_group_id_value = $values['cl_group_id_value'];

        if (!empty($first_name_value)) {
            wc_add_order_item_meta($item_id, "First Name", $first_name_value);
        }
        if (!empty($last_name_value)) {
            wc_add_order_item_meta($item_id, "Last Name", $last_name_value);
        }
        if (!empty($verification_type_value)) {
            wc_add_order_item_meta($item_id, "Verification Type", "$verification_type_value");
        }
        if (!empty($cl_group_id_value)) {
            wc_add_order_item_meta($item_id, "_cl_group_id", $cl_group_id_value);
        }
        if (is_user_logged_in() && ($first_name_value != '' || $last_name_value != '')) {
            $cl_prescription_id = formate_cl_prescription_data_to_save($values, $item_id);
            if ($cl_prescription_id) {
                wc_add_order_item_meta($item_id, "_cl_prescription_id", $cl_prescription_id);
            }
        }
    }

}

function formate_cl_prescription_data_to_save($values, $item_id) {
    $sphere_obj = get_term_by('slug', $values['variation']['attribute_pa_sphere'], 'pa_sphere');
    $base_curve_obj = get_term_by('slug', $values['variation']['attribute_pa_base-curve'], 'pa_base-curve');
    $diameter_obj = get_term_by('slug', $values['variation']['attribute_pa_diameter'], 'pa_diameter');
    $color_obj = get_term_by('slug', $values['variation']['attribute_pa_color'], 'pa_color');
    $cylinder_obj = get_term_by('slug', $values['variation']['attribute_pa_cylinder'], 'pa_cylinder');
    $axis_obj = get_term_by('slug', $values['variation']['attribute_pa_axis'], 'pa_axis');
    $addition_obj = get_term_by('slug', $values['variation']['attribute_pa_addition'], 'pa_addition');
    $dominance_obj = get_term_by('slug', $values['variation']['attribute_pa_dominance'], 'pa_dominance');
    $eye_type_obj = get_term_by('slug', $values['variation']['attribute_pa_eye-type'], 'pa_eye-type');
    $product_reference = get_post_meta($values['product_id'], '_product_reference', true);
    $prescription_arr = array();
    $prescription_arr['user_id'] = get_current_user_id();
    $prescription_arr['first_name'] = $values['first_name_value'];
    $prescription_arr['last_name'] = $values['last_name_value'];
    $prescription_arr['optician_name'] = $values['opticians_name_value'];
    $prescription_arr['optician_phone'] = $values['opticians_phone_value'];
    $prescription_arr['optician_address'] = $values['opticians_address_value'];
    $prescription_arr['patient_dob'] = $values['existing_patient_dob_value'];
    $prescription_arr['prescription_img_url'] = $values['cl_prescription_img_value'];
    $prescription_arr['practice_location'] = '';
    $prescription_arr['base_curve'] = $base_curve_obj->name;
    $prescription_arr['diameter'] = $diameter_obj->name;
    $prescription_arr['colour'] = $color_obj->name;
    $prescription_arr['sphere'] = $sphere_obj->name;
    $prescription_arr['cylinder'] = $cylinder_obj->name;
    $prescription_arr['axis'] = $axis_obj->name;
    $prescription_arr['addition'] = $addition_obj->name;
    $prescription_arr['dominance'] = $dominance_obj->name;
    $prescription_arr['eye_side'] = $eye_type_obj->name;
    $prescription_arr['product_reference'] = $product_reference;
    $prescription_arr['verification_type'] = $values['verification_type_value'];
    $prescription_arr['cl_group_id'] = $values['cl_group_id_value'];

    $cl_prescription_id = add_cl_prescription($prescription_arr);
    if ($cl_prescription_id) {
        $order_id = wc_get_order_id_by_order_item_id($item_id);
        save_cl_prescription_order_ref($cl_prescription_id, $order_id, $item_id);
    }
    return $cl_prescription_id;
}

function cl_random_string($length) {
    $key = '';
    $keys = array_merge(range(0, 9), range('a', 'z'));
    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;
}

add_action('woocommerce_before_cart_item_quantity_zero', 'wdm_remove_user_custom_cl_data_options_from_cart', 99, 1);
if (!function_exists('wdm_remove_user_custom_cl_data_options_from_cart')) {

    function wdm_remove_user_custom_cl_data_options_from_cart($cart_item_key) {
        global $woocommerce;
        // Get cart        
        $cart = $woocommerce->cart->get_cart();
        // For each item in cart, if item is upsell of deleted product, delete it
        foreach ($cart as $key => $values) {
            if ($values['first_name_value'] == $cart_item_key ||
                    $values['last_name_value'] == $cart_item_key ||
                    $values['opticians_name_value'] == $cart_item_key ||
                    $values['opticians_phone_value'] == $cart_item_key ||
                    $values['opticians_address_value'] == $cart_item_key ||
                    $values['patient_name_value'] == $cart_item_key ||
                    $values['existing_patient_dob_value'] == $cart_item_key ||
                    $values['cl_prescription_img_value'] == $cart_item_key)
                unset($woocommerce->cart->cart_contents[$key]);
        }
    }

}
/* end product cart/order meta */
add_action('woocommerce_remove_cart_item', 'remove_cl_item', 99, 2);

function remove_cl_item($cart_item_key, $cart) {
    if (isset($cart->cart_contents[$cart_item_key]['lens_cart_item_key_value'])) {
        $lens_item_key = $cart->cart_contents[$cart_item_key]['lens_cart_item_key_value'];
        if ($lens_item_key != '' && isset($cart->cart_contents[$lens_item_key])) {
            //WC()->cart->remove_cart_item($lens_item_key);
            if (cart_item_exits_clu($lens_item_key)) {
                WC()->cart->set_quantity($lens_item_key, 0);
            }
        }
    }
    if (isset($_GET['frame_remove']) && $_GET['frame_remove'] == '1') {
        if (isset($cart->cart_contents[$cart_item_key]['frame_cart_item_key_value'])) {
            $frame_item_key = $cart->cart_contents[$cart_item_key]['frame_cart_item_key_value'];
            if ($frame_item_key != '' && isset($cart->cart_contents[$frame_item_key])) {
                if (cart_item_exits_clu($frame_item_key)) {
                    WC()->cart->set_quantity($frame_item_key, 0);
                }
            }
        }
    }
}

/*
 * @desc Force individual cart item
 */

function force_individual_cart_cl_items($cart_item_data, $product_id) {
    $unique_cart_item_key = md5(microtime() . rand());
    $cart_item_data['unique_key'] = $unique_cart_item_key;
    return $cart_item_data;
}

add_filter('woocommerce_add_cart_item_data', 'force_individual_cart_cl_items', 99, 2);
/*
 * @desc Remove quantity selector in all product type
 */

function remove_all_cl_quantity_fields($return, $product) {
    return true;
}

//add_filter( 'woocommerce_is_sold_individually', 'remove_all_cl_quantity_fields', 99, 2 );
// change component product per page.
add_filter('woocommerce_cl_component_options_per_page', 'wc_cp_cl_component_options_per_page', 99, 3);

function wc_cp_cl_component_options_per_page($results_count, $component_id, $composite) {
    $results_count = 12;
    return $results_count;
}
//menu items
add_action('admin_menu','cl_register_modifymenu', 10); 
function cl_register_modifymenu() {

    //this is the main item for the menu
    add_menu_page('CL Verification', //page title
        'CL Verification', //menu title
        'manage_woocommerce', //capabilities
        'filter_invalidate_cl_order', //menu slug
        'filter_invalidate_cl_order', //function
        '', // icon url
        40
    );
    //this submenu is HIDDEN, however, we need to add it anyways
    add_submenu_page(null, //parent slug
        'Save Invalidate Order', //page title
        'Save Invalidate Order', //menu title
        'manage_woocommerce', //capability
        'saved_invalidate_cl_order', //menu slug
        'saved_invalidate_cl_order'); //function
}

//require_once(cl_plugin_path . 'cl-admin-section.php');
//Updated by Abdullah
//cl_register_modifymenu();
require_once(cl_plugin_path . 'filter_invalidate_cl_order.php');
require_once(cl_plugin_path . 'saved_invalidate_cl_order.php');
// auto update cart count
add_filter('woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_cl_fragment', 10);
if (!function_exists('woocommerce_header_add_to_cart_cl_fragment')) {

    function woocommerce_header_add_to_cart_cl_fragment($fragments) {
        global $settings;
        $cart_items_header = WC()->cart->get_cart();
        $cart_count_top = 0;
        $group_id_frame = 0;
        foreach ($cart_items_header as $cart_item_key => $cart_item) {
            if ($group_id_frame !== $cart_item['group_id'] || !isset($cart_item['group_id'])) {
                $cart_count_top++;
            }
            $group_id_frame = $cart_item['group_id'];
        }
        ob_start();
        ?>
        <a class="js-cart" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>">
            <i class="<?= $settings->cart_icon ?>"></i>
            <span class="cart-menu-items"><?php echo sprintf(_n('%d', '%d', $cart_count_top, 'woothemes'), $cart_count_top); ?></span>        
        </a>
        <?php
        $fragments['a.js-cart'] = ob_get_clean();
        return $fragments;
    }

}

add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'cinchws_filter_dropdown_args', 10 );

function cinchws_filter_dropdown_args( $args ) {
    if (strpos($args['attribute'], 'pa_rx-') !== false) { return $args; }
    $var_tax = get_taxonomy( $args['attribute'] );
    $label_arr = explode('Product ', $var_tax->labels->name);
    $args['show_option_none'] = apply_filters( 'the_title', $label_arr[1] ); 
    return $args;
}
/* Shoaib Prescription Image Start */
/* woocommerce on order saved first move the prescription images to out side of public directory */

// define the woocommerce_saved_order_items callback 
function generateimagetoencodedcl($imageurl) {
    $rootonelevel1 = str_replace('public_html/', '', dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
    $diroutroot = $rootonelevel1 . 'cl_images';
    /* if (defined('SVNIMGPATH')) {
      $diroutroot = SVNIMGPATH;
      } */

    if (!is_dir($diroutroot)) {
        mkdir($diroutroot, 0755, true);
    }

    $imgpath = str_replace(get_site_url(), $_SERVER['DOCUMENT_ROOT'], $imageurl);
    $returnurl = $imageurl;
    /* if there is parameter in page url */
    // move file from current directory to another directory
    if (file_exists($imgpath)) {
        $imgbase64 = imgtobase64cl($imgpath);
        $returnurl = $imgpath . ".txt";
        $uploadfilepath = $diroutroot . '/' . basename($returnurl);
        $uploadfilepath = trim($uploadfilepath);

        if (file_exists($uploadfilepath)) {
            $returnurl = $imgpath . time() . ".txt";
            $uploadfilepath = $diroutroot . '/' . basename($returnurl);
            $uploadfilepath = trim($uploadfilepath);

            if (file_put_contents($uploadfilepath, $imgbase64) === false) {
                //echo "<br> -----  file not saved.. ---- <br>";
            }
        } else {
            if (file_put_contents($uploadfilepath, $imgbase64) === false) {
                //echo "<br> -----  file not saved.. ---- <br>";
            }
        }
    }

    $ivlen = openssl_cipher_iv_length(CIPHERING);
    $iv = openssl_random_pseudo_bytes($ivlen);
    // Use openssl_encrypt() function to encrypt the data 
    $encryption = encryptstringcl($uploadfilepath, CIPHERING, ENCRYPTSALT,0,$iv);
    // concating the random binary or pesudo bytes to hexa formate with the encrypted image path to get this hex value and use it in every time
    // when we are showing the image or pdf.
    return $encryption . "%RANDBYTES%" . bin2hex($iv);
}

function imgtobase64cl($path) {
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

function encryptstringcl($thestring, $chipering, $encryptsalt, $option=0, $iv=0) {
    return openssl_encrypt(trim($thestring), $chipering, $encryptsalt, $option, $iv);
    //return openssl_encrypt(trim($thestring), $chipering, $encryptsalt);
}

function decryptstringcl($encryptedstring, $chipering, $encryptsalt, $option=0, $iv=0) {
    return openssl_decrypt($encryptedstring, $chipering, $encryptsalt, $option, $iv);
    //return openssl_decrypt($encryptedstring, $chipering, $encryptsalt);
}

/*get the link which will show this file in window of filepath*/
function getencryptedimgbypathcl($filepath) {
    //if in filename an extra string is contacted then explode it in array
    $arrfiles = explode("EXTRAFILE", $filepath);
    $filepath = $arrfiles[0];
    $extraencodedurl = '';
    if(count($arrfiles) > 1){
        $extraencodedurl = $arrfiles[1];
    }
    //$base64strImg = file_get_contents($filepath);
    $filename = basename($filepath);
    $showingname = str_replace(".txt", '', $filename);
    $encodename = str_replace(".", '%dot%', $filename);
    //return '<img id="my_image" src="' . $base64strImg . '" />';
    return '<a href="' . WP_PLUGIN_URL . '/opticommerce-cl/downloadimage.php?filename=' . $encodename . 'EXTRAFILE' . $extraencodedurl . '" class="popup" target="_blank">' . $showingname . '</a>';
}

/*only get the url of the file provided in parameter*/
function getencryptedimgurlbypathcl($filepath) {
    //if in filename an extra string is contacted then explode it in array
    $arrfiles = explode("EXTRAFILE", $filepath);
    $filepath = $arrfiles[0];
    $extraencodedurl = '';
    if(count($arrfiles) > 1){
        $extraencodedurl = $arrfiles[1];
    }
    //$base64strImg = file_get_contents($filepath);
    $filename = basename($filepath);
    $showingname = str_replace(".txt", '', $filename);
    $encodename = str_replace(".", '%dot%', $filename);
    //return '<img id="my_image" src="' . $base64strImg . '" />';
    return WP_PLUGIN_URL . '/opticommerce-cl/downloadimage.php?filename=' . $encodename . 'EXTRAFILE' . $extraencodedurl;
}

/*get the link with pdf icon which will show this file in window of filepath*/
function getencryptedimgbypathclwithicon($filepath) {
    //if in filename an extra string is contacted then explode it in array
    $arrfiles = explode("EXTRAFILE", $filepath);
    $filepath = $arrfiles[0];
    $extraencodedurl = '';
    if(count($arrfiles) > 1){
        $extraencodedurl = $arrfiles[1];
    }
    //$base64strImg = file_get_contents($filepath);
    $filename = basename($filepath);
    $showingname = str_replace(".txt", '', $filename);
    $encodename = str_replace(".", '%dot%', $filename);
    //return '<img id="my_image" src="' . $base64strImg . '" />';
    return '<a href="' . WP_PLUGIN_URL . '/opticommerce-cl/downloadimage.php?filename=' . $encodename . 'EXTRAFILE' . $extraencodedurl . '" target="_blank"><img src="' . WP_PLUGIN_URL . '/opticommerce-cl/assets/img/pdf.png" alt="' . $showingname . '" title="' . $showingname . '"></a>';
}

function getencryptionofimgcl($filepath) {
    //if in filename an extra string is contacted then explode it in array
    $arrfiles = explode("EXTRAFILE", $filepath);
    $filepath = $arrfiles[0];
    $extraencodedurl = '';
    if(count($arrfiles) > 1){
        $extraencodedurl = $arrfiles[1];
    }
    return file_get_contents($filepath);
}

function get_cl_prescriptionid_byorderid($order_id, $item_id) {
    global $wpdb;
    $uid = $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT upref.id, upref.user_id, upref.optician_name, upref.optician_phone, upref.cl_group, upref.optician_address, upref.optician_address, upref.prescription_img_url FROM " . $wpdb->prefix . "user_cl_prescriptions_order_ref upref INNER JOIN " . $wpdb->prefix . "user_cl_prescriptions up ON upref.cl_id = up.id 
                    WHERE order_id = %s AND order_line_item_id = %s
                    LIMIT 1", $order_id, $item_id
            )
    );
    if ($uid) {
        return $uid;
    }
    return 0;
}

// define the woocommerce_display_item_meta callback 
function filter_woocommerce_display_item_meta_clu($html, $item, $args) {
    // make filter magic happen here... 
    $arr_metas = explode('Upload Prescription:', $html);
    $arr_metas2 = array();
    $theencimg = '';
    if (!empty($arr_metas[1]))
        $arr_metas2 = explode('</p>', $arr_metas[1]);
    if (!empty($arr_metas2[0])) {
        //echo "<br> -------------------- <br>";
        //echo strip_tags($arr_metas2[0]);
        //echo "<br> -------------------- <br>";
        $theimgpath = strip_tags($arr_metas2[0]);
        $theimgpath = trim($theimgpath);
        // Explode to break encrypted image path in array index 0 and the random key hex value in index 1
        // Now with encrypted file path we ar saving the hex value of random presudo bytes also
        $encrptexplode = explode('%RANDBYTES%', $theimgpath);
        $theimgpath = $encrptexplode[0];
                                            
        // check if the current string is not url then it will be encrypted image
        //if(strpos($theimgpath, '.jpg') === false && strpos($theimgpath, '.jpeg') === false && strpos($theimgpath, '.png') === false && strpos($theimgpath, '.gif') === false){
        if (strposa_clu($theimgpath, ['.jpg', '.jpeg', '.png', '.gif', '.pdf']) === false) {
            $ivlen = openssl_cipher_iv_length(CIPHERING);
            $iv = openssl_random_pseudo_bytes($ivlen);
            if(count($encrptexplode) > 1){
                $decriptfilename = decryptstringcl($theimgpath, CIPHERING, ENCRYPTSALT, 0, hex2bin($encrptexplode[1]));
                $decriptfilename .= 'EXTRAFILE' . $encrptexplode[1];
            } else{
                $ivlen = openssl_cipher_iv_length(CIPHERING);
                $iv = openssl_random_pseudo_bytes($ivlen);
                $decriptfilename = decryptstringcl($theimgpath, CIPHERING, ENCRYPTSALT, 0, $iv);
                $decriptfilename .= 'EXTRAFILE' . bin2hex($iv);
            }
            //$decriptfilename = decryptstringcl($theimgpath, CIPHERING, ENCRYPTSALT,0,$iv);
            $theimage = getencryptedimgbypathcl($decriptfilename);
            $html = str_ireplace($theimgpath, $theimage, $html);
        }
    }
    return $html;
}

// add the filter 
add_filter('woocommerce_display_item_meta', 'filter_woocommerce_display_item_meta_clu', 99, 3);

function strposa_clu($haystack, $needles = array(), $offset = 0) {
    $chr = array();
    foreach ($needles as $needle) {
        $res = strpos($haystack, $needle, $offset);
        if ($res !== false)
            $chr[$needle] = $res;
    }
    if (empty($chr))
        return false;
    return min($chr);
}

/**
 * Changing a meta value
 * @param  string        $value  The meta value
 * @param  WC_Meta_Data  $meta   The meta object
 * @param  WC_Order_Item $item   The order item object
 * @return string        The title
 */
function change_order_item_meta_value_clu($value, $meta, $item) {
    // By using $meta-key we are sure we have the correct one.
    //echo 'Upload your prescription === ' . $meta->key;
    if ('Upload your prescription' === $meta->key || 'Upload your prescriptions' === $meta->key) {
        //echo "<br> -------------- <br>";
        //echo $value;
        //echo "<br> -------------- <br>";
        $trippedtagval = strip_tags($value);
        // Explode to break encrypted image path in array index 0 and the random key hex value in index 1
        // Now with encrypted file path we ar saving the hex value of random presudo bytes also
        $encrptexplode = explode('%RANDBYTES%', $trippedtagval);
        $trippedtagval = $encrptexplode[0];
        if (strposa_clu($trippedtagval, ['.jpg', '.jpeg', '.png', '.gif', '.pdf']) === false) {
            //$decriptfilename = decryptstringcl($value, CIPHERING, ENCRYPTSALT);
            if(count($encrptexplode) > 1){
                $decriptfilename = decryptstringcl($theimgpath, CIPHERING, ENCRYPTSALT, 0, hex2bin($encrptexplode[1]));
                $decriptfilename .= 'EXTRAFILE' . $encrptexplode[1];
            } else{
                $ivlen = openssl_cipher_iv_length(CIPHERING);
                $iv = openssl_random_pseudo_bytes($ivlen);
                
                $decriptfilename = decryptstringcl($theimgpath, CIPHERING, ENCRYPTSALT, 0, $iv);
                $decriptfilename .= 'EXTRAFILE' . bin2hex($iv);
            }
            $value = getencryptedimgbypathcl($decriptfilename);
        }
    }
    return $value;
}

add_filter('woocommerce_order_item_display_meta_value', 'change_order_item_meta_value_clu', 20, 3);
/* Shoaib Prescription Image End */

add_filter('facetwp_query_args', function( $query_args, $class ) {
    $query_args['posts_per_page'] = 12;
    return $query_args;
}, 99, 2);

function is_cl_product($product_id=0) {
	if(!$product_id) {
        global $post;
        $product_id = $post->ID;
    }
    $terms = wp_get_post_terms($product_id, 'product_cat');
    foreach ($terms as $term) {
        if (in_array($term->slug, array('contact-lenses'))) {
            return true;
        }
    }
    return false;
}

function cart_item_exits_clu($cart_key) {
    $check_cart_items = WC()->cart->get_cart();
    $is_cart_exist = false;
    foreach ($check_cart_items as $cart_item_key => $cart_item) {
        if ($cart_item_key == $cart_key) {
            $is_cart_exist = true;
            break;
        }
    }
    return $is_cart_exist;
}

add_filter('woocommerce_hidden_order_itemmeta', 'hidden_order_cl_itemmeta', 51);

function hidden_order_cl_itemmeta($args) {
    $args[] = '_cl_group_id';
    $args[] = '_cl_prescription_id';
    return $args;
}

add_filter('woocommerce_continue_shopping_redirect', 'bbloomer_change_continue_shopping_clu', 10);

function bbloomer_change_continue_shopping_clu() {
    return wc_get_page_permalink('shop');
}

// Add IN LAB status
// add another status same like processing
function wc_renaming_order_status_clu($order_statuses) {
    foreach ($order_statuses as $key => $status) {
        if ('wc-processing' === $key) {
            $order_statuses['wc-in-lab'] = _x('IN LAB', 'Order status', 'woocommerce');
        }
    }
    return $order_statuses;
}

add_filter('wc_order_statuses', 'wc_renaming_order_status_clu', 10);

/**
 * Register new status
 * Tutorial: http://www.sellwithwp.com/woocommerce-custom-order-status-2/
 * */
function register_processed_order_status_clu() {
    register_post_status('wc-in-lab', array(
        'label' => 'IN LAB',
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('IN LAB <span class="count">(%s)</span>', 'IN LAB <span class="count">(%s)</span>')
    ));
}

add_action('init', 'register_processed_order_status_clu', 10);

// add to cart cl products
add_action('wp_ajax_woocommerce_add_to_cart_cl_products_callback', 'woocommerce_add_to_cart_cl_products_callback', 10);
add_action('wp_ajax_nopriv_woocommerce_add_to_cart_cl_products_callback', 'woocommerce_add_to_cart_cl_products_callback', 10);

function woocommerce_add_to_cart_cl_products_callback() {
	ob_start();
    $data = array();
    $_POST['cl_group_id'] = cl_random_string(40);
    if(isset($_FILES) && $_FILES["presc_upload"]["name"] != '') { 
    $cl_prescription_img = '';
        // save upload prescription
        $base = dirname(__FILE__);
        if (!is_dir($base . "/../../uploads/cl_prescription_imgs")) {
            mkdir($base . "/../../uploads/cl_prescription_imgs", 0755, true);
        }
        $target_dir = $base . "/../../uploads/cl_prescription_imgs/";
        $target_file = $target_dir . basename($_FILES["presc_upload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
        /* if (isset($_POST["add_pres_btn"])) {
          $check = getimagesize($_FILES["presc_upload"]["tmp_name"]);
          if ($check !== false) {
          //echo "File is an image - " . $check["mime"] . ".";
          $uploadOk = 1;
          } else {
          echo "File is not an image.";
          $uploadOk = 0;
          }
          } */
// Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 1;
        }
// Check file size
        if ($_FILES["presc_upload"]["size"] > 10000000) { // 10 mb
            $uploadOk = 0;
        }
// Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "pdf") {
            $uploadOk = 0;
        }
// Check if $uploadOk is set to 0 by an error
        if ($uploadOk) {
            if (move_uploaded_file($_FILES["presc_upload"]["tmp_name"], $target_file)) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }
        }
        if ($uploadOk) {
            $cl_prescription_img = generateimagetoencodedcl(get_site_url() . '/wp-content/uploads/cl_prescription_imgs/' . basename($_FILES["presc_upload"]["name"]));
            unlink($target_file);
        }
        $_POST['cl_prescription_img'] = $cl_prescription_img;
    }
    extract(filter_input_array(INPUT_POST));
    if (!$variation_id) {
        //$variation_id = WC_Product_Variation::get_variation_id();	
        echo json_encode($variation);
        die();
    }
    if (isset($variations) && $variations != '') {
        $variations = json_decode($variations);
    }
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($product_id));
    if (isset($right_qty) && $right_qty != '0') {
        $right_qty = empty($right_qty) ? 1 : apply_filters('woocommerce_stock_amount', $right_qty);
        $variation['attribute_pa_eye-type'] = 'right';
        if (isset($attribute_pa_color) && $attribute_pa_color != '') {
            $variation['attribute_pa_color'] = $attribute_pa_color;
        }
        foreach ($variations as $key => $obj_values) {
            if (strpos($key, 'attribute_') === false) {
                $key = 'attribute_' . $key;
            }
            $variation[$key] = stripslashes($obj_values->right);
        }
        $data[] = add_cl_product_to_cart($product_id, $variation_id, $variation, $right_qty);
    }
    if (isset($left_qty) && $left_qty != '0') {
        $left_qty = empty($left_qty) ? 1 : apply_filters('woocommerce_stock_amount', $left_qty);
        $variation['attribute_pa_eye-type'] = 'left';
        if (isset($attribute_pa_color) && $attribute_pa_color != '') {
            $variation['attribute_pa_color'] = $attribute_pa_color;
        }
        foreach ($variations as $key => $obj_values) {
            if (strpos($key, 'attribute_') === false) {
                $key = 'attribute_' . $key;
            }
            $variation[$key] = stripslashes($obj_values->left);
        }
        $data[] = add_cl_product_to_cart($product_id, $variation_id, $variation, $left_qty); 
    }
	
	//echo json_encode(array('product_id' => $product_id, 'variation_id', $variation_id, 'variation' => $variation, 'QNT' => $left_qty));
	//wp_die();
	
    // Return fragments
    WC_AJAX::get_refreshed_fragments();
    echo json_encode($data);
    die();
}

function add_cl_product_to_cart($product_id, $variation_id, $variation, $quantity) {
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation)) {
        do_action('woocommerce_ajax_added_to_cart', $product_id, 10);
        if (get_option('woocommerce_cart_redirect_after_add') == 'yes') {
            wc_add_to_cart_message($product_id);
        }
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        wc_setcookie('woocommerce_items_in_cart', count($items));
        wc_setcookie('woocommerce_cart_hash', md5(json_encode($items)));
        do_action('woocommerce_set_cart_cookies', true, 10);
        // Return fragments
        //WC_AJAX::get_refreshed_fragments();
    } else {
        $this->json_headers();

        // If there was an error adding to the cart, redirect to the product page to show any errors
        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
        );
    }
    return $data;
}