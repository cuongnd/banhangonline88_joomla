<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/kb.php');

$page1 = '';
$id = JRequest::getVar('id', 0, '', 'int');

// Get article
$database->setQuery("SELECT k.id, k.kbcode, k.kbtitle, k.content, k.keywords, k.views, k.date_created, k.date_updated, u.name FROM #__support_kb as k, #__users as u WHERE u.id=k.id_user AND k.publish='1' AND k.id='" . $id . "'");
$article = null;
$article = $database->loadObject();

// Get article attachments
$database->setQuery("SELECT filename, description FROM #__support_file WHERE id='" . $article->id . "' AND source='K' AND public='1'");
$attachs = $database->loadObjectList();

// Get article comments
$database->setQuery("SELECT c.`date`, u.name, c.comment FROM #__support_kb_comment as c, #__users as u WHERE u.id=c.id_user AND c.id_kb='" . $article->id . "'");
$comments = $database->loadObjectList();

// PDF - CSS
$lang = JFactory::getLanguage();
if ($lang->isRTL()) {
	$css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf_rtl.css');
} else {
	$css = file_get_contents(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/pdf.css');
}

// PDF - Header
$page1 = '<h1 style="font-family:DejaVuSans;">' . $article->kbtitle . '</h1>
<table width="100%" border="0">
<tr>
	<td class="header">' . JText::_('code') . '</td>
	<td>' . $article->kbcode . '</td>
	<td class="header">' . JText::_('author') . '</td>
	<td>' . $article->name . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('created_date') . '</td>
	<td>' . $article->date_created . '</td>
	<td class="header">' . JText::_('last_update') . '</td>
	<td>' . $article->date_updated . '</td>
</tr>
<tr>
	<td class="header">' . JText::_('rating') . '</td>
	<td><img src="media/com_maqmahelpdesk/images/rating/' . HelpdeskForm::GetRate($article->id, 'K', 2) . 'star_pdf.png" /></td>
	<td class="header">' . JText::_('votes') . '</td>
	<td>' . HelpdeskKB::GetVotes($article->id, 'K') . '</td>
</tr>
</table>
<p></p>';

// PDF - Content
$page2 = '<div>' . $article->content . '</div>';

// PDF - Attachments
$page3 = '<h2>' . JText::_('attachments') . '</h2>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header bb">' . JText::_('filename') . '</td>
	<td class="header bb">' . JText::_('description') . '</td>
</tr>
</thead>
<tbody>';
for ($i = 0; $i < count($attachs); $i++) {
	$attach = $attachs[$i];
	$page3 .= '<tr>
		<td>' . $attach->filename . '</td>
		<td>' . $attach->description . '</td>
	</tr>';
}
$page3 .= '</tbody>
</table>';

// PDF - Comments
$page4 = '<h2>' . JText::_('comments') . '</h2>
<table width="100%" border="0">
<thead>
<tr>
	<td class="header bb">' . JText::_('date') . '</td>
	<td class="header bb">' . JText::_('user') . '</td>
	<td class="header bb">' . JText::_('comment') . '</td>
</tr>
</thead>
<tbody>';
for ($i = 0; $i < count($comments); $i++) {
	$comment = $comments[$i];
	$page4 .= '<tr>
		<td>' . $comment->date . '</td>
		<td>' . $comment->name . '</td>
		<td>' . $comment->comment . '</td>
	</tr>';
}
$page4 .= '</tbody>
</table>';

// RTL Check
$lg = ($lang->isRTL() ? 'ar' : 'UTF-8');
$mpdf = new mPDF($lg);

$mpdf->SetAutoFont(AUTOFONT_ALL);

if ($lang->isRTL()) {
	$mpdf->SetDirectionality('rtl');
}

// Add stylesheet
$mpdf->WriteHTML($css, 1);

// Add general details and content
$mpdf->WriteHTML($page1);
$mpdf->WriteHTML($page2);

// Add attachments
if (count($attachs)) {
	$mpdf->AddPage();
	$mpdf->WriteHTML($page3);
}

// Add Comments
if (count($comments)) {
	$mpdf->AddPage();
	$mpdf->WriteHTML($page4);
}

// Outputs PDF
$mpdf->Output(null, 'D');
exit;