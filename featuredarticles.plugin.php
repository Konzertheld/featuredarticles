<?php
namespace Habari;

class FeaturedArticles extends Plugin
{
	/**
	 * Create permission token
	 **/
	public function action_plugin_activation()
	{
		ACL::create_token('feature_content', 'Mark content as featured');
	}
	
	/**
	 * Remove permission token
	 **/
	public function action_plugin_deactivation()
	{
		ACL::destroy_token('feature_content');
	}
	 
	/**
	 * Register templates
	 **/
	public function action_init()
	{
		$this->add_template( 'block.featuredarticles', dirname(__FILE__) . '/block.featuredarticles.php' );
	}

	/**
	 * Make the block available
	 **/
	public function filter_block_list( $blocklist )
	{
		$blocklist[ 'featuredarticles' ] = _t( 'Featured articles' );
		return $blocklist;
	}
	
	/**
	 * Put data into the block
	 **/
	public function action_block_content_featuredarticles( $block )
	{
		$block->featuredarticles = Posts::get(array("any:info" => array("featured" => 1), "limit" => 4, "orderby" => "RAND()"));
	}
	
	/**
	 * Insert the Javascript clickable star image that indicates the feature status
	 * The theme TheViewInside supplys this hook
	 **/
	public function theme_jumplist($theme, $post, $multiple = false)
	{
		$user = User::identify();
		if($user->can('feature_content')) {
			$featureclass = ($post->info->featured)?"featured":"notfeatured";
			return "<a id='feature$post->id' onclick='FeaturedArticles.feature($post->id);' class='$featureclass paginationicon'><img class='paginationicon' src='" . $this->get_url("/$featureclass.png") . "' id='featureimg$post->id' alt='$featureclass' title='$featureclass'></a>";
		}
		return "";
	}
	
	
	/**
	 * Add the Javascript
	 **/
	public function action_template_header()
	{
		Stack::add('template_header_javascript', Site::get_url('scripts') . '/jquery.js', 'jquery');
		Stack::add('template_header_javascript', $this->get_url(true) . 'featuredarticles.js', 'featuredarticles');
		// Set the callback url
		$url = "FeaturedArticles.url = '" . URL::get( 'auth_ajax', array( 'context' => 'feature_article') ) . "';";
		Stack::add('template_header_javascript', $url, 'feature_article_url', 'featuredarticles');
		// Set the plugin url which is needed to exchange the featured image
		$pluginurl = "FeaturedArticles.pluginurl = '" . $this->get_url() . "';";
		Stack::add('template_header_javascript', $pluginurl, 'feature_article_pluginurl', 'featuredarticles');
	}
	
	/**
	 * Check if an article is featured when requested via JS and invert it's feature status
	 **/
	public function action_auth_ajax_feature_article($handler)
	{
		$user = User::identify();
		if($user->can('feature_content')) {
			// Get the data that was sent
			$id = $handler->handler_vars[ 'q' ];
			// Do actual work
			if(is_numeric($id))
			{
				$post = Post::get(array('id' => $id));
				if($post->info->featured)
					$post->info->featured = false;
				else
					$post->info->featured = true;
				if($post->update(true))
				{
					// Wipe anything else that's in the buffer
					ob_end_clean();
					echo ($post->info->featured)?"featured":"notfeatured";
				}
			}
		}
	}
	
	/*
	 * Add checkbox to admin
	 */
	public function action_form_publish($form, $post, $context)
	{
		$user = User::identify();
		if($user->can('feature_content')) {
			$form->settings->insert('comments_enabled', 'checkbox', 'featured', 'null:null', _t('Feature this post'), 'tabcontrol_checkbox');
			if(isset($post->info->featured)) {
				$form->settings->featured->value = $post->info->featured;
			}
		}
	}
	
	/*
	 * Save featured status from admin
	 */
	public function action_publish_post($post, $form)
	{
		$user = User::identify();
		if($user->can('feature_content')) {
			if($form->settings->featured->value) {
				$post->info->featured = true;
			}
			else {
				unset($post->info->featured);
			}
		}
	}
}
?>