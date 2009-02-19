=== aBitGone CommentSafe ===
Contributors: abitgone
Tags: comments, spam, xhtml, html, tags, strip, cleanse, plugin
Requires at least: 2.7
Tested up to: 2.7.1
Version: 1.0.0
Stable tag: 1.0.0

CommentSafe is a very simple plugin which removes (X)HTML tags you specify from your comments. 

== Description ==

CommentSafe is a really simple plugin that I've written for WordPress. Very simply, it takes a list 
of (X)HTML tags - which you can specify through the admin interface - and does one of three things 
with those tags, when they're found in your comments:

1. Strip tags (example: `A <b>bold</b> choice` becomes `A bold choice` - default)
2. Convert tags (example: `A <b>bold</b>` choice becomes `A &lt;b&gt;bold&lt;/b&gt; choice`)
3. Remove tags and content (example: `A <b>bold</b> choice` becomes `A choice`)

The comments are changed before being saved to your WordPress database so that, if you decide to 
uninstall or deactivate CommentSafe, your comments will remain sanitised and safe.

For further information and updates, see <http://abitgone.co.uk/projects/commentsafe>.

== Installation ==

1. Upload `abg-commentsafe.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'abg CommentSafe' in the 'Settings' menu in WordPress and enter the list of (X)HTML tags 
   you wish to filter.
4. Choose a strip method
5. Update the Tags

== Screenshots ==

1. The CommentSafe administration interface, making it super-easy for you to modify the list of 
   (X)HTML tags you wish you filter.