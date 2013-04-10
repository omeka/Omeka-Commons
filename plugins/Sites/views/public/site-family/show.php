<?php
echo head(array('title'=>$site_family->name));
?>

<h1><?php echo metadata($site_family, 'name'); ?></h1>
<div>
<?php echo metadata($site_family, 'description'); ?>
</div>
<ul>
<?php foreach(loop('sites', $sites) as $site): ?>
<li><?php echo link_to($site, 'show', metadata($site, 'title')); ?></li>
<?php endforeach; ?>
</ul>
<?php echo foot(); ?>