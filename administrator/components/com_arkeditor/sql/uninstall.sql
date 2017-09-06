DROP TABLE IF EXISTS #__ark_editor_plugins;
DROP TABLE IF EXISTS #__ark_editor_toolbars;
DROP TABLE IF EXISTS #__ark_editor_languages;
DROP TABLE IF EXISTS #__ark_editor_inline_views;

DELETE FROM #__ucm_history WHERE EXISTS (SELECT 1 FROM #__content_types WHERE type_alias = 'com_modules.custom' AND #__content_types.type_id = #__ucm_history.ucm_type_id);
DELETE FROM #__content_types WHERE type_alias = 'com_modules.custom';


