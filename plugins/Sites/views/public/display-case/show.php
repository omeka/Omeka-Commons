<?php

$bodyclass = 'page display-case';
echo head(array('title' => $site->title , 'bodyclass' => $bodyclass)); 
?>

<div id="primary">
<div class='sites-banner'>
<?php echo sites_site_banner($site); ?>
</div>
<h1><?php echo $site->title; ?></h1>
<div style='float:right' id='sites-logo'>
<?php echo sites_site_logo($site); ?>
</div>
    <div id="sites-overview">
    <h2>Overview</h2>
    <?php if($family): ?>
    <p><?php echo metadata($site, 'title'); ?> is part of <?php echo link_to($family, 'show', metadata($family, 'name'));?></p>
    <?php endif; ?>
    <?php echo $site->description; ?>
    <h3>Collections</h3>
    <ul id='sites-context'>
    <?php foreach(loop('site_context_collection') as $collection) : ?>
        <li>
        <?php echo sites_link_to_original_context(); ?>
        </li>
    <?php endforeach; ?>
    </ul>
    <h3>Exhibits</h3>
    <ul id='sites-context'>

    <?php foreach(loop('site_context_exhibit') as $exhibit ) : ?>
        <li>
        <?php echo sites_link_to_original_context(); ?>
        </li>

    <?php endforeach; ?>
    </ul>
    </div>

    <?php echo tag_cloud($this->tags, '/commons/items'); ?>

    <div id="recent-items">
    <h2>Recently added items</h2>
    <ul class='sites-items'>

    <?php foreach(loop('item') as $item): ?>
    <li>
        <?php echo link_to_item(); ?>
        <?php if (metadata('item', 'has thumbnail')): ?>
        <div class="sites-item-img">
            <?php echo link_to_item(item_image('square_thumbnail')); ?>
        </div>
        <?php endif; ?>
    </li>

    <?php endforeach; ?>

    </ul>
    </div>

</div>
<?php echo foot(); ?>