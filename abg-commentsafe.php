<?php 
	/*

	abg CommentSafe - A simple plugin that removes additional, specific tags from your comments.

	Copyright (c) 2009 Anthony Williams
	<http://abitgone.co.uk/projects/commentsafe>

	This program is free software; you can redistribute it and/or modify 
	it under the terms of the GNU General Public License as published by 
	the Free Software Foundation; either version 2 of the License, or 
	(at your option) any later version.

	This program is distributed in the hope that it will be useful, 
	but WITHOUT ANY WARRANTY; without even the implied warranty of 
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
	
	See the	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License 
	along with this program; if not, write to the Free Software 
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA
	
	*/
	
	define("COMMENTSAFE_VERSION", "1.0.0"); 

	### WordPress Plugin Interface ###
	
/*
Plugin Name: 	abg CommentSafe
Plugin URI: 	http://abitgone.co.uk/projects/commentsafe
Description: 	A very simple plugin that removes additional, specific tags from comments. Go to <a href="?page=abg_commentsafe">Plugins&rarr;CommentSafe Options</a> (once you've activated the plugin) for more details and to set your preferred tags.
Version: 		1.0.0
Author: 		Anthony Williams
Author URI: 	http://abitgone.co.uk/
*/

	#
	#	Add menu item to Options menu.
	#
	function abg_commentsafe_options() 
	{
		if (function_exists('add_options_page')) 
		{
			add_submenu_page(
				'plugins.php',
				'CommentSafe Options',
				'CommentSafe Options',
				8,
				'abg_commentsafe',
				'abg_commentsafe_options_page'
			);
		}
	}
	
	#
	#	Trigger adding the menu option
	#
	add_action('admin_menu', 'abg_commentsafe_options');
	
	#
	#	Draw the normal options page
	#
	function abg_commentsafe_options_page() {
	
		$abg_commentsafe_tags = get_option('abg_commentsafe_tags');
		$abg_commentsafe_behaviour = get_option('abg_commentsafe_behaviour');
		$saved_tags = "";
		
		if (isset($_POST['submit'])) 
		{
			$abg_commentsafe_tags = trim($_POST['abg_commentsafe_tags']);
			$abg_commentsafe_tags = str_replace(" ", "|", trim(preg_replace("/[\s]{2,}/", " ", $abg_commentsafe_tags)));
			update_option('abg_commentsafe_tags', trim($abg_commentsafe_tags));
			
			$abg_commentsafe_behaviour = $_POST['abg_commentsafe_behaviour'];
		}

		switch($abg_commentsafe_behaviour) {
			case "strip":
			case "convert":
			case "remove":
				// Do nothing
				break;
			default:
				$abg_commentsafe_behaviour = "strip";
		}

		if (isset($_POST['submit'])) 
		{		
			update_option('abg_commentsafe_behaviour', $_POST['abg_commentsafe_behaviour']);
		}
		
		?>
			<div class="wrap">
				<h2>aBitGone CommentSafe</h2>
				<p>CommentSafe is a simple plugin that removes additional, specific tags from your comments.</p>
				<p><?php echo __('By default, WordPress allows a myriad of potentially harmful tags to be used in comments posted on your site. CommentSafe, very simply, aims to remove any (X)HTML tags you specify from all of the comments posted to your site.'); ?></p>
				<h3><?php echo __('Simple Settings'); ?></h3>
				<p><?php echo __('Enter the tags you wish to strip, separated with spaces:'); ?></p>
				<form action="" method="post" id="abg_commentsafe">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row"><label for="abg_commentsafe_tags">Strip these tags:</label></th>
								<td>
									<input name="abg_commentsafe_tags" id="abg_commentsafe_tags" type="text" value="<?php echo str_replace("|", " ", $abg_commentsafe_tags); ?>" class="regular-text code" /><br />
									You should consider adding <code>STYLE</code>, <code>SCRIPT</code>, <code>IFRAME</code>, <code>EMBED</code> and <code>OBJECT</code>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="abg_commentsafe_behaviour">Strip method:</label></th>
								<td>
									<input name="abg_commentsafe_behaviour" id="abg_commentsafe_behaviour_strip" type="radio" value="strip"<?php if ($abg_commentsafe_behaviour == "strip") { ?> checked="checked"<?php } ?> /> Strip tags (example: <code>A &lt;b&gt;bold&lt;/b&gt; choice</code> becomes <code>A bold choice</code> &mdash; default)<br />
									<input name="abg_commentsafe_behaviour" id="abg_commentsafe_behaviour_convert" type="radio" value="convert"<?php if ($abg_commentsafe_behaviour == "convert") { ?> checked="checked"<?php } ?> /> Convert tags (example: <code>A &lt;b&gt;bold&lt;/b&gt; choice</code> becomes <code>A &amp;lt;b&amp;gt;bold&amp;lt;/b&amp;gt; choice</code>)<br />
									<input name="abg_commentsafe_behaviour" id="abg_commentsafe_behaviour_remove" type="radio" value="remove"<?php if ($abg_commentsafe_behaviour == "remove") { ?> checked="checked"<?php } ?> /> Remove tags and content (example: <code>A &lt;b&gt;bold&lt;/b&gt; choice</code> becomes <code>A choice</code>)
									<p class="submit"><input type="submit" name="submit" value="<?php echo __('Update Tags'); ?>"></p>
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</div>
		<?php
	
	}
	
	#
	#	CommentSafe Function
	#
	function abg_commentsafe($text)
	{
		$abg_commentsafe_tags = get_option('abg_commentsafe_tags');
		$abg_commentsafe_behaviour = get_option('abg_commentsafe_behaviour');
		
		$regex_pattern = '/\<((' . $abg_commentsafe_tags . ')\b[^\>]*)>(.*?)\<\/\2\>/';
		$regex_replace = "";
		
		switch($abg_commentsafe_behaviour) {
			case "strip":
				$regex_replace = '$3';
				break;
			case "convert":
				$regex_replace = '&lt;$1&gt;$3&lt;$2/&gt;';
				break;
			case "remove":
			default:
				$regex_replace = "";
				break;
		}
		
		$commentsafe_text = preg_replace($regex_pattern, $regex_replace, $text);
		
		return $commentsafe_text;
	}
	
	#
	#	Add comment safe to comments:
	#	This will use the pre_comment_content hook to alter the content BEFORE it is saved to
	#	the database, thus saving Wordpress from having to run it multiple times when the post
	#	and its comments are viewed. This also means that any comments which are sanitised will
	#	remain sanitised after the plugin is deactivated or uninstalled.
	#
	add_filter('pre_comment_content', 'abg_commentsafe');

?>