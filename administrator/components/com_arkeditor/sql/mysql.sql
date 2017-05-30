--
-- Table structure for table `#__ark_editor_plugins`
--

CREATE TABLE  IF NOT EXISTS `#__ark_editor_plugins`(
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL default 'command',
  `row` tinyint NOT NULL default '0',
  `icon` varchar(255) NOT NULL default '',
  `published` tinyint(3) NOT NULL default '0',
  `editable` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `iscore` tinyint(3) NOT NULL default '0',
  `acl` text NULL,
  `params` text NOT NULL,
  `parentid` int(11) NULL, 
  PRIMARY KEY  (`id`),
  UNIQUE KEY `plugin` (`name`)
) ENGINE=InnoDB;

CREATE TABLE  IF NOT EXISTS `#__update_ark_editor_plugins` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL default 'command',
  `row` tinyint NOT NULL default '0',
  `icon` varchar(255) NOT NULL default '',
  `published` tinyint(3) NOT NULL default '0',
  `editable` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `iscore` tinyint(3) NOT NULL default '0',
  `acl` text NULL,
  `params` text NOT NULL,
  `parentid` int(11) NULL, 
  PRIMARY KEY  (`id`),
  UNIQUE KEY `plugin` (`name`)
) ENGINE=InnoDB;

insert into #__update_ark_editor_plugins (`id`, `title`,`name`,`type`,`row`,`published`,`editable`,`icon`,`iscore`,`params`)
select `id`, `title`,`name`,`type`,`row`,`published`,`editable`,`icon`,`iscore`,`params` from `#__ark_editor_plugins`;

DROP TABLE #__ark_editor_plugins;

ALTER table #__update_ark_editor_plugins
RENAME TO `#__ark_editor_plugins`;

INSERT INTO `#__ark_editor_plugins`(`id`, `title`,`name`,`type`,`row`,`published`,`editable`,`icon`,`iscore`,`params`, `parentid`)
VALUES (1,'Scayt','scayt','plugin',1,1,1,'-1872',1,'',82), 
(2,'','sourcearea','plugin',0,1,1,'',1,'',NULL),
(3,'Source','source','command',1,1,1,'-1824',1,'',2),
(4,'Preview','preview','plugin',1,1,1,'-1632',1,'',NULL),
(5,'Cut','cut','command',1,1,1,'-312',1,'',60),
(6,'Copy','copy','command',1,1,1,'-264',1,'',60),
(7,'Paste','paste','command',1,1,1,'-360',1,'',60),
(8,'PasteText','pastetext','plugin',1,1,1,'-1536',1,'',NULL),
(9,'Find','find','plugin',1,1,1,'-528',1,'',NULL),
(10,'Replace','replace','command',1,1,1,'-552',1,'',9),
(11,'SelectAll','selectall','command',1,1,1,'-1728',1,'',61),
(12,'RemoveFormat','removeformat','plugin',1,1,1,'-1680',1,'',NULL),
(13,'Bold','bold','command',2,1,1,'-24',1,'',58),
(14,'Italic','italic','command',2,1,1,'-48',1,'',58),
(15,'Strike','strike','command',2,1,1,'-72',1,'',58),
(16,'Subscript','subscript','command',2,1,1,'-96',1,'',58),
(17,'Superscript','superscript','command',2,1,1,'-120',1,'',58),
(18,'Underline','underline','command',2,1,1,'-144',1,'',58),
(19,'Smiley','smiley','plugin',2,1,1,'-1056',1,'',NULL),
(20,'Link','link','plugin',2,1,1,'-1248',1,'',NULL),
(21,'Image','image','plugin',2,1,1,'-936',1,'',NULL),
(22,'Flash','flash','plugin',2,1,1,'-576',1,'',NULL),
(23,'SpecialChar','specialchar','plugin',2,1,1,'-1848',1,'',NULL),
(24,'PageBreak','pagebreak','plugin',2,1,1,'-1488',1,'',NULL), 
(25,'SpellChecker','checkspell','command',1,1,1,'-2016',1,'',82),
(26,'','tableresize','plugin',2,1,1,'',1,'',82),
(27,'','tabletools','plugin',0,1,1,'',1,'',82),
(28,'TextColor','textcolor','command',3,1,1,'-408',1,'',62),
(29,'BGColor','bgcolor','command',3,1,1,'-384',1,'',62),
(30,'Form','form','command',1,0,1,'-648',1,'',75),
(31,'Radio','radio','command',1,0,1,'-720',1,'',75),
(32,'TextField','textfield','command',1,0,1,'-864',1,'',75),
(33,'Textarea','textarea','command',1,0,1,'-816',1,'',75),
(34,'ShowBlocks','showblocks','plugin',3,1,1,'-1776',1,'',NULL),
(35,'Select','select','command',1,0,1,'-768',1,'',75),
(36,'ImageButton','imagebutton','command',1,0,1,'-696',1,'',75),
(37,'HiddenField','hiddenfield','command',1,0,1,'-672',1,'',75),
(38,'Checkbox','checkbox','command',1,0,1,'-624',1,'',75),
(39,'Button','formbutton','command',1,0,1,'-600',1,'',75),
(40,'NumberedList','numberedlist','command',2,1,1,'-1368',1,'',90),
(41,'BulletedList','bulletedlist','command',2,1,1,'-1320',1,'',90),
(42,'Indent','indent','plugin',2,1,1,'-984',1,'',NULL),
(43,'Outdent','outdent','command',2,1,1,'-1032',1,'',42),
(44,'JustifyLeft','justifyleft','command',2,1,1,'-1128',1,'',55),
(45,'JustifyCenter','justifycenter','command',2,1,1,'-1104',1,'',55),
(46,'JustifyBlock','justifyblock','command',2,1,1,'-1080',1,'',55),
(47,'JustifyRight','justifyright','command',2,1,1,'-1152',1,'',55),
(48,'Blockquote','blockquote','plugin',2,1,1,'-216',1,'',NULL),
(49,'About','about','plugin',3,1,1,'0',1,'',NULL),
(50,'Maximize','maximize','plugin',3,1,1,'-1392',1,'',NULL),
(51,'','div','plugin',0,1,1,'',1,'',NULL),
(52,'CreateDiv','creatediv','command',2,1,1,'-480',1,'',51),
(53,'','editdiv','command',0,1,1,'',1,'',51),
(54,'','removediv','command',0,1,1,'',1,'',51),
(55,'','justify','plugin',0,1,1,'',1,'',NULL),
(56,'','a11yhelp','plugin',0,1,1,'',1,'',NULL),
(58,'','basicstyles','plugin',0,1,1,'',1,'',NULL),
(59,'Table','table','plugin',2,1,1,'-1896',1,'',NULL),
(60,'','clipboard','plugin',0,1,1,'',1,'',NULL),
(61,'','selection','plugin',0,1,1,'',1,'',NULL),
(62,'','colorbutton','plugin',0,1,1,'',1,'',NULL),
(63,'Unlink','unlink','command',2,1,1,'-1272',1,'',20),
(64,'Anchor','anchor','command',2,1,1,'-1224',1,'',20),
(65,'','contextmenu','plugin',0,1,1,'',1,'',NULL),
(66,'','editingblock','plugin',0,1,1,'',1,'',NULL),
(67,'','elementspath','plugin',0,1,1,'',1,'',NULL),
(68,'','enterkey','plugin',0,1,1,'',1,'',NULL),
(69,'','entities','plugin',0,1,1,'',1,'',NULL),
(70,'','toolbar','plugin',0,1,1,'',1,'',NULL),
(71,'','filebrowser','plugin',0,1,1,'',1,'',NULL),
(72,'Styles','stylescombo','plugin',3,1,1,'',1,'',NULL),
(73,'Font','font','plugin',3,1,1,'',1,'',NULL),
(74,'Format','format','plugin',3,1,1,'',1,'',NULL),
(75,'','forms','plugin',0,1,1,'',1,'',NULL),
(76,'Undo','undo','plugin',1,1,1,'-1992',1,'',NULL),
(77,'Redo','redo','command',1,1,1,'-1944',1,'',76),
(78,'Templates','templates','plugin',1,1,1,'-456',1,'',NULL),
(79,'PasteFromWord','pastefromword','plugin',1,1,1,'-1584',1,'',NULL),
(80,'HorizontalRule','horizontalrule','plugin',2,1,1,'-888',1,'',NULL),
(81,'Print','print','plugin',1,1,1,'-1656',1,'',NULL),
(82,'','wsc','plugin',0,1,1,'',1,'',NULL),
(83,'','showborders','plugin',0,1,1,'',1,'',NULL),
(84,'','tab','plugin',0,1,1,'',1,'',NULL),
(85,'','resize','plugin',0,1,1,'',1,'',NULL),
(86,'','wysiwygarea','plugin',0,1,1,'',1,'',NULL),
(87,'','list','plugin',0,1,1,'',1,'',NULL),
(88,'FontSize','fontsize','command',3,1,1,'',1,'',73),
(89,'','bidi','plugin',0,1,1,'',1,'',NULL),
(90,'BidiLtr','bidiltr','command',2,1,1,'-168',1,'',89),
(91,'BidiRtl','bidirtl','command',2,1,1,'-192',1,'',89),
(92,'Iframe','iframe','plugin',2,1,1,'-912',1,'',NULL)

ON DUPLICATE KEY UPDATE `name`= VALUES(`name`),`title`= VALUES(`title`),`row` = VALUES(`row`), `icon` = VALUES(`icon`), `parentid` = VALUES(`parentid`), `type` = VALUES(`type`);



CREATE TABLE IF NOT EXISTS `#__ark_editor_toolbars` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL,
  `published` tinyint(3) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `iscore` tinyint(3) NOT NULL default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `toolbar` (`name`)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `#__ark_editor_inline_views` (
	  `element` varchar(50) NOT NULL,
	  `views`  varchar(500) NOT NULL default '[]',
	  `context`  varchar(50) NOT NULL,
	  `types`  varchar(500) NOT NULL default '[]',
	  `params` text NOT NULL,
	  `parent` varchar(50) NULL,
	   PRIMARY KEY  (`element`,`context`)
) ENGINE=InnoDB;

INSERT INTO `#__ark_editor_inline_views` (`element`,`views`,`context`,`types`,`params`) VALUES
('com_content','["featured","article","category","categories"]','article','["article","featured","category","blog"]','{}')
ON DUPLICATE KEY UPDATE `views`= VALUES(`views`),`context` = VALUES(`context`),`types` = VALUES(`types`),`params` = VALUES(`params`);


INSERT INTO `#__ark_editor_toolbars` (`id`,`title`,`name`,`published`,`checked_out`,`checked_out_time`,`iscore`,`params`) VALUES
(1,'Back','back',1,0,'0000-00-00 00:00:00',1,'{"show_new":"0","show_save":"0","components":[]}'),
(2,'Front','front',1,0,'0000-00-00 00:00:00',1,'{"show_versions":"0","show_new":"0","show_save":"0","show_undo":"0","show_redo":"0","show_find":"0","show_close":"0","show_source":"0","show_design":"0","show_maximum":"0","components":[]}'),
(3,'Inline','inline',1,0,'0000-00-00 00:00:00',1,'{"show_versions":"0","show_new":"0","show_save":"0","show_undo":"0","show_redo":"0","show_find":"0","show_close":"0","show_source":"0","show_design":"0","show_maximum":"0","components":[]}'),
(4,'Title','title',1,0,'0000-00-00 00:00:00',1,'{"show_versions":"0","show_new":"0","show_save":"0","show_undo":"0","show_redo":"0","show_find":"0","show_close":"0","show_source":"0","show_design":"0","show_maximum":"0","components":[]}'),
(5,'Mobile','mobile',1,0,'0000-00-00 00:00:00',1,'{"show_versions":"0","show_new":"0","show_save":"0","show_undo":"0","show_redo":"0","show_find":"0","show_close":"0","show_source":"0","show_design":"0","show_maximum":"0","components":[]}'),
(6,'Image','image',1,0,'0000-00-00 00:00:00',1,'{"show_versions":"0","show_new":"0","show_save":"0","show_undo":"0","show_redo":"0","show_find":"0","show_close":"0","show_source":"0","show_design":"0","show_maximum":"0","components":[]}')
ON DUPLICATE KEY UPDATE `name`= VALUES(`name`);


CREATE TABLE IF NOT EXISTS `#__ark_editor_languages` (
	  `id` int(11) NOT NULL auto_increment,
	   tag  varchar(5),
	   filename varchar (100),
	   PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

-- UCM Stuff
INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `router`, `content_history_options`) 
SELECT * FROM 
(
	SELECT 'Html Module' AS `type_title`,'com_modules.custom' AS `type_alias`,'{"special":{"dbtable":"#__modules","key":"id","type":"Module","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}' AS `table`,'' AS `rules`,'{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"null","core_created_time":"null","core_modified_time":"null","core_body":"content", "core_hits":"null","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"null", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"null", "core_ordering":"ordering", "core_metakey":"null", "core_metadesc":"null", "core_catid":"null", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"note":"note", "position":"position", "position":"position", "module":"module","showtitle":"showtitle", "client_id":"client_id"}}' AS `field_mappings`,'' AS `router`,'{"formFile":"administrator\\/components\\/com_modules\\/models\\/forms\\/module.xml", "hideFields":["asset_id","checked_out","checked_out_time","client_id"],"ignoreChanges":["checked_out", "checked_out_time"],"convertToInt":["publish_up", "publish_down", "showtitle", "ordering"] ,"displayLookup":[{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"} ]}' AS `content_history_option`
) AS tmp 
WHERE NOT EXISTS(SELECT 1 FROM #__content_types WHERE `type_alias` = 'com_modules.custom');
