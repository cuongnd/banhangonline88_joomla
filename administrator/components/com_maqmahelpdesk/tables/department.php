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

class MaQmaHelpdeskTableDepartment extends JTable
{
	var $id = null;
	var $wkdesc = null;
	var $logo = null;
	var $wkabout = null;
	var $wkkb = 0;
	var $wkemail = 0;
	var $wkticket = 0;
	var $show = 0;
	var $wkmail_address = null;
	var $wkmail_address_name = null;
	var $wkadmin_email = null;
	var $auto_assign = 0;
	var $trouble = 0;
	var $contract = 0;
	var $ordering = null;
	var $use_activity = 0;
	var $lim_actmsgs = 0;
	var $lim_actmsgs_chars = 300;
	var $lim_actmsgs_lines = 10;
	var $lim_actwords = 0;
	var $lim_actwords_chars = 0;
	var $hyper_links = 0;
	var $theme = 'default';
	var $tkt_crt_nfy_mgr = 0;
	var $tkt_crt_nfy_admin = 0;
	var $tkt_asgn_old_asgn = 0;
	var $tkt_asgn_new_asgn = 0;
	var $tkt_asgn_nfy_usr_one = 0;
	var $wkfaq = 0;
	var $wkdownloads = 0;
	var $wkglossary = 0;
	var $wkannounces = 0;
	var $id_priority = 0;
	var $enable_discussions = 0;
	var $contract_total_disable = 0;
	var $use_account = 1;
	var $use_bookmarks = 1;
	var $add_mail_tag = 1;
	var $digistore = 0;
	var $shortdesc = null;
	var $slug = null;
	var $bugtracker = 0;
	var $id_group = 0;
	var $support_only = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_workgroup', 'id', $_db);
	}
}