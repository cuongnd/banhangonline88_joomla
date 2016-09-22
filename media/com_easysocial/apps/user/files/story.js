EasySocial.require()
	.script("story/files")
	.done(function($){

		var plugin =
			story.addPlugin("files", {
				settings: {
					url: "<?php echo FRoute::raw('index.php?option=com_easysocial&controller=explorer&task=hook&hook=addFile&uid=' . $this->my->id . '&type=' . SOCIAL_TYPE_USER . '&format=json&tmpl=component&createStream=0&' . FD::token() . '=1' ); ?>",
					max_file_size: "<?php echo $maxFileSize; ?>",
					filters: [{extensions: "<?php echo $allowedExtensions;?>"}]
					// filters: {
					//   mime_types : [
					//     { title : "Zip files", extensions : "zip" }
					//   ]
					// }
				}
			});
	});
