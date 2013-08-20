<?php
queue_js_file('site-aggregation');
queue_css_string("p#add_site_key {cursor: pointer}");
echo head(array('title' => 'Add Site Group'));

?>
<?php echo flash(); ?>
<?php echo $form; ?>

<?php 
echo foot();
?>