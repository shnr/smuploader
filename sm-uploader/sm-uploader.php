<?php
/*
Plugin Name: SM-Uploaders
Plugin URI: -
Description: Image uploader for any purpose. For example, slider Images.
Author: shotanara@shnr.net
Version: 0.1.0
Author URI: http://blog.shnr.net
License: GPLv2
Text Domain: sm-uploader
Domain Path: /lang

*/
const DOMAIN = "sm-uploader";
const OPTION_BASE_NAME = "sm_img";

new my_plugin();

class my_plugin {

function __construct()
{
    load_plugin_textdomain(DOMAIN, false, dirname( plugin_basename( __FILE__ ) ).'/lang');
    //load_plugin_textdomain(self::DOMAIN, false, basename( dirname( __FILE__ ) ).'/lang' );
    add_action('admin_menu', array(&$this, 'admin_menu'));
}

public function admin_menu()
{
    $hook = add_menu_page(
        __('SM Uploader', DOMAIN),
        __('SM Uploader', DOMAIN),
        'update_core',
        DOMAIN,
        array(&$this, 'admin_page')
    );
    add_action('admin_print_scripts-'.$hook, array(&$this, 'admin_scripts')); 

    wp_register_script( 'jquery-ui', plugin_dir_url(__FILE__)  . '/lib/js/jquery-ui-1.9.2.custom.min.js');
    wp_enqueue_script( 'jquery-ui' );
}

/*
 * Admin page begin
 */
public function admin_page()
{
    // define field names
    $hidden_field_name = "hf";


    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
    // Get posted value
        $opt_imgs = $_POST['sm_img'];
        $opt_titles = $_POST['sm_title'];

        $imgAmt = count($opt_imgs);// amount of uploaded image
        if($imgAmt > 0):
            $num = 0;
            for($i=0; $i<$imgAmt; $i++){

                if($opt_imgs[$i] != ''):
                    // save option if image is exit.
                    update_option( OPTION_BASE_NAME . "_" . $i, $opt_imgs[$i] );
                    update_option( OPTION_BASE_NAME . "_title_" . $i, $opt_titles[$i] );
                    $num++;
                endif;
                
            }
            // record how many options updated,
            $num = ($num == 0)? $num+1 : $num; 
            update_option( OPTION_BASE_NAME . "_amt", $num );

        endif;
    
?>
<div class="updated"><p><strong><?php _e('Success!!', DOMAIN) ?></strong></p></div>
<?php
    }

?>
<style type="text/css">
#galleryArea img
{
    max-width: 80px;
    max-height: 80px;
    margin: 5px;
    border: 1px solid #cccccc;
}

#galleryArea ul li{
    border:1px solid #dfdfdf;
    background-color: #f9f9f9;
    margin: 5px;
    padding: 5px;
    cursor: move;
}

#galleryArea .addimg ul li{
    border:none;
    display: inline;
} 

#galleryArea table{
width: 100%;
}

</style>
<div class="wrap" >
<h2><?php _e('Gallery image uploader', DOMAIN) ?></h2>
<div id='poststuff'>

<div id='galleryArea' class="postbox">
<h3 class=""><span><?php _e('Add images', DOMAIN); ?></span></h3>
<div class="inside">
<form name="form" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
    <ul class='gal' id="sortable">

<?php
    $amtImages = (get_option(OPTION_BASE_NAME . "_amt"))? get_option(OPTION_BASE_NAME . "_amt") : 1;

    for($i=0; $i<$amtImages; $i++){

        $imgId = (get_option(OPTION_BASE_NAME . "_" . $i))? (get_option(OPTION_BASE_NAME . "_" . $i)) : "";
        $image = wp_get_attachment_image( $imgId);
        $imgTitle = (get_option(OPTION_BASE_NAME . "_title_" . $i))? (get_option(OPTION_BASE_NAME . "_title_" . $i)) : "";

?>
        <li id="gal_<?php echo $i; ?>" class="cont">
            <table>
                <tr>
                <td width="25%">
                    <label class="title"><?php _e('Title', DOMAIN) ?>:</label>
                    <input type="text" name="sm_title[]" value="<?php echo $imgTitle; ?>" class="title" size="40">
                </td>
                <td width="10%">
                    <label class="img"><?php _e('Image', DOMAIN) ?>:</label><button class="demo-media" class="button"><?php _e('Select image', DOMAIN) ?></button>
                </td>
                <td width="15%">
                    <div class="img">
                    <?php echo $image; ?>
                    <input type="hidden" name="sm_img[]" value="<?php echo $imgId; ?>" class="img">
                    </div>
                </td>
                <td>
                    <div class="addimg">
                        <ul>
                            <li><a href="#" class="add"><?php _e('Add image', DOMAIN) ?></a></li>
                            <li><a href="#" class="remove" <?php echo ($amtImages <= 1)? 'style="display:none"':''; ?>><?php _e('Remove image', DOMAIN) ?></a></li>
                        </ul>
                    </div>
                </td>
                </tr>
            </table>
        </li>
<?php
    }
?>
    </ul>
<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', DOMAIN) ?>" />
</form>
</div><!--/.inside-->
</div><!--/#galleryArea-->
</div>


</div><!-- .wrap -->
<?php
}
/*
 * Admin page end
 */


public function admin_scripts()
{
    wp_enqueue_media(); 
    wp_enqueue_script(
        'sm-uploader',
        plugins_url("/lib/js/sm-uploader.js", __FILE__),
        array('jquery'),
        filemtime(dirname(__FILE__).'/lib/js/sm-uploader.js'),
        false
    );
}


/**
 * get_gallery_images
 *
 * @return Array return attachment ids.
 */
public function get_gallery_images()
{
    $result = array();
    $amtImages = (get_option(OPTION_BASE_NAME . "_amt"))? get_option(OPTION_BASE_NAME . "_amt") : 
    "" ;

    for($i=0; $i<$amtImages; $i++){
        $imgId = (get_option(OPTION_BASE_NAME . "_" . $i))? (get_option(OPTION_BASE_NAME . "_" . $i)) : "";
        $imgTitle = (get_option(OPTION_BASE_NAME . "_title_" . $i))? (get_option(OPTION_BASE_NAME . "_title_" . $i)) : "";
        $imgInfo = array($imgId, $imgTitle);
        $result[] = $imgInfo;

    }

    return $result;
}

}



function get_gal(){
    $results = array();
    $gallery = new my_plugin();
    $results = $gallery->get_gallery_images();
    return $results;
}