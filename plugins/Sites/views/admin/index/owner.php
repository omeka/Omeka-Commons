<?php
queue_js_file('sites');
queue_css_file('sites');
$head = array('bodyclass' => 'sites primary',
              'title' => html_escape($site->title . ' Owner'));
echo head($head);
?>
<div id="primary">
    <?php echo flash(); ?>
</div>

<?php echo foot(); ?>