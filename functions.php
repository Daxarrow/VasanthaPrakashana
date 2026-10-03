<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.2.1' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Admin notice
if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

/**
 * @snippet       Show product short description @ WooCommerce Loop
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 6
 * @community     https://businessbloomer.com/club/
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'display_short_description_on_shop', 5 );

function display_short_description_on_shop() {
	global $product;
	if ( ! $product ) {
		$product = wc_get_product( get_the_id() );
	}

	$short_description = $product->get_short_description();

	if ( ! empty( $short_description ) ) {
		 ?>
		<style>
					.shop-short-description {
				display: flex !important;
				flex-direction: row !important;
				flex-wrap: wrap !important; /* Allows items to drop to the next line */
				align-items: center !important;
				justify-content: center !important;
				gap: 5px !important;
				margin: 5px 0 !important;
				padding: 0 !important;
				text-align: center;
				line-height:1 !important;
			}

			.shop-short-description img {
				display: block !important;
				width: 25px !important; /* Slightly smaller to ensure icons stay together */
				height: auto !important;
				transition: transform 0.3s ease;
			}

			/* Target the Author text specifically */
			.shop-short-description p,
			.shop-short-description a[href*="author"],
			.shop-short-description > a:last-child {
				flex: 0 0 100% !important; /* Forces this element to take up 100% width */
				margin-top: 2px !important;
				display: block !important;
				width: 100% !important;
			}

			.shop-short-description a:hover img {
				transform: translateY(-3px) scale(1.1);
			}
		</style>
		<?php

		// Clean the description to separate images from text
		// We strip the <p> tags the editor adds so our flexbox handles the spacing
		$clean_description = strip_tags($short_description, '<a><img>');

		echo '<div class="shop-short-description">';
			// We wrap the description in a container that forces the row/column logic
			echo preg_replace(
				'/(<a[^>]*><img[^>]*><\/a>)/i',
				'<div class="icon-container">$1</div>',
				$short_description
			);
		echo '</div>';

	} else {
		echo '<span> - </span>';
	}
}
function display_short_description_on_shop_old() {
	global $product;
	$product = wc_get_product( get_the_id() );
	// Get the short description (excerpt)
	$short_description = $product->get_short_description();

	if ( ! empty( $short_description ) ) {
		// Display actual description
		 ?>
		<style>
			.shop-short-description {
				display: flex !important;
    			flex-direction: row !important;
    			align-items: center !important; /* Vertically centers logos of different heights */
    			justify-content: center !important; /* Centers the whole group on the card */
   				 gap: 5px !important; /* Standardized spacing between logos */
    			margin: 5px 0 !important;
    			padding: 0 !important;
    			min-height: 30px;
				/*padding-left: 65px;*/
				line-height: 1 !important;
			}


			.shop-short-description img
			 {
				display: block !important;
				width: 30px !important; /* Prevents stretching */
				max-height: 30px;  /*Limits height so they stay uniform */
				margin-left: 0px !important;
				align-items: center;
				transition: transform 0.3s ease, filter 0.3s ease, box-shadow 0.3s ease;

			}

			.shop-short-description a {
				display: flex !important;
				align-items: center !important;
				text-decoration: none !important;
				padding: 0 !important;
				margin: 0 !important;
				border: none !important;
			}


						/* Hover Effect */
			.shop-short-description a:hover img {
				transform: translateY(-3px) scale(1.1);
				filter: brightness(1.1); /* Makes the logo slightly more vibrant */
    			box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
			}
			/*.shop-short-description img:hover {
  				transform: translateY(-5px) scale(1.1); /* Lifts up 5px and grows 10% */
    			/*filter: brightness(1.1);  Makes the logo slightly more vibrant
    			box-shadow: 0px 5px 15px rgba(0,0,0,0.1);
			}*/

			@media only screen and (max-width: 480px) {
				.shop-short-description {
					justify-content: center; /* Centers the logos on mobile product cards */
					gap: 5px;
					padding: 0px;
				}

				.shop-short-description img {
					max-height: 25px; /* Smaller logos so all 3 fit in one row on mobile */
				}

				.shop-short-description img:hover {
  					transform: scale(1.1); /* Zooms the image to 110% of its original size on hover */
				}
			}

   		</style>
        <?php

		//$short_description = apply_filters( 'woocommerce_short_description', $product->get_short_description() );

		echo '<div class="shop-short-description">' . $short_description . '</div>';
	} else {
		// Display your placeholder text
		echo '<span> - </span>';
	}
}



/**
 * Remove WooCommerce breadcrumbs and replace with a "Back to Category" button
 */
add_action('init', 'custom_replace_breadcrumbs');

function custom_replace_breadcrumbs() {
    // 1. Remove the default breadcrumbs
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

    // 2. Hook in our custom button
    add_action('woocommerce_before_main_content', 'custom_back_button', 20);
}

function custom_back_button() {
    if (is_product()) {
        global $post;

        // Find the category slug for the current product
        $slug_to_use = '';
        $terms = get_the_terms($post->ID, 'product_cat');

        if ($terms && !is_wp_error($terms)) {
            $main_term = $terms[0];
            // If it's a sub-category, get the top-level parent (for the main Elementor tabs)
            if ($main_term->parent != 0) {
                $ancestors = get_ancestors($main_term->term_id, 'product_cat');
                $root_id = end($ancestors);
                $root_term = get_term($root_id, 'product_cat');
                $slug_to_use = $root_term->slug;
            } else {
                $slug_to_use = $main_term->slug;
            }
        }

        echo '<div class="custom-back-button-wrapper" style="margin-bottom: 20px;">';
        // We pass the slug to the JS using 'data-category'
        echo '<a href="#" id="dynamic-back-btn"
                 data-category="' . esc_attr($slug_to_use) . '"
                 class="button back-to-cat-btn">← Go Back</a>';
        echo '</div>';
    }
}

add_action('wp_footer', 'navigation_from_single_product');
function navigation_from_single_product() {
    if (!is_product()) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const backBtn = document.getElementById('dynamic-back-btn');
        if (!backBtn) return;

        const referrer = document.referrer;
        const categoriesUrl = "<?php echo home_url('/categories'); ?>";
        const productCatSlug = backBtn.getAttribute('data-category');

        if (referrer && referrer.includes('/categories')) {
            // User came from Categories:
            // 1. Check if the referrer already has the param
            // 2. If not, add it so the tab highlights
            let targetUrl = referrer;
            if (productCatSlug && !referrer.includes('select_cat=')) {
                const separator = referrer.includes('?') ? '&' : '?';
                targetUrl = referrer + separator + 'select_cat=' + productCatSlug;
            }

            backBtn.href = targetUrl;
            backBtn.innerHTML = "← Back to Categories";
        } else if (referrer && referrer !== window.location.href) {
            backBtn.href = referrer;
            backBtn.innerHTML = "← Go Back";
        } else {
            backBtn.href = "<?php echo home_url(); ?>";
            backBtn.innerHTML = "← Back to Home";
        }
    });
    </script>
    <?php
}

/**
 * Highlight the respective categories
 **/
/**
 * Highlight and persist the active category tab across pagination
 **/
add_action('wp_footer', 'categories_selection');
function categories_selection() {
    // Target the categories page or WooCommerce category view
    if (!is_page('categories') && !is_shop()) return;
    ?>
    <script>
    jQuery(document).ready(function($) {

        // --- 1. SAVE SELECTION ---
        // Save Main Elementor Tab
        $(document).on('click', '.elementor-tab-title', function(e) {
            // Ignore programmatic clicks during tab restoration
            if (e.isTrigger) return;

            var mainTabId = $(this).attr('data-tab');
            if (mainTabId) {
                sessionStorage.setItem('vp_main_tab', mainTabId);
                sessionStorage.removeItem('vp_sub_tab');
            }
        });

        // Save Sub-Category Button
        $(document).on('click', '.elementor-button, .sub-cat-button', function(e) {
            if (e.isTrigger) return;

            var subTabId = $(this).attr('id') || $.trim($(this).text());
            if (subTabId) {
                sessionStorage.setItem('vp_sub_tab', subTabId);
            }
        });

        // Save active tab when pagination links are clicked
        $(document).on('click', '.woocommerce-pagination a, .page-numbers a', function() {
            var activeMain = $('.elementor-tab-title.elementor-active').attr('data-tab');
            if (activeMain) {
                sessionStorage.setItem('vp_main_tab', activeMain);
            }
        });

        // --- 2. RESTORE SELECTION ---
        function restoreCategoriesTabs() {
            var savedMain = sessionStorage.getItem('vp_main_tab');
            var savedSub = sessionStorage.getItem('vp_sub_tab');

            if (savedMain) {
                var $mainTab = $('.elementor-tab-title[data-tab="' + savedMain + '"]');

                if ($mainTab.length > 0) {
                    if (!$mainTab.hasClass('elementor-active')) {
                        // Use triggerHandler or native click without triggering the save loop
                        $mainTab.trigger('click');
                    }

                    if (savedSub) {
                        setTimeout(function() {
                            var $subTab = $('#' + CSS.escape(savedSub)).length
                                ? $('#' + CSS.escape(savedSub))
                                : $('.elementor-button:contains("' + savedSub + '")');

                            if ($subTab.length > 0) {
                                $subTab.trigger('click');
                            }
                        }, 300);
                    }
                }
            }
        }

        // Run after initial page and Elementor frontend initializations complete
        if (window.elementorFrontend) {
            restoreCategoriesTabs();
        } else {
            $(window).on('elementor/frontend/init', restoreCategoriesTabs);
            // Fallback for non-Elementor pages or standard load
            setTimeout(restoreCategoriesTabs, 400);
        }
    });
    </script>
    <?php
}

/**
 * Automatically append selected category parameter to WooCommerce pagination links
 */
add_filter('paginate_links', 'preserve_category_param_in_pagination');
function preserve_category_param_in_pagination($link) {
    if (isset($_GET['select_cat']) && !empty($_GET['select_cat'])) {
        $link = add_query_arg('select_cat', sanitize_text_field($_GET['select_cat']), $link);
    }
    return $link;
}

/**
 * Selecting the respective categories tab while using the pagination links
 */
add_action('wp_footer', 'categories_selection_fixed');
function categories_selection_fixed() {
    if (!is_page('categories') && !is_shop()) return;
    ?>
    <script>
    jQuery(document).ready(function($) {

        // Helper: Get parameter from current URL
        function getUrlParameter(name) {
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            return results ? decodeURIComponent(results[1]) : null;
        }

        // 1. Get active category from URL first, fallback to SessionStorage
        var activeCat = getUrlParameter('select_cat') || sessionStorage.getItem('vp_active_cat');

        if (activeCat) {
            sessionStorage.setItem('vp_active_cat', activeCat);

            // Highlight/activate matching button
            var $activeBtn = $('.elementor-button, .sub-cat-button').filter(function() {
                var btnText = $.trim($(this).text()).toLowerCase();
                var btnId = ($(this).attr('id') || '').toLowerCase();
                return btnText === activeCat.toLowerCase() || btnId === activeCat.toLowerCase();
            });

            if ($activeBtn.length) {
                $activeBtn.addClass('active-category-btn');
            }
        }

        // 2. Save category when user clicks any category button
        $(document).on('click', '.elementor-button, .sub-cat-button', function() {
            var catName = $(this).attr('id') || $.trim($(this).text());
            sessionStorage.setItem('vp_active_cat', catName);
        });

        // 3. Attach category parameter to pagination links dynamically (fallback if PHP filter is not used)
        $(document).on('click', '.woocommerce-pagination a, .page-numbers a', function(e) {
            var savedCat = sessionStorage.getItem('vp_active_cat');
            if (savedCat) {
                var href = $(this).attr('href');
                if (href && href.indexOf('select_cat=') === -1) {
                    e.preventDefault();
                    var separator = href.indexOf('?') !== -1 ? '&' : '?';
                    window.location.href = href + separator + 'select_cat=' + encodeURIComponent(savedCat);
                }
            }
        });

    });
    </script>
    <?php
}


add_action('wp_footer', 'vp_global_author_popup');
function vp_global_author_popup() {
    ?>
    <div id="author-overlay" onclick="toggleAuthorPopup('close')"></div>
    <div id="author-popup">
        <div id="author-popup-inner">
            <h2 id="v-author-name"></h2>
            <div id="v-author-bio"></div>
            <button onclick="toggleAuthorPopup('close')">Close</button>
        </div>
    </div>

    <script>
    function toggleAuthorPopup(status, name = '', bio = '') {
        const popup = document.getElementById('author-popup');
        const overlay = document.getElementById('author-overlay');

        if (status === 'open') {
            document.getElementById('v-author-name').innerText = name;
            document.getElementById('v-author-bio').innerHTML = bio;
            popup.style.display = 'block';
            overlay.style.display = 'block';
        } else {
            popup.style.display = 'none';
            overlay.style.display = 'none';
        }
    }
    </script>
    <?php
}

//Removing the sorting select option from All Books page
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );


