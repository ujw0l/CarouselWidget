<?php
/*
Plugin Name: Carousel widget
Plugin URI: https://github.com/ujw0l/CarouselWidget
Description: Carousel widget widget and Image carousel for page
Version: 2.5.0
Author: Ujwol Bastakoti
Author URI: http://ujw0l.github.io/
text-domain : carousel-widget
License: GPLv2
*/


class carouselWidgetPlugin{

	public function __construct(){

	register_activation_hook(__FILE__,array($this,'create_url_table') );
	register_deactivation_hook(__FILE__,array($this, 'delete_url_table') );
	self::add_required_actions();
	add_action( 'wp_enqueue_scripts', array($this, 'frontend_enqueue_script') );
	
	}


public function frontend_enqueue_script(){
wp_enqueue_script('frontend_carousel_library_js', plugins_url('js/carousel.js',__FILE__ ),array(),'',true);
wp_enqueue_script('frontend_image_carousel_js', plugins_url('js/image-carousel.js',__FILE__ ),array(),'',true);
wp_add_inline_script( 'frontend_image_carousel_js', "window.addEventListener('load',()=>{if(null != document.querySelector('#inpage_carousel')){  new imageCarousel('#inpage_carousel',{callBack:el=>el.style.opacity = '1'}); } })" );

}	

public function carousel_enqueue_script(){

wp_enqueue_media(); 
wp_enqueue_script('ctc_overlay_viewer_js', plugins_url('js/ctc_overlay.js',__FILE__ ),array(),'',false);
wp_enqueue_script('backend_image_carousel_js', plugins_url('js/image-carousel.js',__FILE__ ),array(),'',true);
wp_enqueue_script('carousel_script', plugins_url('js/carousel_widget.js',__FILE__ ),array(),'',true);
wp_enqueue_style( 'ctc_overlay_viewer_css', plugins_url('css/ctc_overlay_style.css',__FILE__ ),array(),'',false);
wp_add_inline_script( 'backend_image_carousel_js', "window.addEventListener('load',()=>{if(null != document.querySelector('#inpage_carousel')){  new imageCarousel('#inpage_carousel',{callBack:el=>el.style.opacity = '1'}); } })" );


 }

 public function add_required_actions(){
	add_action('admin_menu', array($this, 'carousel_widget_admin_menu') );
	add_action( 'admin_enqueue_scripts',array($this,'carousel_enqueue_script') );
	add_action( 'wp_ajax_add_carousel_item', array($this , 'add_carousel_item') );
	add_action( 'wp_ajax_delete_carousel_item', array($this , 'delete_carousel_item') );
	add_shortcode( 'inpage_carousel', array($this, 'in_page_carousel' ) );
 }
 

public function create_url_table(){
    global $wpdb;
    $table_name = $wpdb->prefix."url_table";
   $sql = "CREATE TABLE `". $table_name."`(
	`id` mediumint(9) NOT NULL AUTO_INCREMENT,
	`imageurl` varchar(250) NOT NULL,
	`url` varchar(250) NOT NULL,
	`site` varchar(100),
	PRIMARY KEY (`id`),
    UNIQUE KEY `url` (`url`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

   $wpdb->query($sql);

}

public function delete_url_table(){
 global $wpdb;
$table_name = $wpdb->prefix."url_table";
$sql = "DROP TABLE ".$tableName.";";
$wpdb->query($sql);
}

		
// function to add menu setting page 
public function carousel_widget_admin_menu() {

					if ( is_admin() ):
						add_menu_page(
										'Carousel widget', 
										'Carousel widget', 
										'administrator',
										'carousel_widget', 
										array($this,'carousel_widget_admin_page'),
										'dashicons-slides',
										'50'
									);
					endif;
		    
}

//
public function carousel_widget_admin_page(){
        $activeTab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'carousel_settings_tab';
       
        ?>
                                     <h1 class="dashicons-before dashicons-format-gallery" >Carousel Settings</h1>   
                                        <h2 class="nav-tab-wrapper ctcMainNavTab">
                                        <a href="?page=carousel_widget&tab=carousel_settings_tab" class="dashicons-before dashicons-admin-generic nav-tab <?php echo $activeTab == 'carousel_settings_tab' ? 'nav-tab-active' : ''; ?> "> Settings / Info </a>
										<a  href="?page=carousel_widget&tab=carousel_images_list" class="dashicons-before dashicons-menu-alt nav-tab <?php echo $activeTab == 'carousel_images_list' ? 'nav-tab-active' : ''; ?>"> Item List</a>
										  <a  href="?page=carousel_widget&tab=carousel_demo_admin" class="dashicons-before dashicons-images-alt  nav-tab <?php echo $activeTab == 'carousel_demo_admin' ? 'nav-tab-active' : ''; ?>"> Demo</a>
                                      </h2>
                                                            
        <?php   
    
	switch ( $activeTab  ):

		case 'carousel_settings_tab' :
		self::carouselSettingTabContent('add_slide');
		break;
		case 'carousel_images_list':
			self::carouselImageList();
		break;
		case 'carousel_demo_admin':
		   self::admin_carousel_demo();
		break;
	endswitch;	
 
}


public function admin_carousel_demo(){
echo '<fieldset style="border:1px dotted rgba(0,0,0,1);width:45%;float:left;display:inline-block;margin-top:20px;padding:20px;"> <legend>Carousel Demo</legend>';
$this->in_page_carousel(array('height'=>'390','width'=>'1000'));
echo "</fieldset>";
}

public function carouselSettingTabContent(){

 ?>
<fieldset style="border:1px dotted rgba(0,0,0,1);width:45%;float:left;display:inline-block;margin-top:20px;">
 <legend align="center" class="dashicons-before dashicons-plus">Add Item to Carousel</legend> 
		<div class="form_div" style="padding:5%;" >
        <form id= "carousel_add_image" class="update_form" method="POST" action=" " >  
           <div> <label for="image_url" >*Image URL: </label> 
		 	  <input id="image_url" style="margin-left:18px;" required type="url" size="40"  name="imageurl" value="" >
 			 <span id="carousel_widget_media"  id="carousel_widget_media" style="cursor:pointer;" onmouseenter = "this.style.color= 'rgba(242, 120,75, 1)'" onmouseleave = "this.style.color= ''" class="dashicons-before dashicons-admin-media" href="javaScript:void(0);"><span>
		</div>
			<div><label for="site_url">*Site URL &nbsp;&nbsp;: </label> 
			<input id="site_url" style="margin-left:22px;" required type="url"  size="40" name="url" value=""></div>
			<div><label for="site_name">*Site Name: </label> 
			<input id="site_name" style="margin-left:20px;" type="text" required  size ="30" name="site" value="" ></div>
           <div><?=submit_button('Add Image','primary','add_carousel_image')?></div>
        </form>  
</div>
</fieldset >


<fieldset  style="border:1px dotted rgba(0,0,0,1);width:40%;float:left;display:inline-block;margin-left:100px;margin-top:20px;">

 <legend class="dashicons-before dashicons-editor-ul" align="center">Notes: </legend>
     <ol>
       <li > Use high resolution images for better result.
        	<i><br/>Media->Select Image OR use external image URL</i>
	  </li>
      <li> Do not use filename with too many characters.</li>
      <li> Duplicate entry will be rejecte by the plugin.</li>
      <li> To display carousel on page use shortcode [inpage_carousel].</li>
     <li>  To add custom height and/or width  [inpage_carousel heigth="custom height inpx" width="custom width in px"] </li>
	 <li>  To set carousel on autoplay use  [inpage_carousel auto_play="true" ] </li>
	 <li>To set height , width and autoplay of wodget, to settings of carousel widget.</li>
      <li>Enjoy! More cool stuffs to follow.</li>
  </ol>

</fieldset>


 <?php

}


public function carouselImageList(){
 
 global $wpdb;
 	$table_name = $wpdb->prefix."url_table";
 	$sql = "SELECT * FROM `".$table_name."`;";
	$result = $wpdb->get_results($sql,ARRAY_A );
	 ?>
 	<div class='table_div'>
 	
<table class='carousel_widget_table wp-list-table widefat fixed media striped' style="text-align:center;">
<tr style='font-weight:bolder;' >
		<td>No</td><td>Image</td><td>URL</td><td>Site Name</td><td>Delete</td>
</tr>
<?php
 	$a = 1;
 	foreach ($result as $row):
 
 ?>
 		<tr id="carousel_item_row_<?=$row['id']?>" >
		 <td style='width:5%;' class='rowId'><?=$a?></td>
		 <td style='width:40%;' class="overlay-image" >
		 		<img style="cursor:pointer;width:60px;height:60px;" src='<?=$row['imageurl']?>' title='<?=$row['site']?>' >
		 </td>
		 <td style='width:10%;'><a href='<?=$row['url']?>' target="_blank" >URL</a></td>
		 <td style='width:30%;'><?=$row['site']?></td>
 		<td  style='width:15%;'>
		 <span style="cursor:pointer;color:rgba(0,0,0,1);" onmouseleave= 'this.style.color="rgba(0,0,0,1)"' onmouseenter='this.style.color="rgba(242, 38, 19, 1)"' data-item-id="<?=$row['id']?>" class="dashicons-before dashicons-no-alt carousel_delete_item"><span></td>
		 </tr>
		 <?php
 		$a++;
	 endforeach;
 	echo"</table></div>";
 }
 

 public function add_carousel_item(){
		if(empty($_POST["imageurl"]) || empty($_POST['url']) || empty($_POST['site']) ):
		echo __('Please fill in the all required field','carousel-widget');

		else: 
			global $wpdb;
				$insert = $wpdb->insert( 
					$wpdb->prefix."url_table", 
					array('imageurl'=> $_POST["imageurl"],"url"=>$_POST['url'],'site'=>$_POST['site'])
				);

				if( $wpdb->insert_id > 0 ):
					echo json_encode( array('true', __('Item Sucessfully added.','carousel-widget') ) );
				else:
					echo json_encode( array('false',__("Item couldn't be added.",'carousel-widget') ) );
				endif;	
		endif;	
		wp_die();
 }

public function delete_carousel_item(){
		global $wpdb;

		// Using where formatting.
		$row_count = $wpdb->delete( $wpdb->prefix."url_table", array( 'id' => $_POST['rowId'] ), array( '%d' ) );
		if($row_count >0 ):
			echo json_encode(array('true', __('Item sucessfully deleted.','crousel-widget' ),$_POST['rowId'] ));
		else: 
			echo json_encode(array('false', __("Item couldn't be deleted.",'crousel-widget' ) ));
		endif;	
		wp_die();
 }


public function in_page_carousel($atts){
$base = '';	
if(is_admin() ):
	$screen = get_current_screen();
	$base =  $screen->parent_base;
endif;

if( $base == 'carousel_widget' || !is_admin() ) :
	global $wpdb; 
	$height =  !empty($atts['height']) ? $atts['height'] : '600';
	$width = !empty( $atts['width'] ) ? $atts['width'] : '1200';
	$auto_play =  'true'== $atts['auto_play'] ? $atts['auto_play'] : 'false';
    $table_name = $wpdb->prefix."url_table";
    $sql = "SELECT * FROM `".$table_name."` ;";
	 $result = $wpdb->get_results($sql,ARRAY_A );
 	echo "<div id ='inpage_carousel' data-auto-play='{$auto_play}'  class='inpage_carousel' style='opacity:0; height:{$height}px;width:{$width}px; '>"; 
	foreach ($result as $row): 
 	    		echo "<img src='{$row ['imageurl']}' title='{$row['site']}' data-site-url='{$row['url']}'  />"; 			
	endforeach;
   echo "</div>";
endif;
 	
 }

}


//Carousel Widget part

class carousel_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'carousel_widget', // Base ID
			'Carousel Widget', // Name
			array( 'description' => __( 'Carousel Widget', 'text_domain' ), ) // Args
			
		);

	
	}
	

/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
	global $wpdb;	
	wp_add_inline_script( 'frontend_carousel_library_js', "if(null != document.querySelector('#carousel_widget')){  if(document.querySelector('#carousel_widget').getAttribute('data-auto-play') == 'true'){ var autoPlayThis = true; }else{var autoPlayThis = false;} ; new ctcCarousel('#carousel_widget', {autoPlay: autoPlayThis,autoPlayInterval: 3000,autoPlaySelector:'#carousel_widget'});}" );

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$height = !empty($instance['carousel_height']) ? esc_attr($instance['carousel_height']) : '400';
		$width = !empty($instance['carousel_width']) ? esc_attr($instance['carousel_width']) : '400';
		$auto_play = '1' === $instance['carousel_auto_play'] ? 'true' : 'false';

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;

		$table_name = $wpdb->prefix."url_table";
		$sql = "SELECT * FROM `".$table_name."`;";
		$result = $wpdb->get_results($sql,ARRAY_A );
		echo "<div id ='carousel_widget' data-auto-play='{$auto_play}'  class='inpage_carousel' style='opacity:0; height:{$height}px;width:{$width}px; '>"; 
			foreach ($result as $row): 
 	    		 echo "<img src='{$row ['imageurl']}' title='{$row['site']}' data-site-url='{$row['url']}'  />"; 			
			endforeach;
   echo "</div>";
	
		echo $after_widget;


	}
//contents for backend widget

	
/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	
	
	public function form( $instance ) {
		
	  if ( isset( $instance[ 'title' ] ) ) {
			
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Carousel', 'text_domain' );
		}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
				<label for="<?php echo $this->get_field_id( 'carousel_height' ); ?>"><?php _e( 'Height:' ); ?> <i> px</i></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'carousel_height' ); ?>" name="<?php echo $this->get_field_name( 'carousel_height' ); ?>" type="number" value="<?php echo esc_attr( $instance['carousel_height' ] ); ?>" />
		    </p>
		   <p>
				<label for="<?php echo $this->get_field_id( 'carousel_width' ); ?>"><?php _e( 'Width:' ); ?> <i> px</i></label> 
				
				<input class="widefat" id="<?php echo $this->get_field_id( 'carousel_width' ); ?>" name="<?php echo $this->get_field_name( 'carousel_width' ); ?>" type="number" value="<?php echo esc_attr( $instance['carousel_width' ] ); ?>" />
		    </p>
		    <p>
				<?php $auto_play =  '1' == $instance['carousel_auto_play' ] ? 'checked':'' ; ?>
				<label for="<?php echo $this->get_field_id( 'carousel_auto_play' ); ?>"><?php _e( 'Autoplay slides :' ); ?></label>
				<input <?= $auto_play?> class="widefat" id="<?php echo $this->get_field_id( 'carousel_auto_play' ); ?>" name="<?php echo $this->get_field_name( 'carousel_auto_play' ); ?>" type="checkbox"  value="1" />
		    </p>

		
		<p>
		
		<?php 
	}

/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['carousel_height'] = strip_tags( $new_instance['carousel_height'] );
		$instance['carousel_width'] = strip_tags( $new_instance['carousel_width'] );
		$instance['carousel_auto_play'] = strip_tags( $new_instance['carousel_auto_play'] );
       return $instance;
	}
	
}//end of class carousel_widget

/*function resgiter widget as plguin*/
function register_carousel_widget(){
    register_widget( "carousel_widget" );
}

add_action( 'widgets_init', 'register_carousel_widget' );


new carouselWidgetPlugin();













		
		

		
		

	