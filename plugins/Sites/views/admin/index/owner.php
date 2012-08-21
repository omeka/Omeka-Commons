<?php
queue_js('sites');
queue_css('sites');
$head = array('bodyclass' => 'sites primary',
              'title' => html_escape($site->title . ' Owner'));
head($head);

function approve_link($site) {
    if(empty($site->added)) {
        return "<span class='approve' id='approve-" . $site->id . "'>Approve</span>";
    }
    return $site->added;
}
?>
<h1><?php echo $head['title']; ?></h1>

<div id="primary">
    <?php echo flash(); ?>
    <h2></h2>
    
</div>


<?php foot(); ?>