<?php

$bodyclass = 'page sites-browse';
?>

<?php head(array('title' => $site->title , 'bodyclass' => $bodyclass)); ?>
<div id="primary">
<?php foreach($sites as $site): ?>
<div class='site'>
<h2><?php echo sites_link_to_site($site); ?></h2>
<p><?php echo $site->description; ?></p>
<p><?php echo sites_link_to_original_site($site); ?>
<div class='site-sample'>

<?php
    $items = sites_random_site_item($site);
    $item = $items[0];

    echo item_square_thumbnail(array(), 0, $item);
?>
<h3><?php echo item('Dublin Core', 'Title', array(), $item); ?></h3>
</div>
</div>
<?php endforeach; ?>
</div>
