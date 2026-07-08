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

/**
 * Phase A (fix): repaint the theme accent from lime to the FastSource rose.
 *
 * The Prisma theme colors links/buttons/accents through its OWN CSS custom
 * properties (--theme-color-text_link, --alter_link, --extra_link and their
 * _hover steps), whose VALUES come from Theme Options stored in the database
 * and printed inline in <head> at runtime. That inline block overrides the
 * child style.css, which is why editing the stylesheet alone did nothing.
 *
 * So we re-declare just the ACCENT variables here and inject them with a LATE
 * priority + an id-scoped :root so this rule loads after the theme's inline
 * vars and wins on order. Neutrals/backgrounds are left untouched — only the
 * accent is remapped, matching the Phase A token decision (rose #B11742).
 */
if ( ! function_exists( 'prisma_child_accent_override' ) ) {
	// Priority 999 on wp_head prints this AFTER the theme's own inline <head>
	// variable block, whatever mechanism emitted it — so source order (equal
	// specificity, last wins) decides in our favour without any !important.
	add_action( 'wp_head', 'prisma_child_accent_override', 999 );
	function prisma_child_accent_override() {
		$accent       = '#B11742'; // --fs-accent
		$accent_hover = '#880D30'; // --fs-accent-hover
		$accent_glow  = '#E07799'; // --fs-accent-glow

		echo <<<CSS
<style id="fs-accent-override">
:root {
	/* primary accent (all three theme scopes) */
	--theme-color-text_link:  {$accent};
	--theme-color-text_hover: {$accent_hover};
	--theme-color-alter_link: {$accent};
	--theme-color-alter_hover:{$accent_hover};
	--theme-color-extra_link: {$accent};
	/* blend variants the theme derives from the link colour */
	--theme-color-text_link_blend:  {$accent_glow};
	--theme-color-alter_link_blend: {$accent_glow};
}
</style>

CSS;
	}
}

?>