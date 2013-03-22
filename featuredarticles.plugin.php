<?php
namespace Habari;
/**
 * TODO: Rights management (token for featuring)
 * 
 **/
class FeaturedArticles extends Plugin
{
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
		if(User::identify()->id)
		{
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
?>