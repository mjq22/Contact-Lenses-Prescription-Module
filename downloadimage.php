<?php

define("WP_ROOT", __DIR__);
define("DS", DIRECTORY_SEPARATOR);
define('ENCRYPTSALT', 'vcspres');
define('CIPHERING', 'AES-128-CTR');

$ivlen = openssl_cipher_iv_length(CIPHERING);
$iv = openssl_random_pseudo_bytes($ivlen);

$rootonelevel1 = str_replace('public_html/', '', dirname($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR);
$diroutroot = $rootonelevel1 . 'cl_images';
define('DIROUTROOT', $diroutroot);
require_once WP_ROOT . DS . "../../../wp-load.php";

global $wpdb;


$user = wp_get_current_user();
$allowed_roles = array('administrator', 'shop_manager');
$getthefilename = filter_var($_GET['filename'], FILTER_SANITIZE_STRING);
//get explode the filename because in file name we are passing random pesudo bytes in hexa format
$arrfilename = explode("EXTRAFILE", $getthefilename);

if(count($arrfilename) > 1){
    $thefilename = str_replace('%dot%', '.', $arrfilename[0]);
    $iv = hex2bin($arrfilename[1]);
} else {
    $thefilename = str_replace('%dot%', '.', $getthefilename);
}

$uid = $wpdb->get_var(
        $wpdb->prepare(
                "SELECT id FROM " . $wpdb->prefix . "user_prescriptions
                    WHERE user_id = %d AND prescription_img_url = %s
                    LIMIT 1", $user->ID, encryptstringcl(DIROUTROOT . '/' . $thefilename, CIPHERING, ENCRYPTSALT, 0, $iv)
        )
);

if (array_intersect($allowed_roles, $user->roles) || $uid) {
    // Stuff here for allowed roles
    // defines for folders


    /* if there is parameter in page url */
    if (!empty($getthefilename)) {
        //echo "File found = " . $_GET['filename'] . "<br>";
        if (file_exists(DIROUTROOT . "/" . $thefilename)) {
            //echo "Download the file : " . $thefilename;
            //exit();
            $base64strImg = file_get_contents(DIROUTROOT . "/" . $thefilename);
            //echo $base64strImg;
            //echo "<br> ----------------------------------------------- <br> -------------------------- <br> ---------------------------- <br>";
            // to download this image we can use the following code
            /* header('Content-Description: File Transfer');
              header("Content-type: application/octet-stream");
              header("Content-disposition: attachment; filename= " . str_replace('.txt', '', $thefilename) . "");
              $arrbase64 = explode("base64,", $base64strImg);

              if(!empty($arrbase64[1]))
              exit(base64_decode($arrbase64[1])); */
            if (strpos($thefilename, '.pdf') !== false) {
                header("Content-type: application/pdf");

                //print base64 decoded
                $arrbase64 = explode("base64,", $base64strImg);

                if (!empty($arrbase64[1]))
                    exit(base64_decode($arrbase64[1]));
            } else {
                echo '<img id="my_image" src="' . $base64strImg . '" />';
            }
            exit();
        } else {
            //echo "Else " . DIROUTROOT . "/" . $thefilename;
            echo "File dosen't exist.";
            exit();
        }
    } else {
        echo "Full Else .... <br>";
        exit();
    }
} else {
    echo "Nothing in file.";
}
?>