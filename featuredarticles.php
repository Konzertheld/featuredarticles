<?php if(count($theme->featuredarticles)): ?>
	<ul>
		<?php foreach($theme->featuredarticles as $article): ?>
		<li><?php echo $article->title; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>