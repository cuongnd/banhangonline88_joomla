<h1 style="font-family:DejaVuSans;">{title}</h1>
<table width="100%" border="0">
    <tr>
        <td class="header"><?php echo JText::_('code');?></td>
        <td>{code}</td>
        <td class="header"><?php echo JText::_('author');?></td>
        <td>{author}</td>
    </tr>
    <tr>
        <td class="header"><?php echo JText::_('created_date');?></td>
        <td>{date_created}</td>
        <td class="header"><?php echo JText::_('last_update');?></td>
        <td>{date_updated}</td>
    </tr>
    <tr>
        <td class="header"><?php echo JText::_('rating');?></td>
        <td><img src="media/com_maqmahelpdesk/images/rating/{rating}star_pdf.png"/></td>
        <td class="header"><?php echo JText::_('votes');?></td>
        <td>{votes}</td>
    </tr>
</table>
<p></p>

<div>{content}</div>

<pagebreak/>
<h2><?php echo JText::_('attachments');?></h2>
<table width="100%" border="0">
    <thead>
    <tr>
        <td class="header bb"><?php echo JText::_('filename');?></td>
        <td class="header bb"><?php echo JText::_('description');?></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{filename}</td>
        <td>{description}</td>
    </tr>
    </tbody>
</table>

<pagebreak/>
<h2><?php echo JText::_('comments');?></h2>
<table width="100%" border="0">
    <thead>
    <tr>
        <td class="header bb"><?php echo JText::_('date');?></td>
        <td class="header bb"><?php echo JText::_('user');?></td>
        <td class="header bb"><?php echo JText::_('comment');?></td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{date}</td>
        <td>{name}</td>
        <td>{comment}</td>
    </tr>
    </tbody>
</table>
