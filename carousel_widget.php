<?php
/*
Plugin Name: Carousel widget
Plugin URI: 
Description: Roundabout carosel widget
Version: 1.1.0
Author: Ujwol Bastakoti
Author URI:
License: GPLv2
*/

wp_register_script( 'roundAboutShapeJquery', plugins_url('js/jquery.roundabout-shapes.min.js',__FILE__ ));
wp_register_script( 'roundAboutJquery', plugins_url('js/jquery.roundabout.js',__FILE__ ));
wp_register_script( 'easingJquery', plugins_url('js/jquery.easing.js',__FILE__ ));
wp_register_style( 'carouselWidgetCss', plugins_url('css/carouselwidget.css',__FILE__ )); //register css for plugin
wp_register_style( 'jqueryUiCss', plugins_url('css/jquery-ui.css',__FILE__ )); //register jquery ui css

class carousel_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'carousel_widget', // Base ID
			'Carousel Widget', // Name
			array( 'description' => __( 'Carousel Widget', 'text_domain' ), ) // Args
		);
	}
	
	public function display_carousel()
	{
		global $wpdb;
		
		wp_enqueue_script('roundAboutJquery');
		wp_enqueue_script('roundAboutShapeJquery');
		wp_enqueue_style('carouselWidgetCss');
		
	?>
	<script type="text/javascript" >
	 var $jq=jQuery.noConflict();
			$jq(document).ready(function() {
				$jq('ol.roundabout-holder').roundabout({
					shape: 'rollerCoaster',
					easing:"swing",
					duration:0,
			         autoplay: true,
			         autoplayDuration: 4000,
			         autoplayPauseOnHover: true,
			         minOpacity:0.9,
			         minScale:0.4
				
			        });
			});//end of the jquery animation

			
		
		</script>
	
	<?php 
	
		
		$table_name = $wpdb->prefix."url_table";
		
		$sql = "SELECT * FROM `".$table_name."` order by RAND() LIMIT 5;";
		
		$result = $wpdb->get_results($sql,ARRAY_A );
		
		echo"<ol class='roundabout-holder'>";
		foreach ($result as $row)
		{
			
			echo"<li style='background-image:url(".$row ['imageurl']."); background-repeat:no-repeat; background-size:100% 100%;' id='roundabout-moveable-item'>";
			//echo"<a target='_blank' href='".$row['url']."'><span>".$row['site']."</span></li></a>";
		}
		echo "</ol>";
		
		?>
		
<?php 		
		
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
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		//echo __( 'Carousel Widget!', 'text_domain'); 
			//echo "This section will contain contents of widget";	
			
		$carousel = $this->display_carousel();
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
		
		
		//$form =	carousel_widget_backend_form();
		//echo($form);
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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
       return $instance;
	}
	
}//end of class carousel_widget
// register carousel widget
add_action( 'widgets_init', create_function( '', 'register_widget( "carousel_widget" );' ) );

//function to create url table
function create_url_table()

{
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

register_activation_hook(__FILE__,'create_url_table');


/*runs when you remove plugin */
register_uninstall_hook(__FILE__,'delete_url_table');
function delete_url_table(){
    $table_name = $wpdb->prefix."url_table";
$sql = "DROP TABLE ".$tableName.";";
$wpdb->query($sql);
}


//function to delete row from table
if($_GET['action']== 'carousel_widget_update_table')
{ 

$action = carousel_widget_update_table($_GET['id']);
	
}

function carousel_widget_update_table($id)
{
	global $wpdb;

$table_name = $wpdb->prefix."url_table";
$sql = "DELETE FROM $table_name WHERE id= $id;";

$result = $wpdb->query($sql);
if($result==1)
{
   header("Location: " . $_SERVER["HTTP_REFERER"]);
}
else
{
	
}
}//end of function carousel_widget_update_table

//update database table with new portfolio item
if(($_POST['carousel_widget_update']==true)) 
{
function carousel_widget_update($post)
{
	global $wpdb;
	$table_name = $wpdb->prefix."url_table";
	if(empty($post['url'])||empty($post['imageurl'])||empty($post['site']))
	{
		?>
		<script type="text/javascript">
		alert("Please avoid  Illegal Activities!");
		
		</script>
	<?php		
	}
	else{
		
		$check = array('http','https');
		$http = explode("://",$post['url']);
		
		
	 if (in_array($http[0],$check)) {
	 	
	 	
	 	$url = $post['url'];
		
	}
	else
	{
		$url = "http://".$post['url'];
		echo($url);
		
	}
	
	//var_dump($post);
	
	$wpdb->insert($table_name,
	       
			array('imageurl'=>$post['imageurl'],'url'=>$url,  'site'=>$post[ 'site']),
	
			array('%s','%s'));
	}
  }
}





//when the form is updated
if ( $_POST['carousel_widget_update'] == 'true' ) {
	carousel_widget_update($_POST);
}


//load data with ajax 
add_action('admin_print_scripts', 'ui_ajax_tab_javascript');

function ui_ajax_tab_javascript() {


?>
<script type="text/javascript" src ='http://code.jquery.com/jquery-1.9.1.min.js'></script>
<script type="text/javascript" >
jQuery(document).ready(function($){
jQuery('a.ajax_true').click(function(){

		//var value = $(this).text();
		var aj = $(this).attr('ajax');
        
		//alert(aj);
		 var data = {
				 action : aj
	             //admindata: value
				};
		 $(this).removeAttr('ajax');
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl,data , function(response) {
				var divclass = '.'+aj;
				
				$(divclass).append(response);
			});
});
})
</script>
<?php
}





function carousel_widget_settings()
		{
			wp_enqueue_script('jquery');
			wp_enqueue_style('carouselWidgetCss');
			add_thickbox();//call built in function for thickbox
		?>
<script type="text/javascript">			
jQuery(document).ready(function(){

  jQuery(".update_form ").keyup(function(){	
		
	var inputVal = new Array();
	var i =0;
	
	jQuery(".update_form input[type=text]").each(function() {
        inputVal[i] = jQuery.trim(jQuery(this).val());
        i++;
           });
	var emptyField = jQuery.inArray("",inputVal);

	
	  if(emptyField === -1)
	   {
		  
		   jQuery("#update_button").removeAttr("disabled");
		   jQuery(".form_enable").remove(); 
	   }

    });

})
</script>
<h3 class="ui_tab_h3">Add Item to Carousel</h3> 
		<div class="form_div" >
        
  	
        <form class="update_form" method="POST" action="" > 
            <input type="hidden" name="carousel_widget_update" value="true" />  
            <label class="howto"><span> *Image URL:</span> <input type="text" size="40"  name="imageurl"/></label><span class="media_library"><a href="upload.php?TB_iframe=true" class="thickbox">Media</a></span><br/>
			<label class="howto"><span>*Site URL &nbsp;&nbsp;: </span> <input type="text"  size="40" name="url"/></label><br/>
			<label class="howto"><span>*Site Name: </span><input type="text"  size ="30" name="site"/></label><br/>
            <label class="howto_button"><button disabled id="update_button" type="submit"  name="submit" value="Update" class="button">Update</button></label>
        </form>  
        <p class="form_enable">Button will enable once all fields are filled</p> 
         <p class="emptyAlert"  title="Alert!" ><br/>&nbsp;&nbsp;&nbsp;&nbsp;* Fields cannot be empty.</p>
<div>
    <h3>Notes: </h3>
     
     <ol>
       <li > Use high resolution images for better result.
        <font style='color:black;font-size:x-small;'><br/>Media->Edit copy 'File Url' OR use external image URL</font></li>
      <li> Do not use filename with too many characters.</li>
      <li> To display carousel on page use shortcode [inpage_carousel].</li>
      <li> To display carousel widget on page use shortcode [inpage_carousel_widget].</li>
      <li> To Rate Review and Report Bug <a href="http://wordpress.org/support/view/plugin-reviews/carousel-widget?TB_iframe=true" class='thickbox' >Click Here</a> </li>
      <li> If updating please also update file url to full image url "http://....../abc.jpg"  </li>
      <li>Enjoy! More cool stuffs to follow.</li>
      
  </ol>
  </div>
</div>
       
    
    
    <?php 
   
}




//function to display setting options and  table of contents most probably demo 
 function carousel_widget_admin_page()
 {
 	
 	wp_enqueue_script('roundAboutJquery');
 	wp_enqueue_script('roundAboutShapeJquery');
 	//wp_enqueue_style('carouselWidgetCss');
 	//wp_enqueue_script('jquery');
 	wp_enqueue_script('jquery-ui-tabs');//jquery ui tabs
 	wp_enqueue_style('jqueryUiCss');//jquery ui css
 	?>
 	
<script type="text/javascript">
jQuery(document).ready(function() {
	 jQuery( "#tabs" ).tabs();	
})
 
 </script>	
 	
 	<div class ="wrap">
 	<div id="icon-plugins" class="icon32"></div>
 	<h2>Carousel Widget</h2>
 	<br/>
 	<div id="tabs" style="width:96%;">
 	
 	
 	<ul>
 	 <li ><a id="tab_1" href="#tab-1" >Form</a></li>
 	<li > <a id="tab_2" class="ajax_true" href="#tab-2"  ajax="display_url_table">Media List</a></li>
 	<li  ><a id="tab_3" class="ajax_true" href="#tab-3" ajax="inpage_carousel">Demo-Carousel</a></li>
 	<li  ><a id="tab_4" class="ajax_true" href="#tab-4" ajax="carousel_widget">Demo-Widget</a></li>
 	</ul>
 	
 	    <div id="tab-1" class="ui_tab" >
 	 
 	    <?php carousel_widget_settings();?>
 	   
 	  </div>
 	  <div id="tab-2" class="ui_tab display_url_table">
 	   <h3 class="demo_div_h3">List of the items on Carousel </h3>
 	  </div>
 	  <div id="tab-3"  class="ui_tab inpage_carousel" >
 	     <h3 class="demo_div_h3">Circular Carousel Demo</h3>
 	  
 	  </div> 
 	  <div id="tab-4"  class="ui_tab carousel_widget" >
 	       <h3 class="demo_div_h3">Carousel Widget Demo</h3>
 	  
 	  </div> 
 </div>
</div> 	 	
<?php 
 	
 	
 	
 }
 
		
 
 
 /* Ajax actions on the setting section */
 
 //ajax call to display widget
 add_action('wp_ajax_carousel_widget', 'carousel_widget_callback');
 
 //widget  call back function for ajax requeet
 function carousel_widget_callback(){
 	//call the class caousel_widget
 	$obj = new carousel_widget();
 	
 	
 	echo '<div class="demo_widget">';
    $obj->display_carousel();
    echo '</div>';
    die();
  }
  
  //function to create carousel widget on page
  function carousel_widget_shortcode()
  {
  	//call the class caousel_widget
  	$obj = new carousel_widget();
  	
  	
  	echo '<div class="demo_widget">';
  	$obj->display_carousel();
  	echo '</div>';
  }
    add_shortcode('inpage_carousel_widget','carousel_widget_shortcode');
 
 
 //function for in page carousel
 function in_page_carousel()
 {
 
 	global $wpdb;
 	wp_enqueue_script('jquery');
 	wp_enqueue_script('easingJquery');
 	wp_enqueue_script('roundAboutJquery');
 	wp_enqueue_script('roundAboutShapeJquery');
 	wp_enqueue_style('carouselWidgetCss');
 
 	?>
 					<script type="text/javascript" >
 					 var $jq=jQuery.noConflict();
 							$jq(document).ready(function() {
 								$jq('ul.roundabout-holder-page').roundabout({
 									easing:"swing",
 									duration:0
 									
 									});
 							});//end of the jquery animation
 				
 						
 						</script>
 					
 					<?php 
 					
 						
 						$table_name = $wpdb->prefix."url_table";
 						
 						$sql = "SELECT * FROM `".$table_name."` ;";
 						
 						$result = $wpdb->get_results($sql,ARRAY_A );
 						echo '<div>';
 						echo"<ul class='roundabout-holder-page'>";
 						foreach ($result as $row)
 						{
 							
 	    					echo"<li style='background-image:url(".$row ['imageurl']."); background-repeat:no-repeat; background-size:100% 100%;' id='roundabout-moveable-item'>";
 							echo"<a target='_blank' href='".$row['url']."'><span>".$row['site']."</span></li></a>";
 						}
 						echo "</ul></div>";
 						
 						
 				
 			}
 			add_shortcode( 'inpage_carousel', 'in_page_carousel' );
 
 
 
 	//ajax call back function for inpage carousel
 	add_action('wp_ajax_inpage_carousel', 'inpage_carousel_function');
 
 	function inpage_carousel_function()
 	{
 		$circular_carousel = in_page_carousel();
 		die();
 	}
 
 
 
 
 //ajax call back function for table of items 
 add_action('wp_ajax_display_url_table', 'display_url_table_callback');
 
 function display_url_table_callback()
 {
 	wp_enqueue_script('jquery');
 
 
 	global $wpdb;
 	add_thickbox();//call built in function for thickbox
 
 
 	$table_name = $wpdb->prefix."url_table";
 	$sql = "SELECT * FROM `".$table_name."`;";
 	$result = $wpdb->get_results($sql,ARRAY_A );
 	echo "<div class='table_div'>";
 	
 	echo"<table class='carousel_widget_table wp-list-table widefat fixed media' align='center'><tr style='font-weight:bolder;' align='center'><th>No</th><th>Image</th><th>URL</th><th>Site Name</th><th>Delete</th></tr>";
 	$a = 1;
 	foreach ($result as $row)
 	{
 
 
 		echo "<tr><td  class='rowId'>".$a."</td><td style='text-decoration:underline;'><a  href='".$row['imageurl']."TB_iframe=true&height=auto&width=auto' title='".$row['site']."' class='thickbox'> <img src='".$row['imageurl']."'  width='60' height='60' class='attachment-80x60'></a></td><td style='text-decoration:underline;'><a href='".$row['url']."?TB_iframe=true&width=600&height=550' class='thickbox'>URL</a></td><td>".$row['site']."</td>";
 		echo "<td class='row_delete'><a href='?action=carousel_widget_update_table&id=".$row['id']."' onclick='return showNotice.warn();' >X<a></td></tr>";
 
 		$a++;
 	}
 	echo"</table></div>";
 	die();
 }
 
		
		
//add option on admin panel settings
		
		if ( is_admin() ){
		
		   
			/* Call the html code */
			
			add_action('admin_menu', 'carousel_widget_admin_menu');
			
			
		}
		
	// function to add menu setting page 
		function carousel_widget_admin_menu() {
		    
		    add_menu_page('Carousel widget', 'Carousel widget', 'administrator','carousel_widget', 'carousel_widget_admin_page');
		    
		}
		
		

	
?>