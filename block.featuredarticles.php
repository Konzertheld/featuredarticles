<?php if ( ! defined('HABARI_PATH' ) ) { die( _t('Please do not load this page directly.') ); } ?>
<div id="featuredarticles">
	<?php if($content->_show_title):?><h2><?php _e($content->title); ?></h2><?php endif; ?>
	<?php if (isset($content->featuredarticles) && count($content->featuredarticles)):?>
	<ul class="postlist">
		<?php foreach ($content->featuredarticles as $article): ?>
		<li>
			<div class="postlist-meta">
				<h3><a href="<?php echo $article->permalink; ?>" title="<?php echo $article->title; ?>"><?php echo $article->title_out; ?></a></h3>
				<p><?php _e("Post of %s", array($article->pubdate->format())); ?></p>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>