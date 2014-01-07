<?php 
class PageLines_Selector {
	
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		$this->root = trailingslashit( get_theme_root() );
		$this->url = plugins_url( '', __FILE__);
		add_action( 'wp_head', array( $this, 'head' ) );
		add_action( 'template_redirect', array( $this, 'draw_bar' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
	}
	
	function scripts() {
		wp_enqueue_script( 'jquery' );
		wp_register_style( 'pl_bar', plugins_url('assets/style.css', __FILE__) );
		wp_enqueue_style( 'pl_bar' );
	}
	function init() {
		$themes = wp_get_themes();
		$this->themes = array();
		foreach( $themes as $slug => $theme_data ) {
			
			$data = get_file_data( $this->root . $slug . '/style.css', array( 'PLURL' ) );
			if( '' != $data[0] ) {
				$this->themes[$slug]['url'] = $data[0];
				$data = get_file_data( $this->root . $slug . '/style.css', array( 'PLTYPE' ) );
				if( '' != $data[0] )
					$this->themes[$slug]['type'] = $data[0];

				$data = get_file_data( $this->root . $slug . '/style.css', array( 'PLMOB' ) );
				if( '' != $data[0] )
					$this->themes[$slug]['mobile'] = $data[0];

				$data = get_file_data( $this->root . $slug . '/style.css', array( 'Theme Name' ) );
				if( '' != $data[0] )
					$this->themes[$slug]['name'] = $data[0];
				$data = get_file_data( $this->root . $slug . '/style.css', array( 'PLDEMO' ) );
				if( '' != $data[0] )
					$this->themes[$slug]['demo'] = $data[0];
			}				
		}

	}
	
	function head() {
		if( ! $this->showbar() )
			return;
			
		echo '<link  href="http://fonts.googleapis.com/css?family=Kreon:300,400,700" rel="stylesheet" type="text/css" >';
		?>
		<script>
		
        var theme_list_open = false;

        jQuery(document).ready(function () {

        	function fixHeight () {

        	var headerHeight = jQuery("#switcher").height();

        	jQuery("#iframe").attr("height", ((jQuery(window).height() - 10) - headerHeight) + 'px');

        	}

        	jQuery(window).resize(function () {

        		fixHeight();

        	}).resize();

        	jQuery("#theme_select").click( function () {

        		if (theme_list_open == true) {

        		jQuery(".center ul li ul").hide();

        		theme_list_open = false;

        		} else {

        		jQuery(".center ul li ul").show();         		

        		theme_list_open = true;

        		}

        		return false;

        	});

			jQuery(".desktop-resize").click(function () {
				
				jQuery("#iframe").attr("width", "100%")
			})

			jQuery(".tablet-resize").click(function () {
				
				jQuery("#iframe").attr("width", "768px")
			})
			
			jQuery(".mobile-resize").click(function () {
				
				jQuery("#iframe").attr("width", "480px")
			})

        	jQuery("#theme_list ul li a").click(function () {


				var theme_data = jQuery(this).attr("rel").split(",");

	        	jQuery("li.purchase a").attr("href", theme_data[1]);
	        	jQuery("li.remove_frame a").attr("href", theme_data[0]);
	        	jQuery("#iframe").attr("src", theme_data[0]);
	        	jQuery(".center ul li ul").hide();
				jQuery('#theme_select').text(theme_data[2])
        	theme_list_open = false;

        	return false;

        	});

        });

        </script>

<?php
	}
	
	function draw_bar() {
		if( ! $this->showbar() )
			return;
			
		// get current slug..
		
		$slug = basename( get_template_directory() );
		$current_theme_purchase_url = $this->themes[$slug]['url'];
		$current_theme_url = $this->themes[$slug]['demo'];
		$current_theme_name = $this->themes[$slug]['name'];
		?>
		<div id="switcher">

			<div class="center">

			<ul>

			<li><img src="<?php echo $this->url . '/assets/logo.png';?>" alt="" /></li>

			<li id="theme_list"><a id="theme_select" href="#"><?php echo $current_theme_name; ?></a>

				<ul>

					<?php

					foreach ($this->themes as $i => $theme) :

					echo '<li><a href="#" rel="' . $theme['demo'] . ',' . $theme['url'] . ',' . $theme['name'] . '">' . ucfirst($theme['name']) . ' ' . $theme['type'] . '</a></li>';

					endforeach;

					?>

				</ul>

			</li>	
			
			<li class="desktop-resize"><a href="#">desktop</a></li>
			<li class="tablet-resize"><a href="#">tablet</a></li>
			<li class="mobile-resize"><a href="#">mobile</a></li>
			<li class="remove_frame"><a href=#>CLOSE</a></li>
			<li class="purchase" rel="<?php echo $current_theme_purchase_url; ?>"><a href="<?php echo $current_theme_purchase_url; ?>">Purchase</a></li>		
			
			</div>

			</ul>

		</div>
<div class="iframe-wrap">
<iframe id="iframe" src="<?php echo $current_theme_url; ?>" frameborder="0" width="100%"></iframe>
</div>
	<?php
		
	}
	
	function showbar() {
		if( isset( $_GET['dobar'] ) && ! is_user_logged_in() )
			return true;
	}
}

new PageLines_Selector;