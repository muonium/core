var ExtIcons = (function() {
	// Private
	var ext = '';
	var pos = -1;
	var icon;

	// Types of files
	var types = {
		archive : ['zip', 'tar', 'gz', 'bz', 'bz2', 'xz', 'rar', 'jar', '7z', 'lzma'],
		code : ['php', 'html', 'htm', 'php3', 'php4', 'php5', 'java', 'css', 'scss', 'xml', 'svg', 'sql', 'c', 'cpp', 'cs', 'js', 'au3', 'asm', 'h',
			'ini', 'jav', 'p', 'pl', 'rb', 'sh', 'bat', 'py'],
		image : ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'tiff'],
		doc : ['docx', 'odt', 'doc', 'odp'],
		pdf : ['pdf'],
		sound : ['mp3', 'ogg', 'flac', 'wav', 'aac', 'm4a'],
		video : ['mp4', 'avi', 'wmv', 'mpeg', 'mov', 'mkv', 'mka', 'mks', 'flv']
	};

	var getIcon = function(filename) {
		icon = 'text';
		pos = filename.lastIndexOf('.');
		if(pos !== -1) {
			ext = filename.substr(pos+1);
			$.each(types, function(i,v) {
				if(v.indexOf(ext) !== -1) {
					icon = i;
					return false;
				}
			});
		}
		return icon;
	};

	// Public
	return {
		set : function(filename) {
			if(filename !== undefined) { // filename is specified when you want the icon for only one file (return the image)
				icon = getIcon(filename);
				return '<img src="'+IMG+'desktop/extensions/'+icon+'.svg" class="icon">';
			}
			else {
				$('.file').each(function() {
					filename = $(this).attr('data-title');
					icon = filename !== null && filename !== undefined ? getIcon(filename) : 'text';
					$(this).find('.file_body .icon').remove();
					$(this).find('.file_body').prepend('<img src="'+IMG+'desktop/extensions/'+icon+'.svg" class="icon">');
				});
			}
		}
	}
});
