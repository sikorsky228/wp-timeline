<?php
/*
Plugin Name: Timeline
Plugin URI: /
Description: Timeline of custom meta boxes
Author: Sikorsky228
Version: 1.0
Author URI: https://github.com/sikorsky228
*/

function meta_styles()
{
    wp_enqueue_style( 'ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
}
add_action( 'admin_enqueue_scripts', 'meta_styles' );

function meta_scripts()
{
    wp_enqueue_script( 'jQuery', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js' );

}
add_action( 'admin_enqueue_scripts', 'meta_scripts' );

function dynamic_add_custom_box() {
    add_meta_box(
        'dynamic_sectionid',
        __( 'Timeline', 'myplugin_textdomain' ),
        'dynamic_inner_custom_box',
        'sprava');
}

/* Prints the box content */
function dynamic_inner_custom_box($post) {

    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'dynamicMeta_noncename' );
    ?>
    <div id="meta_inner">
    <?php

    //get the saved meta
    $stage = get_post_meta($post->ID,'stage',true);

    $c = 0;
    if ( count( $stage ) > 0 ) {
        foreach( $stage as $item ) {
            if ( isset( $item['date'] ) || isset( $item['info'] ) ) {
            ?>
                <p id="stageBlock"> Дата проведення <input type="data" class="datepicker" name='stage[<?php echo $c ?>][date]' value="<?php echo $item["date"] ?>" />
                <label>Статус:</label>
                <select name="stage[<?php echo $c ?>][status]">
                  <option <? echo selected($item["status"], 'sud'); ?> value="sud">Суд</option>
                  <option <? echo selected($item["status"], 'sprava'); ?> value="sprava">Справу вiдкрито</option>
                  <option <? echo selected($item["status"], 'end'); ?> value="end">Закрито</option>
                  </select>
                <label>Опис</label>
                <textarea cols="50" name="stage[<?php echo $c ?>][info]"><?php echo $item['info']; ?></textarea><span class="remove">Remove</span></p>
            <?
                $c = $c +1;

            }
        }
    }

    ?>

    <span id="here"></span>
    <span class="add"><?php _e('Add item'); ?></span>
    <script>
        var $ =jQuery.noConflict();
        $(document).ready(function() {

          $('body').on('focus',".datepicker", function(){
              $(this).datepicker();
          });
            var count = $('p#stageBlock').length;
            $(".add").on ('click', function() {
                count = count + 1;
                $('#here').append('<p id="stageBlock"> Дата проведення <input type="data" class="datepicker" name="stage['+count+'][date]" value="" />-- Статус : <select name="stage['+count+'][status]"><option value="sud">Суд</option><option value="sprava">Справу вiдкрито</option><option value="end">Закрито</option></select>-- Опис <textarea cols="50" name="stage['+count+'][info]"></textarea><span class="remove">Remove</span></p>');
                return false;
            });
            $(".remove").live('click', function() {
                $(this).parent().remove();
            });
        });
        </script>
    </div>
<?php

}

/* When the post is saved, saves our custom data */
function dynamic_save_postdata( $post_id ) {
    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if ( !isset( $_POST['dynamicMeta_noncename'] ) )
        return;

    if ( !wp_verify_nonce( $_POST['dynamicMeta_noncename'], plugin_basename( __FILE__ ) ) )
        return;

    // OK, we're authenticated: we need to find and save the data

    $stage = $_POST['stage'];

    update_post_meta($post_id,'stage',$stage);
}

add_action( 'add_meta_boxes', 'dynamic_add_custom_box' );
add_action( 'save_post', 'dynamic_save_postdata' );
