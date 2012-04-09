<?php
head();

?>
<?php echo flash(); ?>
<div id="primary">
<?php if(!$this->error): ?>
<p>Your API key is:<br/><br/> <?php echo $site->key; ?></p>

<p>The next step is to copy and paste it into your Commons configuration page:
<?php $link = $site->url . "/admin/plugins/config?name=Commons"; ?>
<a target="_blank" href="<?php echo $link; ?>"><?php echo $link; ?></a>
</p>

<p>After you have done that, you can start applying licensing information to items and collections
and sending them on to the Commons. Be sure to read the Terms of Service so you know exactly how we will use
information about your site to help others discover your materials.
</div>

<?php endif; ?>

<?php foot(); ?>