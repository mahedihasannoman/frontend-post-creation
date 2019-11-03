## Frontend Post Creation

Frontend Post Creation is a small Wordpress plugin that displays a form that will let the user create WordPress Post entries when submitted. Available fields title, category selector, editor, tag and featured image. After post successfully published, It will redirect you to the post page immediately.

To add Create Post Form in a page, you have to use the shortcode: `[fpc-form]`


This plugin provides a filter which is `add_filter( 'bp_after_has_members_parse_args', 'fpc_remove_admin_member_from_buddy_member_page' );` that can be used in a child theme to excludes all admin members from being listed in the BuddyPress members page.

This plugin also add current theme name & version in admin dashbord footer. The responsible filter for this feature is `add_filter('admin_footer_text', 'change_wp_dashboard_footer');` which is already added in pluign main file.

You can see quick demo of Frontend Post Creation form [here](https://rsfaq.braintum.com/create-post/).
