<?php
/**
 * Plugin Name: Repeater Entries Widget
 * Plugin URL: https://wordpress.org/plugins/repeater-entries-widget/
 * Description:  Let users to enter as many entries as they want in widget with repeater fields like title, description, image, etc.
 * Version: 1.4
 * Author: ZealousWeb
 * Author URI: http://zealousweb.com
 * Developer: The Zealousweb Team
 * Developer E-Mail: opensource@zealousweb.com
 * Text Domain: repeater-entries-widget
 * Domain Path: /languages
 *
 * Copyright: © 2009-2015 ZealousWeb Technologies.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 *
 * @access public
 * @since  1.3
 * @return $content
 */

if (! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if (!defined('ZWREW_VERSION') ) {
    define('ZWREW_VERSION', '1.4'); // Version of plugin
}

if (!defined('ZWREW_FILE') ) {
    define('ZWREW_FILE', __FILE__); // Plugin File
}

if (!defined('ZWREW_DIR') ) {
    define('ZWREW_DIR', dirname(__FILE__)); // Plugin dir
}

if (!defined('ZWREW_PLUGIN_PATH') ) {
    define('ZWREW_PLUGIN_PATH', plugin_dir_path(__FILE__)); // Plugin Path
}

if (!defined('ZWREW_URL') ) {
    define('ZWREW_URL', plugin_dir_url(__FILE__)); // Plugin url
}

if (!defined('ZWREW_PLUGIN_BASENAME') ) {
    define('ZWREW_PLUGIN_BASENAME', plugin_basename(__FILE__)); // Plugin base name
}

if (!defined('ZWREW_PREFIX') ) {
    define('ZWREW_PREFIX', 'zwrew'); // Plugin prefix
}

if (!defined('ZWREW_TEXT_DOMAIN') ) {
    define('ZWREW_TEXT_DOMAIN', 'repeater-entries-widget'); // Plugin text domain
}

/**
 * include admin and front file
 */
if (is_admin() ) {
    include_once ZWREW_PLUGIN_PATH . '/inc/admin/' . ZWREW_PREFIX . '.admin.php';
} else {
    include_once ZWREW_PLUGIN_PATH . '/inc/front/' . ZWREW_PREFIX . '.front.php';
}

/**
 * Repeater Entries Widget Class
 */
add_action(
    'widgets_init', function () {
        return register_widget("ZWREW_Plugin");
    }
);
class ZWREW_Plugin extends WP_Widget
{

    /**
     * constructor -- name this the same as the class above
     */
    function __construct()
    {
        parent::__construct(false, $name = __('Repeater Entries Widget', ZWREW_TEXT_DOMAIN));
    }

    /**
     * @see WP_Widget::widget -- do not rename this
     */
    function widget( $args, $instance )
    {
        extract($args);
        $max_entries = esc_attr(get_option('rew_max'));
        $max_entries = (empty($max_entries)) ? '5' : $max_entries;
        $link_target = get_option('rew_link_target');
        $alignment   = esc_attr(get_option('content_align'));

        $title   = $before_title.apply_filters('widget_title', $instance['title']).$after_title;
        echo $before_widget; //phpcs:ignore
        echo $title; //phpcs:ignore
        for($i=0; $i<$max_entries; $i++)
        {
        if (isset($instance['block-' . $i])) {
            $block = $instance['block-' . $i];
        }
            if(isset($block) && $block != "") {
                //Caption
                $caption = esc_attr($instance['caption-' . $i]);
                $caption_link = esc_url($instance['caption_link-' . $i]);
                if(get_option('rew_caption_link') == 1 && !empty($caption_link)) {
                    if($link_target == '_window') {
                        $caption_target = "href='#' onClick=\"window.open('".$caption_link."','MyWindow','toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes,width=600,height=300'); return false;\"";
                    } else {
                        $caption_target = 'href="'.$caption_link.'" target="'.$link_target.'"';
                    }
                    $caption = '<a '.$caption_target.'>'.$caption.'</a>';
                }

                //Image
                $image_url  = esc_url($instance['image_uri-' . $i]);
                $size  = esc_attr($instance['size-' . $i]);
                $height = esc_attr($instance['height-' . $i]);
                $width = esc_attr($instance['width-' . $i]);
                $image_link = esc_url($instance['image_link-' . $i]);
                if($size != 'custom') {
                    $attachment_id = zwrew_get_attachment_id($image_url);
                    $image = wp_get_attachment_image_src($attachment_id, $size);
                    $image_url = $image[0];
                    $width = $image[1];
                }

                $image = '<img src="'.$image_url.'" alt="'.$instance['alternate_text-' . $i].'" height="'.$height.'" width="'.$width.'"/>';
                if(get_option('rew_image_link') == 1 && !empty($image_link)) {
                    if($link_target == '_window') {
                        $image_target = "href='#' onClick=\"window.open('".$image_link."','MyWindow','toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes,width=600,height=300'); return false;\"";
                    } else {
                        $image_target = 'href="'.$image_link.'" target="'.$link_target.'"';
                    }
                    $image = '<a '.$image_target.'><img src="'.$image_url.'" alt="'.$instance['alternate_text-' . $i].'" height="'.$height.'" width="'.$width.'"/></a>';
                }

                //Description
                $short_desc    = apply_filters('the_content', $instance['short_desc-' . $i]);
                if(strlen($short_desc) > 200) { $short_desc = substr($short_desc, 0, 200).'...';
                }

                $full_description = apply_filters('the_content', $instance['full_desc-' . $i]);
                if(strlen($full_description) > 200) {
                    $full_desc = substr($full_description, 0, 200).'...';
                } else {
                    $full_desc = $full_description;
                }

                $short_desc_link = $instance['short_desc_link-' . $i];
                $button_title = empty($instance['button_title-' . $i]) ? 'Read More' : $instance['button_title-' . $i];
                $short_desc_button="";
                if(get_option('rew_description') == 'short' && (!empty($short_desc_link) && $short_desc_link != "")) {
                    if($link_target == '_window') {
                        $button_target = "href='".$short_desc_link."' onClick=\"window.open('".$short_desc_link."','MyWindow','toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes,width=600,height=300'); return false;\"";
                    } else {
                        $button_target = 'href="'.$short_desc_link.'" target="'.$link_target.'"';
                    }
                    $short_desc_button = '<a '.$button_target.'><button name="external_link">'.$button_title.'</button></a>';
                }
                ?>
                <ul class="rew-entries" style="text-align:<?php echo $alignment; //phpcs:ignore ?>"> 
                <?php if(get_option('rew_caption') == 1 ) { ?>
                    <li class="rew-caption"><strong><?php echo $caption; //phpcs:ignore ?></strong></li>
                <?php } ?>
                <?php if(get_option('rew_image') == 1) { ?>
                    <li class="rew-image"><?php echo $image; //phpcs:ignore?></li>
                <?php } ?>
                <?php if(get_option('rew_description') == 'short') { ?>
                    <li class="rew-short-desc"><?php echo $short_desc; //phpcs:ignore?></li>
                    <li class="rew-shortdesc-btn"><?php echo $short_desc_button; //phpcs:ignore?></li>
                <?php } else { ?>
                    <li class="rew-full-desc half" id="full-desc-half<?php echo $i;?>"><?php echo $full_desc; //phpcs:ignore?></li>
                    <li class="rew-full-desc" style="display:none;" id="full-desc-full<?php echo $i;?>"><?php echo wp_kses_post(apply_filters('the_content', $instance['full_desc-' . $i])); ?></li>
                    <?php if(strlen($full_description) > 200) { ?>
                    <li>
                    <button name="full_desc_button" class="full_desc_button" onclick="getDescription(<?php echo esc_js($i);?>);"><?php echo $button_title; //phpcs:ignore?></button>
                    </li>
                    <?php }
                } ?>
                </ul>
                <?php
            }
        }
        echo $after_widget; //phpcs:ignore
    }
    //Function widget ends here

    /**
     * @see WP_Widget::update -- do not rename this
     */
    function update( $new_instance, $old_instance )
    {
        $instance = array();
        $max_entries = get_option('rew_max');
        $max_entries = (empty($max_entries)) ? '5' : $max_entries;
        $instance['title'] = wp_strip_all_tags($new_instance['title']);
        for($i=0; $i<$max_entries; $i++){
            $block = $new_instance['block-' . $i];
            if($block == 0 || $block == "") {
                $instance['block-' . $i] = $new_instance['block-' . $i];
                $instance['caption-' . $i] = wp_strip_all_tags($new_instance['caption-' . $i]);
                $instance['caption_link-' . $i] = wp_strip_all_tags($new_instance['caption_link-' . $i]);
                $instance['image_uri-' . $i]   = wp_strip_all_tags($new_instance['image_uri-' . $i]);
                $instance['alternate_text-' . $i]   = wp_strip_all_tags($new_instance['alternate_text-' . $i]);
                $instance['size-' . $i]       = wp_strip_all_tags($new_instance['size-' . $i]);
                $instance['width-' . $i]      = wp_strip_all_tags($new_instance['width-' . $i]);
                $instance['height-' . $i]     = wp_strip_all_tags($new_instance['height-' . $i]);
                $instance['image_link-' . $i] = wp_strip_all_tags($new_instance['image_link-' . $i]);
                $instance['short_desc-' . $i] = wp_strip_all_tags($new_instance['short_desc-' . $i]);
                $instance['short_desc_link-' . $i] = wp_strip_all_tags($new_instance['short_desc_link-' . $i]);
                $instance['full_desc-' . $i]     = wp_strip_all_tags($new_instance['full_desc-' . $i]);
                $instance['button_title-' . $i] = wp_strip_all_tags($new_instance['button_title-' . $i]);
            } else  {
                $count = $block - 1;
                $instance['block-' . $count] = $new_instance['block-' . $i];
                $instance['caption-' . $count] = wp_strip_all_tags($new_instance['caption-' . $i]);
                $instance['caption_link-' . $count] = wp_strip_all_tags($new_instance['caption_link-' . $i]);
                $instance['image_uri-' . $count]   = wp_strip_all_tags($new_instance['image_uri-' . $i]);
                $instance['alternate_text-' . $count]   = wp_strip_all_tags($new_instance['alternate_text-' . $i]);
                $instance['size-' . $count]       = wp_strip_all_tags($new_instance['size-' . $i]);
                $instance['width-' . $count]      = wp_strip_all_tags($new_instance['width-' . $i]);
                $instance['height-' . $count]     = wp_strip_all_tags($new_instance['height-' . $i]);
                $instance['image_link-' . $count] = wp_strip_all_tags($new_instance['image_link-' . $i]);
                $instance['short_desc-' . $count] = wp_strip_all_tags($new_instance['short_desc-' . $i]);
                $instance['short_desc_link-' . $count] = wp_strip_all_tags($new_instance['short_desc_link-' . $i]);
                $instance['full_desc-' . $count]     = wp_strip_all_tags($new_instance['full_desc-' . $i]);
                $instance['button_title-' . $count] = wp_strip_all_tags($new_instance['button_title-' . $i]);
            }
        }
        return $instance;
    }
    //Function update ends here

    /**
     * @see WP_Widget::form -- do not rename this
     */
    function form( $display_instance )
    {
        $max_entries = get_option('rew_max');
        $max_entries = (empty($max_entries)) ? '5' : $max_entries;
        $widget_add_id = $this->id . "-add";
        $title   = isset(($display_instance['title'])) ? esc_attr($display_instance['title']) : '' ;
        $rew_html = '<p>';
        $rew_html .= '<label for="'.$this->get_field_id('title').'"> '. __('Widget Title', ZWREW_TEXT_DOMAIN) .' :</label>';
        $rew_html .= '<input id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />';
        $rew_html .= '<div class="'.$widget_add_id.'-input-containers"><div id="entries">';
        for( $i =0; $i<$max_entries; $i++)  {
            if(isset($display_instance['block-' . $i]) || isset($display_instance['caption-' . $i]) || isset($display_instance['image_uri-' . $i]) ) {

                $display = (!isset($display_instance['block-' . $i]) || ($display_instance['block-' . $i] == "")) ? 'style="display:none;"' : '';

                $rew_html .= '<div id="entry'.($i+1).'" '.$display.' class="entrys"><span class="entry-title" onclick = "slider(this);"> '. __('Entry', ZWREW_TEXT_DOMAIN) .' </span>';
                $rew_html .= '<div class="entry-desc cf">';
                $rew_html .= '<input id="'.$this->get_field_id('block-' . $i).'" name="'.$this->get_field_name('block-' . $i).'" type="hidden" value="'.$display_instance['block-' . $i].'">';
                /**
                * Block Caption
                */
                if(get_option('rew_caption') == 1) {
                      $rew_html .= '<p>';
                      $rew_html .= '<label for="'.$this->get_field_id('caption-' . $i).'"> '. __('Caption', ZWREW_TEXT_DOMAIN) .' :</label>';
                      $rew_html .= '<input id="'.$this->get_field_id('caption-' . $i).'" name="'.$this->get_field_name('caption-' . $i).'" type="text" value="'.$display_instance['caption-' . $i].'">';
                      $rew_html .= '</p>';
                    if(get_option('rew_caption_link') == 1) {
                        $rew_html .= '<p>';
                        $rew_html .= '<label for="'.$this->get_field_id('caption_link-' . $i).'"> '. __('Link on Caption', ZWREW_TEXT_DOMAIN) .' :</label>';
                        $rew_html .= '<input id="'.$this->get_field_id('caption_link-' . $i).'" name="'.$this->get_field_name('caption_link-' . $i).'" type="text" value="'.$display_instance['caption_link-' . $i].'">';
                        $rew_html .= '</p>';
                    }
                }
                /**
                * Block Image
                */
                if(get_option('rew_image') == 1) {
                    $size       = esc_attr($display_instance['size-' . $i]);
                    $style = ($size == 'custom') ? 'style="display:block;"' : 'style="display:none;"';
                    $rew_html .= '<p>';
                    $rew_html .= '<label for="'.$this->get_field_id('image_uri-' . $i).'">'. __('Image', ZWREW_TEXT_DOMAIN) .' :</label>';
                    $show = (!empty($display_instance['image_uri-' . $i])) ? 'style="display:block;"' : '';
                    $rew_html .= '<input type="button name="removeimg" id="remove-img" class="button button-secondary" onclick="removeImage('.$i.');" '.$show.'>';
                    $rew_html .= '<img src="'.$display_instance['image_uri-' . $i].'" class="block-image" '.$show.'>';
                    $rew_html .= '<input type="hidden" class="img'.$i.'" style="width:auto;" name="'.$this->get_field_name('image_uri-' . $i).'" id="'.$this->get_field_id('image_uri-' . $i).'" value="'.$display_instance['image_uri-' . $i].'" />';
                    $rew_html .= '<input type="button" class="select-img'.$i.'" style="width:auto;" value="Select Image" onclick="selectImage('.$i.');"/>';
                    $rew_html .= '</p><p>';
                    $rew_html .= '<label for="'.$this->get_field_id('alternate_text-' . $i).'"> '. __('Alternate Text', ZWREW_TEXT_DOMAIN) .' :</label>';
                    $rew_html .= '<input type="text" name="'.$this->get_field_name('alternate_text-' . $i).'" id="'.$this->get_field_id('alternate_text-' . $i).'" value="'.$display_instance['alternate_text-' . $i].'" />';
                    $rew_html .= '</p><p>';
                    $rew_html .= '<label for="'.$this->get_field_id('size-' . $i).'"> '. __('Size', ZWREW_TEXT_DOMAIN) .' :</label>';
                    $rew_html .= '<select class="select-size'.$i.'" onChange="selectSize(this.value,'.$i.');" name="'.$this->get_field_name('size-' . $i).'" id="'.$this->get_field_id('size-' . $i).'" >';
                    $possible_sizes = apply_filters(
                        'image_size_names_choose', array(
                        'full'      => __('Full Size', ZWREW_TEXT_DOMAIN),
                        'thumbnail' => __('Thumbnail', ZWREW_TEXT_DOMAIN),
                        'medium'    => __('Medium', ZWREW_TEXT_DOMAIN),
                        'large'     => __('Large', ZWREW_TEXT_DOMAIN),
                        ) 
                    );
                    $possible_sizes['custom'] = __('Custom');
                    foreach( $possible_sizes as $size_key => $size_label ) {
                                  $rew_html .= '<option value="'.$size_key.'" '.selected($display_instance['size-' . $i], $size_key, false).'>'.$size_label.'</option>';
                    }
                    $rew_html .= '</select></p>';
                    $rew_html .= '<div id="custom_size'.$i.'" '.$style.'>';
                    $rew_html .= '<p><label for="'.$this->get_field_id('width-' . $i).'"> '. __('Width', ZWREW_TEXT_DOMAIN) .' :</label>';
                    $rew_html .= '<input id="'.$this->get_field_id('width-' . $i).'" name="'.$this->get_field_name('width-' . $i).'" type="text" value="'.$display_instance['width-' . $i].'" size="3" />';
                    $rew_html .= '</p><p>';
                    $rew_html .= '<label for="'.$this->get_field_id('height-' . $i).'"> '. __('Height', ZWREW_TEXT_DOMAIN) .' :</label>';
                    $rew_html .= '<input id="'.$this->get_field_id('height-' . $i).'" name="'.$this->get_field_name('height-' . $i).'" type="text" value="'.$display_instance['height-' . $i].'" size="3" />';
                    $rew_html .= '</p></div>';

                    if(get_option('rew_image_link') == 1) {
                         $rew_html .= '<p>';
                         $rew_html .= '<label for="'.$this->get_field_id('image_link-' . $i).'"> '. __('Link on Image', ZWREW_TEXT_DOMAIN) .' :</label>';
                         $rew_html .= '<input id="'.$this->get_field_id('image_link-' . $i).'" name="'.$this->get_field_name('image_link-' . $i).'" type="text" value="'.$display_instance['image_link-' . $i].'" />';
                         $rew_html .= '</p>';
                    }
                }
                /**
                * Description
                */
                $short_desc    = esc_attr($display_instance['short_desc-' . $i]);
                $short_desc_link    = esc_attr($display_instance['short_desc_link-' . $i]);
                $full_desc    = esc_attr($display_instance['full_desc-' . $i]);
                $button_title    = esc_attr($display_instance['button_title-' . $i]);

                if(get_option('rew_description') == 'short') {
                    $rew_html .= '<p class="last desc">';
                    $rew_html .= '<label for="'.$this->get_field_id('short_desc-' . $i).'"> '. __('Short Description', ZWREW_TEXT_DOMAIN) .' :</label>';
                    $rew_html .= '<textarea id="'.$this->get_field_id('short_desc-' . $i).'" name="'.$this->get_field_name('short_desc-' . $i).'">'.$short_desc.'</textarea>';
                    $rew_html .= '</p><p>';
                    $rew_html .= '<label for="'.$this->get_field_id('short_desc_link-' . $i).'"> '. __('External Link', ZWREW_TEXT_DOMAIN) .' :</label>';
                    $rew_html .= '<input class="widefat" id="'.$this->get_field_id('short_desc_link-' . $i).'" name="'.$this->get_field_name('short_desc_link-' . $i).'" type="text" value="'.$short_desc_link.'" />';
                    $rew_html .= '</p>';
                } else {
                    $rew_html .= '<p class="last desc">';
                    $rew_html .= '<label for="'.$this->get_field_id('full_desc-' . $i).'"> '. __('Full Description', ZWREW_TEXT_DOMAIN) .' :</label>';
                    $rew_html .= '<textarea id="'.$this->get_field_id('full_desc-' . $i).'" name="'.$this->get_field_name('full_desc-' . $i).'">'.$full_desc.'</textarea>';
                    $rew_html .= '</p>';
                }
                /**
                * Read More Button
                */
                $rew_html .= '<p class="last">';
                $rew_html .= '<label for="'.$this->get_field_id('button_title-' . $i).'"> '. __('Read More Button Title', ZWREW_TEXT_DOMAIN) .' :</label>';
                $rew_html .= '<input id="'.$this->get_field_id('button_title-' . $i).'" name="'.$this->get_field_name('button_title-' . $i).'" type="text" value="'.$button_title.'" />';
                $rew_html .= '</p>';
                $rew_html .= '<p><a href="#delete"><span class="delete-row">'. __('Delete Row', ZWREW_TEXT_DOMAIN) .'</span></a></p>';
                $rew_html .= '</div></div>';
            }
        }
        $rew_html .= '</div></div>';
        $rew_html .= '<div id="message">'. __('Sorry, you reached to the limit of', ZWREW_TEXT_DOMAIN) .' "'.$max_entries.'" '. __('maximum entries', ZWREW_TEXT_DOMAIN) .'.</div>'  ;
        $rew_html .= '<div class="'.$widget_add_id.'" style="display:none;">' . __('ADD ROW', ZWREW_TEXT_DOMAIN) . '</div>';
        ?>
        <script>
          jQuery(document).ready(function(e) {
            jQuery.each(jQuery(".<?php echo esc_attr($widget_add_id); ?>-input-containers #entries").children(), function(){
                if(jQuery(this).find('input').val() != ''){
                    jQuery(this).show();
                }
            });
            jQuery(".<?php echo esc_attr($widget_add_id); ?>" ).bind('click', function(e) {
                var rows = 0;
                jQuery.each(jQuery(".<?php echo esc_attr($widget_add_id); ?>-input-containers #entries").children(), function(){
                    if(jQuery(this).find('input').val() == ''){
                        jQuery(this).find(".entry-title").addClass("active");
                        jQuery(this).find(".entry-desc").slideDown();
                        jQuery(this).find('input').first().val('0');
                        jQuery(this).show();
                        return false;
                    }
                    else{
                      rows++;
                      jQuery(this).show();
                      jQuery(this).find(".entry-title").removeClass("active");
                      jQuery(this).find(".entry-desc").slideUp();
                    }
                });
                if(rows == '<?php echo $max_entries; //phpcs:ignore?>') 
                {
                    jQuery("#rew_container #message").show();
                }
            });
            jQuery(".delete-row" ).bind('click', function(e) {
                var count = 1;
                var current = jQuery(this).closest('.entrys').attr('id');
                jQuery.each(jQuery("#entries #"+current+" .entry-desc").children(), function(){
                    jQuery(this).val('');
                });
                jQuery.each(jQuery("#entries #"+current+" .entry-desc p").children(), function(){
                    jQuery(this).val('');
                });
                jQuery('#entries #'+current+" .entry-title").removeClass('active');
                jQuery('#entries #'+current+" .entry-desc").hide();
                jQuery('#entries #'+current).remove();
                jQuery.each(jQuery(".<?php echo esc_attr($widget_add_id); ?>-input-containers #entries").children(), function(){
                    if(jQuery(this).find('input').val() != ''){
                        jQuery(this).find('input').first().val(count);
                    }
                    count++;
                });
            });
        });
        </script>
        <style>
            .cf:before, .cf:after { content: ""; display: table; }
            .cf:after { clear: both; }
            .cf { zoom: 1; }
            .clear { clear: both; }
            .clearfix:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }
            .clearfix { display: inline-block; }
            * html .clearfix { height: 1%; }
            .clearfix { display: block;}

            #rew_container input,select,textarea{ float: right;width: 60%;}
            #rew_container label{width:40%;}
        <?php echo '.'.esc_attr($widget_add_id); ?>{
            background: #ccc none repeat scroll 0 0;font-weight: bold;margin: 20px 0px 9px;padding: 6px;text-align: center;display:block !important; cursor:pointer;
            }
            .block-image{width:50px; height:30px; float: right; display:none;}
            .desc{height:55px;}
            #entries #remove-img{background:url('<?php echo esc_url(ZWREW_URL);?>assets/images/deleteimg.png') center center no-repeat; width:20px; height:22px;display:none;}
            #entries{ padding:10px 0 0;}
            #entries .entrys{ padding:0; border:1px solid #e5e5e5; margin:10px 0 0; clear:both;}
            #entries .entrys:first-child{ margin:0;}
            #entries .delete-row{margin-top:20px;float:right;text-decoration: underline;color:red;}
            #entries .entry-title{ display:block; font-size:14px; line-height:18px; font-weight:600; background:#f1f1f1; padding:7px 5px; position:relative;}
            #entries .entry-title:after{ content: '\f140'; font: 400 20px/1 dashicons; position:absolute; right:10px; top:6px; color:#a0a5aa;}
            #entries .entry-title.active:after{ content: '\f142';}
            #entries .entry-desc{ display:none; padding:0 10px 10px; border-top:1px solid #e5e5e5;}
            #rew_container #entries p.last label{ white-space: pre-line; float:left; width:39%;}
            #message{padding:6px;display:none;color:red;font-weight:bold;}
        </style>
        <div id="rew_container">
        <?php echo $rew_html; //phpcs:ignore?> 
        </div>
        <?php
    } //Function form ends here
} //ZWREW_Plugin class ends here


/**
* Function to add widget script at admin side
*/
add_action('admin_enqueue_scripts', 'zwrew_admin_enqueue');
function zwrew_admin_enqueue()
{
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script(ZWREW_PREFIX . '-custom-photoswipe', ZWREW_URL . 'assets/js/repeater-entries-widget.js', array('jquery'), ZWREW_VERSION, true);
}


/**
* Function to add widget script at frontend
*/
add_action('wp_head', 'zwrew_front_enqueue');
function zwrew_front_enqueue()
{
    wp_enqueue_script(ZWREW_PREFIX . '-rew', ZWREW_URL . 'assets/js/repeater-entries-widget.js', array('jquery'), ZWREW_VERSION, true);
}


/**
 * Function to get attachment id based on url
 */
function zwrew_get_attachment_id( $attachment_url = '' )
{

    global $wpdb;
    $attachment_id = false;
    if ('' == $attachment_url ) {
        return;
    }

    $upload_dir_paths = wp_upload_dir();

    if (false !== strpos($attachment_url, $upload_dir_paths['baseurl']) ) {
        $attachment_url = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url);
        $attachment_url = str_replace($upload_dir_paths['baseurl'] . '/', '', $attachment_url);
        $attachment_id = $wpdb->get_var($wpdb->prepare("SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url)); //phpcs:ignore
    }
    return $attachment_id;
}

/**
* Remove Thumbnail Dimensions
*/
add_filter('post_thumbnail_html', 'zwrew_remove_thumbnail_dimensions', 10);
add_filter('image_send_to_editor', 'zwrew_remove_thumbnail_dimensions', 10);
function zwrew_remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

?>
