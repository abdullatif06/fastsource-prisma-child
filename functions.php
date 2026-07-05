<?php
/**
 * Child-Theme functions and definitions
 */

// Load rtl.css because it is not autoloaded from the child theme
if ( ! function_exists( 'prisma_child_load_rtl' ) ) {
	add_filter( 'wp_enqueue_scripts', 'prisma_child_load_rtl', 3000 );
	function prisma_child_load_rtl() {
		if ( is_rtl() ) {
			wp_enqueue_style( 'prisma-style-rtl', get_template_directory_uri() . '/rtl.css' );
		}
	}
}

/**
 * UI enhancement: sticky + shrink-on-scroll header.
 *
 * The Elementor-built custom header on this site is position:absolute and
 * scrolls away with the page (the theme's built-in fixed mechanism targets
 * non-Elementor `.sc_layouts_row` headers, which this page doesn't use).
 * So we add a tiny scroll listener that toggles `.fs-sticky-on` on
 * `.top_panel`; the matching styles live in the child style.css (Phase 3).
 */
if ( ! function_exists( 'prisma_child_sticky_header' ) ) {
	add_action( 'wp_enqueue_scripts', 'prisma_child_sticky_header', 3100 );
	function prisma_child_sticky_header() {
		$js = <<<'JS'
(function () {
	function init() {
		var header = document.querySelector('.top_panel');
		if (!header) { return; }
		var THRESHOLD = 120;      // px scrolled before the header sticks
		var ticking = false;
		function update() {
			if (window.pageYOffset > THRESHOLD) {
				header.classList.add('fs-sticky-on');
			} else {
				header.classList.remove('fs-sticky-on');
			}
			ticking = false;
		}
		window.addEventListener('scroll', function () {
			if (!ticking) { window.requestAnimationFrame(update); ticking = true; }
		}, { passive: true });
		update();
	}
	if (document.readyState !== 'loading') { init(); }
	else { document.addEventListener('DOMContentLoaded', init); }
})();
JS;
		// Attach the inline script to an always-present handle (jQuery is enqueued by the theme).
		wp_add_inline_script( 'jquery-core', $js );
	}
}

?>