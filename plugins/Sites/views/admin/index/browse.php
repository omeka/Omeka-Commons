<?php
queue_js_file('sites');
queue_css_file('sites');
$head = array('bodyclass' => 'sites primary',
              'title' => html_escape('Sites'));
echo head($head);

?>

<div id="primary">
    <div class="pagination"><?php echo pagination_links(); ?></div>
    
    <ul class="quick-filter-wrapper">
        <li><a href="#" tabindex="0">Filter Approved</a>
        <ul class="dropdown">
            <li><span class="quick-filter-heading">Filter Approved</span></li>
             <li><a href="<?php echo url('sites'); ?>">All</a></li>
             <li><a href="<?php echo url('sites?approved=true') ?>">Approved</a></li>
             <li><a href="<?php echo url('sites?approved=false') ?>">Needs approval</a></li>       
            </ul>
        </li>
    </ul>    

<div id='sites-search' style='clear:both'>
    
<form method="get">
Search by:
<label for='title'>Title</label><input size='10' type='text' name='title'/>
<label for='affiliation'>Affiliation</label><input size='10' type='text' name='affiliation'/>
<label for='admin_name'>Admin Name</label><input size='10' type='text' name='admin_name'/>
<button>Submit</button>
</form>
</div>
    <table>
        <thead>
        <tr>
        <th class="batch-edit-heading">Approve?</th>
        <?php
        $browseHeadings[__('Site')] = 'title';
        $browseHeadings[__('Affiliation')] = 'affiliation';
        $browseHeadings[__('Admin Name')] = 'admin_name';
        $browseHeadings[__('Last Import')] = 'last_import';
        $browseHeadings[__('Approved')] = 'date_approved';
        echo browse_sort_links($browseHeadings, array('link_tag' => 'th scope="col"', 'list_tag' => '')); 
        ?>        

        </tr>

        </thead>
        <tbody>
        <?php foreach(loop('sites') as $site): ?>
        <tr>
        <td class="batch-edit-check" scope="row">
            <?php if(!$site->date_approved):?>
            <input type="checkbox" name="sites[]" value="<?php echo $site->id; ?>" />
            <?php endif;?>
        </td>
        <td>
            <span class='title'><a href='<?php echo $site->url; ?>'><?php echo $site->title; ?></a></span>
            <?php $action = $site->site_aggregation_id ? 'edit' : 'add' ;?>
            <ul class='action-links group'>
            <li><a href="<?php echo url("/sites/site-aggregation/$action/id/" . $site->site_aggregation_id); ?>">Aggregation</a></li>
            </ul>
            
        </td>
        <td><?php echo $site->affiliation; ?></td>
        <td>
            <?php echo $site->admin_name; ?>
            <ul class='action-links group'>
                <li class='details-link'><a href='mailto: <?php echo $site->admin_email; ?>'>Email admin</a></li>
            </ul>  
        </td>
        <td><?php echo $site->last_import; ?></td>
        <td>
        <?php if(!$site->date_approved): ?>
            <span class='approve' id='approve-<?php echo $site->id; ?>'>Approve</span>
        <?php else: ?>
            <?php echo $site->date_approved; ?>
        <?php endif; ?>
        </td>
        </tr>
        <?php endforeach; ?>

        </tbody>

    </table>
    <div class="pagination"><?php echo pagination_links(); ?></div>
</div>
<?php echo foot(); ?>
