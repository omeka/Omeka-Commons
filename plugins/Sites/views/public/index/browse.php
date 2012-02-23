<?php

$bodyclass = 'page sites-browse';
?>

<?php head(array('title' => $site->title , 'bodyclass' => $bodyclass)); ?>
<div id="primary">
<?php foreach($sites as $site): ?>
<div class='site'>
<h2><?php echo sites_link_to_site($site); ?></h2>
<div class='site-sample'>
    <p>Example Item:</p>
    <?php
        $items = sites_random_site_item($site);
        $item = $items[0];
    ?>
    <h3><?php echo link_to_item(null, array(), 'show',  $item); ?></h3>
    <?php echo item_square_thumbnail(array(), 0, $item); ?>
    <p><?php echo sites_link_to_site($site, 'Explore in the Commons'); ?></p>
</div>
<p><?php echo $site->description; ?></p>
<p><?php echo sites_link_to_original_site($site); ?>

</div>
<?php endforeach; ?>
</div>
<?php echo foot(); ?>