var ExtIcons = (function() {
	// Private
	var ext = '';
	var pos = -1;
	var icon;

	// Types of files
	var archive = ['zip', 'tar', 'gz', 'bz', 'bz2', 'xz', 'rar', 'jar', '7z'];
	var code = ['php', 'html', 'htm', 'php3', 'php4', 'php5', 'java', 'css', 'scss', 'xml', 'svg', 'sql', 'c', 'cpp', 'cs', 'js', 'au3', 'asm', 'h', 'ini', 'jav', 'p', 'pl', 'rb', 'sh', 'bat', 'py'];
	var image = ['jpg', 'jpeg', 'png', 'bmp', 'gif'];
	var doc = ['docx', 'odt', 'doc', 'odp'];
	var pdf = ['pdf'];
	var sound = ['mp3', 'ogg', 'flac', 'wav', 'aac', 'm4a'];
	var video = ['mp4', 'avi', 'wmv', 'mpeg', 'mov', 'mkv', 'mka', 'mks', 'flv'];

	// Public
	return {
		set : function() {
			var dir_files = document.querySelectorAll(".file");
			for(var i = 0; i < dir_files.length; i++) {
				icon = 'text';
				pos = dir_files[i].getAttribute('data-title').lastIndexOf('.');
				if(pos !== -1) {
					ext = dir_files[i].getAttribute('data-title').substr(pos+1);
					if(archive.indexOf(ext) !== -1)
				    	icon = 'archive';
				    else if(code.indexOf(ext) !== -1)
				    	icon = 'code';
				    else if(image.indexOf(ext) !== -1)
				    	icon = 'image';
				    else if(doc.indexOf(ext) !== -1)
				    	icon = 'doc';
				    else if(pdf.indexOf(ext) !== -1)
				    	icon = 'pdf';
				    else if(sound.indexOf(ext) !== -1)
				    	icon = 'sound';
				    else if(video.indexOf(ext) !== -1)
				    	icon = 'video';
				}
				dir_files[i].innerHTML = '<img src="'+img+'desktop/extensions/'+icon+'.svg" class="icon"> '+dir_files[i].innerHTML;
			}
		}
	}
});
