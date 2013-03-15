<?php
queue_js_file('sites');
queue_css_file('sites');
$head = array('bodyclass' => 'sites primary',
              'title' => html_escape('Sites'));
echo head($head);

?>

<div id="primary">
    <?php echo flash(); ?>
    
    <ul class="quick-filter-wrapper">
        <li><a href="#" tabindex="0"><?php echo __('Quick Filter'); ?></a>
        <ul class="dropdown">
            <li><span class="quick-filter-heading"><?php echo __('Quick Filter') ?></span></li>
             <li><a href="<?php echo url('sites/index/browse'); ?>">All</a></li>
             <li><a href="<?php echo url('sites/index/browse?unapproved=true') ?>">Needs approval</a></li>       
            
            </ul>
        </li>
    </ul>    

    <table>
        <thead>
        <tr>
        <th>Title</th>
        <th>Description</th>
        <th>URL</th>
        <th>Admin Email</th>
        <th>Author</th>
        <th>Site Copyright</th>
        <th>Owner</th>
        <th>Last Import</th>
        <th>Approved</th>
        </tr>

        </thead>
        <tbody>
        <?php foreach(loop('sites') as $site): ?>
        <tr>
        <td><?php echo $site->title; ?></td>
        <td><?php echo $site->description; ?></td>
        <td><?php echo $site->url; ?></td>
        <td><?php echo $site->admin_email; ?></td>
        <td><?php echo $site->author; ?></td>
        <td><?php echo $site->copyright_info; ?></td>
        <td><?php echo $site->getOwner()->name; ?>
        <td><?php echo $site->last_import; ?></td>
        <td>
        <?php if(!$site->added): ?>
            <span class='approve' id='approve-<?php echo $site->id; ?>'>Approve</span>
        <?php else: ?>
            <?php echo $site->added; ?>
        <?php endif; ?>
        </td>
        </tr>
        <?php endforeach; ?>

        </tbody>

    </table>
</div>
<?php echo foot(); ?>
