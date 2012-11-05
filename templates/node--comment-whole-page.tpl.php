<?php

/**
 * @file
 * Bartik's theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct url of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type, i.e., "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode, e.g. 'full', 'teaser'...
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined, e.g. $node->body becomes $body. When needing to access
 * a field's raw values, developers/themers are strongly encouraged to use these
 * variables. Otherwise they will have to explicitly specify the desired field
 * language, e.g. $node->body['en'], thus overriding any language negotiation
 * rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 */
 
global $user;
global $base_url;

?>
<?php if ($page && $user->uid != $content['body']['#object']->uid && $user->uid != 1 && !in_array('administrator', $user->roles)): ?>
  <?php $this_cid = $content['body']['#object']->nid; ?>
  <?php $this_gid = $content['og_group_ref']['#items'][0]['target_id']; ?>
    <?php drupal_goto($base_url.'/node/'.$this_gid.'/comment-response/'.$this_cid); ?>
  <?php endif; ?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php print render($title_prefix); ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="meta submitted">
      <?php print $user_picture; ?>
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>

  <div class="content clearfix"<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
	  print '<p><strong>Comment ID:</strong> '.$content['body']['#object']->nid;
	  print ' &nbsp; <strong>User ID:</strong> '.$content['body']['#object']->uid;
	  if ($user->uid == 1 || in_array('administrator', $user->roles) || in_array('manager', $user->roles) || $user->uid != $content['body']['#object']->uid) {
		  if(isset($content['field_comment_flag'])){
			$flag_count = count($content['field_comment_flag']['#items']);
			$flags = '';
			for($i=0;$i<$flag_count;$i++){
				if($i > 0){
					$flags .= ', ';	
				}
				$flags .= $content['field_comment_flag'][$i]['#markup'];
			}
	  		print '<br/><strong>Flag(s):</strong> '.$flags;
		  }
	  }
	  $group_title = $content['og_group_ref'][0]['#title'].' - ';
	  $chapter_reference = $content['field_chapter_reference']['#items'][0]['entity']->field_heading['und'][0]['value'];  
	  print '<p><strong>Chapter:</strong> '.$chapter_reference.'<br/>';
	  print '<strong>Page:</strong> '.$content['field_start_page'][0]['#markup'].'</p>';
	  //print render($content['body']);
	  print '<p>'.$content['body'][0]['#markup'].'</p>';	  print '<p><strong>Date Submitted:</strong> '.date('m/d/Y - g:i', $content['body']['#object']->created).'<br/><strong>Last Modified:</strong> '.date('m/d/Y - g:i', $content['body']['#object']->changed).'</p>';
	  
	  if ($user->uid == 1 || in_array('administrator', $user->roles) || in_array('manager', $user->roles)) {
		//dpm($content);
		if(isset($content['field_edit_tracking'])){
			$edit_tracking = '<hr/><p class="instructions"><strong>Edit History:</strong>';
			foreach($content['field_edit_tracking']['#items'] as $edit){
				$edit_tracking .= '<br/>'.$edit['safe_value'];
			}
			$edit_tracking .= '</p>';
			print $edit_tracking;
		}
	  }
?>
  </div>

  <?php
    // Remove the "Add new comment" link on the teaser page or if the comment
    // form is being displayed on the same page.
    if ($teaser || !empty($content['comments']['comment_form'])) {
      unset($content['links']['comment']['#links']['comment-add']);
    }
    // Only display the wrapper div if there are links.
    $links = render($content['links']);
    if ($links):
  ?>
    <div class="link-wrapper">
      <?php print $links; ?>
    </div>
  <?php endif; ?>

  <?php print render($content['comments']); ?>

</div>
