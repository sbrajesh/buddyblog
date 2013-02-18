<?php
class BuddyBlogAdminHelper{
    
    
    private function __construct() {
        
        add_action('admin_menu',array($this,'add_menu'));
        add_action('admin_init',array($this,'settings_init'));
    }
    
    /**
     * Add menu to options tab
     */
    function add_menu(){
        add_options_page(__('BuddyBlog Settings','buddyblog'), __('BuddyBlog Settings','buddyblog'), 'manage_options', 'buddyblog', array($this,'render_admin'));
        
    }
    
    function settings_init(){
        
        register_setting('buddyblog_options', 'buddyblog_options');
        //add sections
        add_settings_section('general', __('General Options'), array($this,'render_section_general') , 'buddyblog');
        //add_settings_field('buddyblog_cat_option', __('Allow User to select Categories'), array($this,'render_setting_category')), 'buddyblog', 'general');
        
    }
    
    /**Render admin view*/
    
   function render_menu() {  

    $settings_output = buddyblog_get_settings();  
    ?>  
    <div class="wrap">  
        <div class="icon32" id="icon-options-general"></div>  
        <h2><?php _e('BuddyBlog Settings'); ?></h2>  
        <?php if ( isset( $_GET['settings-updated'] ) ) {
               echo "<div class='updated'><p>".__('Settings updated successfully.','buddyblog')."</p></div>";
          } ?>  
        
        <form action="options.php" method="post">
            <?php settings_fields( 'buddyblog_options' ); ?>
            <?php do_settings_sections('buddyblog' ); ?>
            <p class="submit">  
                <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes','buddyblog'); ?>" />  
            </p>  
              
        </form>  
    </div><!-- wrap -->  
<?php }  
    


    //register settings fields
    
}


function buddyblog_get_settings(){
    
    $options=array(
        'post_type'=>'post',
        'taxonomies'=>array('category'),
        'allow_upload'=>false,
        'max_upload_count'=>2
        );
    
    return $options;
}
?>