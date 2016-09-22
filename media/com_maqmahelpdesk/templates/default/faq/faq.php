<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_faq');?></h2>

    <p><?php echo JText::_('faq_header');?></p>

    <h3><?php echo JText::_('dl_category');?>: <?php echo $category_name;?></h3>
    <?php if (count($categories)) : ?>
        <?php foreach ($categories as $row) : ?>
            <h4><a href="<?php echo $row->link;?>"><?php echo $row->name;?></a> <?php echo ($row->articles ? '(' . $row->articles . ')' : '');?></h4>
        <?php endforeach; ?>
    <?php endif;?>

    <?php if (count($articles)): ?>
        <div class="accordion" id="faqaccordion" style="margin-top:15px;">
        <?php foreach ($articles_rows as $article) : ?>
            <div class="accordion-group">
	            <div class="accordion-heading">
		            <a class="accordion-toggle" data-toggle="collapse" data-parent="#faqaccordion" href="#collapsefaq-<?php echo $article['id'];?>">
			            <?php echo $article['title'];?>
		            </a>
	            </div>
	            <div id="collapsefaq-<?php echo $article['id'];?>" class="accordion-body collapse" style="height: 0px; ">
		            <div class="accordion-inner">
			            <?php echo $article['content'];?>
		            </div>
	            </div>
            </div>
        <?php endforeach; ?>
		</div>
    <?php endif;?>

	<?php if ($parent) : ?>
	<p class="tar"><a href="javascript:window.history.go(-1);" class="btn"><i class="ico-chevron-left"></i> <?php echo JText::_('troubleshooter_back');?></a></p>
	<?php endif;?>

</div>