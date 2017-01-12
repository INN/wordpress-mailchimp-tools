# MailChimp Tools for WordPress

## What is this?

This project aims to provide a simple set of tools for author MailChimp Campaigns via the WordPress post editor.

Right now, it provides:

- A settings panel to add/save your MailChimp API Key.
- A campaign editor meta box.
- A campaign preview meta box.
- A settings panel for setting campaign defaults on a per-post-type basis.

## Getting started

This tool set is *not* a plugin. You won't see it available in the WordPress dashboard for activation.

You *must* include the library in your plugin.

You can add the tools via [Composer](https://getcomposer.org/):

    composer require inn/wordpress-mailchimp-tools:dev-master

### A simple example

    require_once __DIR__ . '/vendor/autoload.php';

    function my_plugin_init() {
        register_post_type('newsletter', array(
            'label' => 'Newsletter',
            'labels' => array(
                'name' => 'Newsletters',
                'singular_name' => 'Newsletter'
            ),
            'show_ui' => true,
            'public' => true
        ));

    }
    add_action( 'init', 'my_plugin_init' );

## Templates

The campaign editor relies on an `mc:edit="body"` [editable content area](http://kb.mailchimp.com/templates/code/create-editable-content-areas-with-mailchimps-template-language) specified in whatever template you use.

When creating or updating a campaign, the tools will add the WordPress post's body to the element of your template that has the `mc:edit="body"` attribute.

A `simple-one-column.html` with the `mc:edit="body"` placemarker is included in the `templates/` directory. You can copy the contents on `simple-one-column.html` and use MailChimp's template importer to add the template to your account.

[Read more about MailChimp's templates here](http://kb.mailchimp.com/templates/code/getting-started-with-mailchimps-template-language).

### Set default content for your post type

You can use the `default_content` filter to load boilerplate markup into the post editor for your custom post type:

    function my_default_content($content) {
        $screen = $screen = get_current_screen();
        if ( $screen->post_type ==  'newsletter' ) {
            $default_content = file_get_contents( __DIR__ . '/templates/my-newsletter-default-markup.html' );
            return $default_content;
        }
        return $content;
    }
    add_filter( 'default_content', 'my_default_content' );
