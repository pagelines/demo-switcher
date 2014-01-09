<?php
/*
Plugin Name: PageLines Demo Switcher Tool
Description: Must be network activated OR mu-plugin
Version: 0.4.91
Author: PageLines
PageLines: true
*/
class PageLines_Selector {
	
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		$this->root = trailingslashit( get_theme_root() );
		$this->url = plugins_url( '', __FILE__);
		add_action( 'wp_head', array( $this, 'head' ) );
		add_action( 'template_redirect', array( $this, 'draw_bar' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_shortcode( 'pl_demo_switcher', array( $this, 'shortcode' ) );
	}
	
	function scripts() {
		
		wp_register_style( 'pl_tabs', plugins_url('assets/tabs.css', __FILE__) );
		wp_register_style( 'pl_bar', plugins_url('assets/style.css', __FILE__) );
		wp_register_script( 'pl-track', plugins_url('assets/tracker.js', __FILE__), array( 'jquery' ) );
		
		wp_enqueue_style( 'pl_tabs' );

		if( ! $this->showbar() )
			return;

		wp_enqueue_style( 'pl_bar' );
		wp_enqueue_script( 'pl-track' );
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

        	jQuery("#theme-list").click( function () {

        		if (theme_list_open == true) {

        		jQuery("#switcher ul li ul").hide();

        		theme_list_open = false;

        		} else {

        		jQuery("#switcher ul li ul").show();         		

        		theme_list_open = true;

        		}

        		return false;

        	});

			jQuery(document).click(function() {
			    jQuery("#switcher ul li ul").hide()
			});

			jQuery(".desktop").click(function () {
				
				jQuery("#iframe").attr("width", "100%")
				jQuery("#iframe").removeClass("wrap-iphone");
				jQuery("#iframe").removeClass("wrap-tablet");
				jQuery("#iframe").removeClass("wrap-desktop");
				jQuery("#iframe").addClass("wrap-desktop");				
			})

			jQuery(".ipad").click(function () {
				
				jQuery("#iframe").attr("width", "768px")
				jQuery("#iframe").removeClass("wrap-iphone");
				jQuery("#iframe").removeClass("wrap-tablet");
				jQuery("#iframe").removeClass("wrap-desktop");
				jQuery("#iframe").addClass("wrap-tablet");
			})
			
			jQuery(".iphone").click(function () {
				
				jQuery("#iframe").attr("width", "480px")
				jQuery("#iframe").removeClass("wrap-iphone");
				jQuery("#iframe").removeClass("wrap-tablet");
				jQuery("#iframe").removeClass("wrap-desktop");
				jQuery("#iframe").addClass("wrap-iphone");
			})

        	jQuery("#theme-list ul li a").click(function () {


				var theme_data = jQuery(this).attr("rel").split(",");

	        	jQuery("a.purchase").attr("href", theme_data[1]);
	        	jQuery("a.remove_frame").attr("href", theme_data[0]);
	        	jQuery("#iframe").attr("src", theme_data[0]);
	        	jQuery("#switcher ul li ul").hide();
				jQuery('#theme-dropdown-select').text(theme_data[2])
				jQuery("li a").removeClass("active");
				jQuery('#theme-' + theme_data[3]).addClass('active')
				jQuery(".resize-icon").removeClass("active");
				jQuery(".desktop").addClass("active");
        	theme_list_open = false;

        	return false;

        	});

			jQuery(".resize-icon").click(function () {
				jQuery(".resize-icon").removeClass("active");
				jQuery(this).addClass('active')
			})

			jQuery('.iframe-wrap iframe').iframeTracker({
		        blurCallback: function(){
		            jQuery("#switcher ul li ul").hide();
		        }
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

			<ul>

			<li id="logo"><a href="http://themes.pagelines.com" class="sprite">Logo</a></li>

			<li id="theme-list"><a id="theme-dropdown-select" class="sprite" href="#">Choose A Theme</a>

				<ul id="theme-dropdown">

					<?php

					foreach ($this->themes as $i => $theme) :

					echo '<li><a id="theme-' . $i . '" href="#" rel="' . $theme['demo'] . ',' . $theme['url'] . ',' . $theme['name'] . ',' . $i . '">' . ucfirst($theme['name']) . ' ' . '<span>' . $theme['type'] . '</span></a></li>';

					endforeach;

					?>

				</ul>

			</li>	
			
			<li><a href="#" class="sprite resize-icon desktop active">desktop</a></li>
			<li><a href="#" class="sprite resize-icon ipad">tablet</a></li>
			<li><a href="#" class="sprite resize-icon iphone">mobile</a></li>
			<li id="right-side"  rel="<?php echo $current_theme_purchase_url; ?>">
				<a href="<?php echo $current_theme_purchase_url; ?>" class="purchase">Purchase</a>
				<a href="<?php echo $current_theme_url; ?>" class="sprite remove_frame">CLOSE</a>
			</li>

			</ul>

		</div>
<div class='iframe-wrap'>
<iframe id="iframe" src="<?php echo $current_theme_url; ?>" frameborder="0" width="100%"></iframe>
</div>
	<?php
		
	}
	
	function showbar() {
		if( isset( $_GET['dobar'] ) && ! is_user_logged_in() )
			return true;
	}
	
	function shortcode() {
		$themes = $this->themes;
			
		ob_start();
		?>
		
		<script>
		jQuery(document).ready(function () {			
			jQuery(".theme-demo-wrap ul li a").hover(function () {
				var slug = jQuery(this).attr("rel")
				jQuery(".demo-shot").attr("src", slug);
			})
		})
		</script>
		
		<div class="theme-demo-wrap">
			<ul class="theme-list">
				<?php
				foreach( $themes as $slug => $data ) {
					printf( '<li><a class="demo-hover" rel="%s" href="%s">%s</a></li>',
					sprintf( '%s/themes/%s/screenshot.png', WP_CONTENT_URL, $slug ),
					sprintf( '%s/?dobar', $data['demo'] ),
					$data['name']
				);
				}
				?>
			</ul>
			
			<div class="mba-wrapper">
			<?php printf( '<img class="demo-shot" src="%s/screenshot.png" />', get_template_directory_uri() ); ?>
			</div>
			
		</div>
		<?php
		
		return ob_get_clean();	
	}
}

new PageLines_Selector;