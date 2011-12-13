<?php
queue_js('installations');
$head = array('bodyclass' => 'installations primary',
              'title' => html_escape('Installations'));
head($head);

function approve_link($installation) {
    
    if(empty($installation->added)) {
        return "<span class='approve' id='approve-" . $installation->id . "'>Approve</span>";
    }
    return $installation->added;
    
}
?>
<h1><?php echo $head['title']; ?></h1>

<div id="primary">
    <?php echo flash(); ?>
    <div id="browse-meta" class="group">
        <div id="browse-meta-lists">
            <ul id="items-sort" class="navigation">
                <li><strong>Quick Filter</strong></li>
            <?php
                echo nav(array(
                    'All' => uri('installations/index/browse'),
                    'Needs Approval' => uri('installations/index/browse?unapproved=true')
                ));
            ?>
            </ul>
        </div>
    </div>
    
    
    
    <table>
    	<thead>
    	<tr>
    	<th>Title</th>
    	<th>Description</th>
    	<th>URL</th>
    	<th>Admin Email</th>
    	<th>Author</th>
    	<th>Site Copyright</th>
    	<th>Last Import</th>
    	<th>Approved</th>
    	</tr>
    	
    	</thead>
    	<tbody>
        <?php foreach($installations as $installation): ?>
        <tr>
        <td><?php echo $installation->title; ?></td>
        <td><?php echo $installation->description; ?></td>
        <td><?php echo $installation->url; ?></td>
        <td><?php echo $installation->admin_email; ?></td>
        <td><?php echo $installation->author; ?></td>
        <td><?php echo $installation->copyright_info; ?></td>
        <td><?php echo $installation->last_import; ?></td>
        <td><?php echo approve_link($installation); ?></td>
                
        </tr>
        <?php endforeach; ?>
    	
    	</tbody>

    </table>
</div>
<?php foot(); ?>
