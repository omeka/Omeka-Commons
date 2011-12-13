<?php
head();

?>

<div id="primary">

<p>Your API key is:<br/><br/> <?php echo $installation->key; ?></p>

<p>The next step is to copy and paste it into your Commons configuration page:
<?php $link = $installation->url . "/admin/plugins/config?name=Commons"; ?>
<a target="_blank" href="<?php echo $link; ?>"><?php echo $link; ?></a>
</p>

<p>After you have done that, you can start applying licensing information to items and collections
and sending them on to the Commons. Be sure to read the Terms of Service so you know exactly how we will use
information about your site to help others discover your materials.
</div>



<?php foot(); ?>