<?php
queue_js('sites');
$head = array('bodyclass' => 'sites primary',
              'title' => html_escape('Installations'));
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
    <div id="browse-meta" class="group">
        <div id="browse-meta-lists">
            <ul id="items-sort" class="navigation">
                <li><strong>Quick Filter</strong></li>
            <?php
                echo nav(array(
                    'All' => uri('sites/index/browse'),
                    'Needs Approval' => uri('sites/index/browse?unapproved=true')
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
        <?php foreach($sites as $site): ?>
        <tr>
        <td><?php echo $site->title; ?></td>
        <td><?php echo $site->description; ?></td>
        <td><?php echo $site->url; ?></td>
        <td><?php echo $site->admin_email; ?></td>
        <td><?php echo $site->author; ?></td>
        <td><?php echo $site->copyright_info; ?></td>
        <td><?php echo $site->last_import; ?></td>
        <td><?php echo approve_link($site); ?></td>
                
        </tr>
        <?php endforeach; ?>
    	
    	</tbody>

    </table>
</div>
<?php foot(); ?>
