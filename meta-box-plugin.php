<?php

    /*
    Plugin Name: metabox
    Description: test metabox plugin
    Version: 1.0
    Author:Dave
    Author URI: https://www.github.com/repzlaw
    */

    add_action( 'admin_menu', 'add_metabox' );

    function add_metabox() {

        add_meta_box(
            'metabox', // metabox ID
            ' Contributors', // title
            'metabox_callback', // callback function
            array('post'), // post type or post types in array
            'normal', // position (normal, side, advanced)
            'default' // priority (default, low, high, core)
        );

    }

    function metabox_callback( $post ) {
        $contributor_meta = get_post_meta( $post->ID, '_contributor_meta', true );
        $contributor_meta = $contributor_meta ? $contributor_meta : [];
        
        $args = array(
            'orderby' => 'user_nicename',
            'order'   => 'ASC',
        );
        $users = get_users( $args );

        foreach ($users as $key => $user) { ?>
            <div>
                <div>
                    <input type="checkbox" name="contributor_meta[]" 
                        value="<?php echo $user->ID ?>"
                        <?php echo (in_array($user->ID, $contributor_meta)) ? 'checked="checked"' : ''; ?>
                        /> 
                    <span>  <?php echo $user->user_nicename ?> </span>
                </div>
                <br/>

            </div>
      <?php  }
    }


    add_action('save_post', 'save_meta');

    function save_meta( ) {
        global $post;
        // Get our form field
        if(isset( $_POST['contributor_meta'] ))
        {
            $custom = $_POST['contributor_meta'];
            $old_meta = get_post_meta($post->ID, '_contributor_meta', true);
            // Update post meta
            if(!empty($old_meta)){
                update_post_meta($post->ID, '_contributor_meta', $custom);
            } else {
                add_post_meta($post->ID, '_contributor_meta', $custom, true);
            }
        }

    }

    //add contributors to the end of post
    add_filter('the_content','addContributorsToEndOfPost');

    function addContributorsToEndOfPost($content)
    {
        global $post;

        $contributor_meta = get_post_meta( $post->ID, '_contributor_meta', true );
        $contributor_meta = $contributor_meta ? $contributor_meta : [];
        if(is_single() && is_main_query() ){
            $r = 
            '<div style="margin-left:25px">
                <h5 style="color:red"> Contributors</h5>';

                foreach ($contributor_meta as $key => $value) {
                    $user = get_userdata($value);
                    $r .=  '<p class="text-success ml-4 ms-3">'.
                    '<span style="margin-right:8px">'. get_avatar( $value, $size = '15' ).'</span>'.
                    '<a style="text-decoration:none" href="/author/'.$user->user_nicename.'">'.$user->user_nicename .'</a>'.
                    '</p>';
                }
        }
        return $content .$r.'</div>';
    }




?>