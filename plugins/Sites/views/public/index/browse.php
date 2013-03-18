<?php

$bodyclass = 'page sites-browse';
echo head(array('title' => __('Browse sites') , 'bodyclass' => $bodyclass)); 
?>
<div id="primary">

<?php foreach(loop('site') as $site) : ?>
    <div class='sites-site'>
    <h2><?php echo  $site->title; ?></h2>
    <?php echo sites_site_logo($site); ?>
    <div class='sites-sample'>
        <?php if($random_item = sites_random_site_item($site)): ?>
                <p>Example Item:</p>
                <h3><?php echo link_to_item(null, array(), 'show',  $random_item); ?></h3>
                <?php echo item_image('square_thumbnail', array(), 0, $random_item); ?>
                <p><?php echo link_to($site, 'show', 'Explore in the Commons'); ?></p>
        <?php endif; ?>
    </div>
    <p><?php echo $site->description; ?></p>
    <p><?php echo sites_link_to_original_site($site); ?>

    </div>
<?php endforeach; ?>
</div>
<?php echo foot(); ?>