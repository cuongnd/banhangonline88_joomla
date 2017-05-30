<!--

When a person create a new article
- Creation stage
- Draft stage (active)
- Publish stage
- Unpublish stage

When a person opens a published article
- Creation stage
- Publish stage (active)
- Unpublish stage

When a person opens a published article and starts editing the article [JS switch]
- Creation stage
- Publish stage (from this)
- Published modification stage (to this - active)
- Unpublish stage

When a person opens a pending article
- Creation stage
- Pending stage (active)
- Publish stage
- Unpublish stage

When a person opens a pending article and starts editing the article [JS switch]
- Creation stage
- Pending stage (from this)
- Pending modification stage (to this - active)
- Publish stage
- Unpublish stage

When a person opens an unpublished article
- Creation stage
- Publish stage
- Unpublished stage (active)

-->



<style type="text/css">

.stages {
	width: 100%;
	border-collapse: collapse;
}

.stage,
#eblog-wrapper div#write_container td {
	vertical-align: top;
	white-space: nowrap;
	padding: 0 !important;
}


.stage-progress {
	height: 24px;

	position: relative;
	top: -24px;
	margin-bottom: -24px;

	/* Fill in border gaps */
	margin-left: -2px;
	left: 1px;

	/* For browser that don't support gradients, we'll set a blanket background colour */
	background-color: #abb2bc;

	/* Webkit background gradient */
	background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #b6bcc6), color-stop(1, #9da5b0));
	background: -moz-linear-gradient(#9da5b0 0%, #b6bcc6 100%);

	/* Give it the inset look by adding some shadows and highlights */
	-webkit-box-shadow: inset 0px 1px 2px 0px rgba(0, 0, 0, 0.5);
	-moz-box-shadow: inset 0px 1px 2px 0px rgba(0, 0, 0, 0.5);
	box-shadow: inset 0px 1px 2px 0px rgba(0, 0, 0, 0.5);
}

.stage-content td {
	padding: 5px !important;
}

.stage-progress td .progress-bar {
	height: 22px; /* 2px of 24px used by border */
	text-align: right;
}

.stage-progress td.stageComplete .progress-bar,
.stage-progress td.stageCurrent .progress-bar {
	/* Set the background size so the stripes work correctly */
	-webkit-background-size: 44px 44px; /* Webkit */

	/* For browser that don't support gradients, we'll set a blanket background colour */
	background-color: #74d04c;

	/* Webkit background stripes and gradient */
	background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #74d04c), color-stop(1, #9bdd62));
	background: -moz-linear-gradient(#9bdd62 0%, #74d04c 100%);

	/* Webkit embossing */
	-webkit-box-shadow: inset 0px 1px 0px 0px #dbf383, inset 0px -1px 1px #58c43a;
	-moz-box-shadow: inset 0px 1px 0px 0px #dbf383, inset 0px -1px 1px #58c43a;
	box-shadow: inset 0px 1px 0px 0px #dbf383, inset 0px -1px 1px #58c43a;

	/* Give it a higher contrast outline */
	border: 1px solid #4c8932;
	border-right: none;
}

.stage.active .stage-progress .progress-bar {
	border-right: 1px solid #4c8932;
}


.stage .stage-progress .caption {
	float: right;
	display: none;
}

.stage.active .stage-progress .caption {
	display: inline;
}





.stage-actions {
	line-height: 40px;
	vertical-align: middle;
	height: 40px;
}

.stage-actions td {
	visibility: hidden;
	background: #ccc;
}

.stage-actions td.stageCurrent {
	visibility: visible;
}


.stage-actions button {
	text-align: left;
	background: none;
	line-height: 40px;
	height: 40px;
	vertical-align: middle;

	border: none;
	cursor: pointer;
}
.stage-actions button span {
	display: block;
	font-size: 10px;
}


.stage-content {
	padding: 10px;
}
.stage-content h3 {
	font-size: 12px;
}
.stage-content h3 .past,
.stage-content h3 .future {
	display: none;
}



.published-modification-stage {
	display: none;
}
.published-modification-stage.stageCurrent {
	display: block;
}
</style>


<div class="pbl">
<table id="write-stages" width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table stages clearfix">
<thead class="stage-progress">
<tr>
	<!-- Creation stage -->
	<td class="creation-stage stageComplete">
		<div class="progress-bar"></div>
	</td>
	<!-- Creation stage -->

	<!-- Draft stage-->
	<?php if( $isDraft || !$isEdit ): ?>
	<td class="draft-stage stageCurrent">
		<div class="progress-bar"></div>
	</td>
	<?php endif; ?>
	<!-- Draft stage -->

	<?php if ( @$isPending ): ?>
	<!-- Pending stage -->

	<td class="pending-stage <?php if (!$isDraft) echo 'stageCurrent'; ?>">
		<div class="progress-bar"></div>
	</td>
	<!-- Pending stage -->

	<!-- Pending modification stage -->
	<td class="pending-modification-stage <?php if ($isDraft) echo 'stageCurrent'; ?>">
		<div class="progress-bar"></div>
	</td>
	<!-- Pending modification stage -->
	<?php endif; ?>

	<!-- Publish stage -->
	<td class="publish-stage <?php if($blog->published == 1 && !$isDraft && $isEdit) echo 'stageCurrent'; ?>">
		<div class="progress-bar"></div>
	</td>
	<!-- Publish stage -->

	<!-- Published modification stage -->
	<td class="published-modification-stage <?php if( $isDraft && $isEdit ) echo 'stageCurrent'; ?>">
		<div class="progress-bar"></div>
	</td>
	<!-- Published modification stage -->

	<!-- Unpublish stage -->
	<td class="unpublish-stage <?php if($blog->published == 0) echo 'stageCurrent'; ?>">
		<div class="progress-bar"></div>
	</td>
	<!-- Unpublish stage -->
</tr>
</thead>

<tbody class="stage-content">
<tr>

	<!-- Creation stage -->
	<td class="creation-stage stageComplete">
		<!-- <?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_CREATION_DATE'); ?> -->
		<h3>Created on</h3>
		<p>
			<?php
				jimport( 'joomla.utilities.date' );
				$date 	= EasyBlogDateHelper::getDate();
				$now 	= EasyBlogDateHelper::toFormat($date);

				if($blog->created != "")
				{
				    $newDate    = EasyBlogDateHelper::getDate($blog->created);
				    $now 		= EasyBlogDateHelper::toFormat($newDate);
				}
				echo EasyBlogHelper::dateTimePicker('created', $now, $now); 
			?>
		</p>
	</td>
	<!-- Creation stage -->

	<!-- Draft stage -->
	<?php if( $isDraft || !$isEdit ): ?>
	<td class="draft-stage stageCurrent">
		<h3>On draft</h3>
		<p><div id="draft_status"></div></p>
	</td>
	<?php endif; ?>
	<!-- Draft stage -->

	<?php if ( @$isPending ): ?>
	<!-- Pending stage -->
	<td class="pending-stage <?php if (!$isDraft) echo 'stageCurrent'; ?>">
		<h3>Pending approval</h3>
		<p>
			<?php if ( !empty( $this->acl->rules->manage_pending ) ): ?>	
			Submitted by %author% on %date%.
			<?php else: ?>
			This article submitted by %author-or-you% on %date% is pending approval.
			<?php endif; ?>
		</p>
	</td>
	<!-- Pending stage -->

	<!-- Pending modification stage -->
	<td class="pending-modification-stage <?php if ($isDraft) echo 'stageCurrent'; ?>">
		<h3>Modifiying pending article</h3>
		<p><div id="draft_status"></div></p>
	</td>
	<!-- Pending modification stage -->
	<?php endif; ?>	

	<!-- Publish stage -->
	<td class="publish-stage <?php if($blog->published == 1 && !$isDraft && $isEdit) echo 'stageCurrent'; ?>">
		<h3>
			<!-- <?php echo JText::_('COM_EASYBLOG_PUBLISHING_DATE'); ?> -->
			<span class="past">Published on</span>
			<span class="current">Publish on</span>
			<span class="future">To publish on</span>
		</h3>
		<p>
			<?php
				if($blog->publish_up != "")
				{
				    $newDate    = EasyBlogDateHelper::getDate($blog->publish_up);
					$now		= EasyBlogDateHelper::toFormat($newDate);
				}
				else {
					$now 		= EasyBlogDateHelper::toFormat($date);
				}

				echo EasyBlogHelper::dateTimePicker('publish_up', $blog->publish_up != '' ? $now : JText::_('COM_EASYBLOG_IMMEDIATELY'), $now); 
			?>
		</p>
	</td>
	<!-- Publish stage -->

	<!-- Published modification stage -->
	<td class="published-modification-stage <?php if( $isDraft && $isEdit ) echo 'stageCurrent'; ?>">	
		<h3>Modifying published article</h3>
		<p><div id="draft_status"></div></p>
	</td>
	<!-- Published modification stage -->

	<!-- Unpublish stage -->
	<td class="unpublish-stage <?php if($blog->published == 0) echo 'stageCurrent'; ?>">
		<h3>
			<!-- <?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_UNPUBLISH_DATE'); ?> -->
			<span class="past">Unpublished on</span>
			<span class="current">Unpublish on</span>
			<span class="future">To unpublish on</span>
		</h3>
		<p>
			<?php
				$notEmpty = true;
				if ( $blog->publish_down == "0000-00-00 00:00:00" || empty( $blog->id ) || empty($blog->publish_down))
				{
					$now 			= '';
					$notEmpty 		= false;
				}
				else {
					$newDate    = EasyBlogDateHelper::getDate($blog->publish_down);
					$now		= EasyBlogDateHelper::toFormat($newDate);
					$notEmpty 	= true;
				}
				
				echo EasyBlogHelper::dateTimePicker('publish_down', $notEmpty ? $now : JText::_('COM_EASYBLOG_NEVER'), $now, true);
			?>
		</p>
	</td>
	<!-- Unpublish stage -->

</tr>
</tbody>


<tfoot class="stage-actions">
<tr>
	<!-- Creation stage -->
	<td class="creation-stage stageComplete">
		<button class="">Delete post</button>
	</td>
	<!-- Creation stage -->

	<!-- Draft stage-->
	<?php if( $isDraft || !$isEdit ): ?>
	<td class="draft-stage stageCurrent">
		<button class="">Discard draft</button>

		<?php if ( empty($this->acl->rules->publish_entry) ): ?>
		<button class="">Submit for approval</button>
		<?php else: ?>
		<button class="">Publish now <span>or <a href="">schedule for later</a></span></button>
		<?php endif; ?>	
	</td>
	<?php endif; ?>
	<!-- Draft stage -->

	<?php if ( @$isPending ): ?>
	<!-- Pending stage -->
	<td class="pending-stage <?php if (!$isDraft) echo 'stageCurrent'; ?>">
		<?php if ( empty($this->acl->rules->publish_entry) ): ?>
		<button class="">Revert to draft</button>
		<?php endif; ?>

		<?php if ( !empty( $this->acl->rules->manage_pending ) ): ?>
		<button class="">Approve &amp; publish now <span>or <a href="">schedule for later</a></span></button>
		<?php endif; ?>
	</td>
	<!-- Pending stage -->

	<!-- Pending modification stage -->
	<td class="pending-modification-stage <?php if ($isDraft) echo 'stageCurrent'; ?>">
		<button class="">Discard changes</button>
		<button class="">Update changes</button>
	</td>
	<!-- Pending modification stage -->
	<?php endif; ?>	

	<!-- Publish stage -->
	<td class="publish-stage <?php if($blog->published == 1 && !$isDraft && $isEdit) echo 'stageCurrent'; ?>">
		<button class="float-l">Revert to draft</button>
		<button class="float-r">Unpublish now <span>or <a href="">set unpublish date</a></span></button>
	</td>
	<!-- Publish stage -->

	<!-- Published modification stage -->
	<td class="published-modification-stage <?php if( $isDraft && $isEdit ) echo 'stageCurrent'; ?>">
		<button class="">Discard changes</button>
		<button class="">Update changes <!--<span>or <a href="">publish as new</a></span>--></button>
	</td>
	<!-- Published modification stage -->

	<!-- Unpublish stage -->
	<td class="unpublish-stage <?php if($blog->published == 0) echo 'stageCurrent'; ?>">
		<?php if($blog->published == 0): ?>
		<button class="">Republish article</button>
		<?php endif; ?>
	</td>
	<!-- Unpublish stage -->
</tr>
</tfoot>


</table>

</div>