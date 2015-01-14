One Page Layouts
================

This is a plugin for theme developers. When used with the Menu Customizer, it gives you a way to manage a "one-page" site of content blocks in the customizer.

### How to set up (in code)

Set up via `add_theme_support`. Here are the defaults:

	add_theme_support( 'one-page-layout', array(
		'menu_name'        => __( 'One Page Layout', 'one-page' ),
		'posts-template'   => 'content',
		'custom-format'    => '<div id="%1$s" class="hentry custom"><h1 class="entry-title"><a href="%2$s">%3$s</a></h1> <div class="entry-summary"><p>%4$s</p></div></div>',
		'archive-template' => 'content',
		'archive-before'   => '<div id="%1$s" class="archive %2$s"><h1 class="page-title">%3$s</h1>',
		'archive-after'    => '</div>',
		'archive-count'    => '6',
	) );

Since menus can contain posts, pages, taxonomy archives, and custom links, the configuration is a little involved. For displaying posts & pages, we grab the template defined in `posts-template` (content.php by default).

Taxonomy archives are trickier. They're wrapped in `archive-before` and `archive-after`. The placeholders in `archive-before` are 1: an ID, 2: the taxonomy being displayed, 3: the name of this taxonomy. Each post in the archive is displayed with `archive-template` (content.php, again). It'll only display `archive-count` posts. Note: does not take into account non-post post types.

Lastly, custom links are output from a format string; 1: an ID, 2: the URL, 3: link title, and 4: description.

### How to set up (in WordPress)

Create a new menu with the pages/posts/categories etc that you want on your front page. Set this menu to the "One Page Layout" menu location (theme devs can rename this).

### Displaying your layout

Use this function in your template, most likely front-page.php, instead of the loop.

	<?php onepage_layout(); ?>

If you're using [Menu Customizer](https://github.com/voldemortensen/menu-customizer), you can jump into the customizer and add/manage items in the menu, and see your changes in real-time.

### Try it out

Download ["One Page Aventurine"](https://cloudup.com/cMikaM9p2Oa), a child theme of [Aventurine](https://wordpress.org/themes/aventurine), a quick proof-of-concept for how to use this in a theme.
