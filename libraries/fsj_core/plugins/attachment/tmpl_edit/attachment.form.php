<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="row fileupload-buttonbar">
    <div class="col-lg-7">
        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn fileinput-button">
            <span>Upload Files</span>
            <input type="file" name="files[]" multiple>
        </span>
        <!-- The global file processing state -->
        <span class="progress-extended"></span>
		<div class="col-lg-5 fileupload-progress fade in" style="display: inline-block;">
        <div class="progress-extended">Max Size: <?php echo FSJ_Format::Size(FSJ_Helper::getMaximumFileUploadSize()); ?></div>
    </div>
    </div>
    <!-- The global progress state -->
</div>
	    
<input id="files_delete" type="hidden" name="files_delete" value="" />
		
<table role="presentation" class="table table-striped table-condensed table-valign-middle fsj_file_upload" style="width:750px;" id="attach_files">
	<tbody class="files">
		<?php foreach ($files as $file): ?>
			<?php $file->params = json_decode($file->params); ?>
			<?php $type = $file->dest; ?>
			<?php include JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'plugins'.DS.'attachment'.DS.'attachment.'.$type.DS.'tmpl_edit'.DS.$type.'.edit.php'; ?>
		<?php endforeach; ?>		
	</tbody>
</table>
		
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td width='48' style='text-align: center;'>
            <span class="preview"></span>
        </td>
        <td>
            <div class='name'>{%=file.name%}</div>
			<div class='size'>Processing...</div>
            <strong class="error text-danger"></strong>
        </td>
		<td width='150'>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
				<div class="bar bar-success" style="width:0%;"></div>
			</div>
		</td>
		<td width='150' style='text-align: right'>
            <button class="btn cancel">
                &times;
            </button>
        </td>
    </tr>
{% } %}
</script>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade" style='cursor: move'>
		<td width='48'>
            <span class="preview">
				<a href='{%=file.url%}' title='{%=file.url%}'>
					{% if (file.thumbnailUrl) { %}
						<img src="{%=file.thumbnailUrl%}" width='48' height='48'>
					{% } %}
				</a>
            </span>
        </td>
        <td>
			<div>
				<input type='text' name='new_filetitle[]' value='{%=file.name%}' class='input-xxlarge'>
				<input type='hidden' name='new_filename[]' value='{%=file.name%}'>
				<input type='hidden' name='new_fileorder[]' class='order' value=''>
			</div>
			<div style='padding: 3px 6px'>
				<span class="name">
					<a href='{%=file.url%}' title='{%=file.url%}'>
						{%=file.name%}
					</a>
				</span>, 
				<span class='size'>{%=o.formatFileSize(file.size)%}</span>
			</div>
			
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td colspan='2' style='text-align: right'>
            <button class="btn delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                &times;
            </button>
        </td>
    </tr>
{% } %}
</script>
