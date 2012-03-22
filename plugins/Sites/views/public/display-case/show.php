<?php

$bodyclass = 'page display-case';
?>

<?php head(array('title' => $site->title , 'bodyclass' => $bodyclass)); ?>
<div id="primary">
<h1><?php echo $site->title; ?></h1>
<div style='float:right'>
<?php echo sites_site_logo($site); ?>
</div>
    <div id="overview">
    <h2>Overview</h2>
    <?php echo $site->description; ?>
    <h3>Collections</h3>
    <ul>
    <?php foreach($collections as $collection): ?>
        <li>
        <?php echo $collection->title; ?>
        </li>

    <?php endforeach; ?>
    </ul>
    <h3>Exhibits</h3>
    <ul>
    <?php foreach($exhibits as $exhibit): ?>
        <li>
        <?php echo $exhibit->title; ?>
        </li>

    <?php endforeach; ?>
    </ul>
    </div>

    <?php echo tag_cloud($this->tags, '/commons/items'); ?>

    <div id="recent-items">
    <h2>Recently added items</h2>
    <ul>

    <?php set_items_for_loop($items); ?>
    <?php while(loop_items()): ?>
    <li>
    <?php echo link_to_item(); ?>
    <?php if(item_has_thumbnail()): ?>
    <div class="item-img">
        <?php echo link_to_item(item_square_thumbnail()); ?>
    </div>
    <?php endif; ?>
    </li>

    <?php endwhile; ?>

    </ul>
    </div>

</div>
<?php echo foot(); ?>