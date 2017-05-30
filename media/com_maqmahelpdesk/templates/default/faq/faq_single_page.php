<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_faq');?></h2>

    <p><?php echo JText::_('faq_header');?></p><?php

    if (count($categories)) :
        foreach ($categories as $row) :
            $articles = HelpdeskKB::getArticlesForCategory($row->id);
            if (count($articles)): ?>
                <h3><?php echo $row->name;?></h3>
                <div class="accordion" id="faq<?php echo $row->id;?>"><?php
                foreach ($articles as $article) : ?>
		                <div class="accordion-group">
			                <div class="accordion-heading">
				                <a class="accordion-toggle" data-toggle="collapse" data-parent="#faq<?php echo $row->id;?>" href="#collapsefaq-<?php echo $row->id . $article->id;?>">
					                <?php echo $article->title;?>
				                </a>
			                </div>
			                <div id="collapsefaq-<?php echo $row->id . $article->id;?>" class="accordion-body collapse" style="height: 0px; ">
				                <div class="accordion-inner">
					                <?php echo $article->content;?>
				                </div>
			                </div>
		                </div><?php
                endforeach; ?>
                </div><?php
            endif;
        endforeach;
    endif;?>

</div>