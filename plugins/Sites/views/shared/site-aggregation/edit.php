<?php
$head = array('title' => 'Manage Site Group');
queue_js_file('site-aggregation');
queue_css_string("p#add_site_key {cursor: pointer}");
echo head($head);

?>
<?php echo flash(); ?>

<?php echo $form; ?>

<?php 
echo foot();
?>