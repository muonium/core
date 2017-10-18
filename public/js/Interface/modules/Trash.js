var Trash = (function() {
	// If State = 0, set State to 1 and load contents from trash.
	// If State = 1, set State to 0 and load other contents.
	return {
		state : 0,
		switch : function() {
			Trash.state = Math.abs(Trash.state-1);
			if(Trash.state == 0) {
				$("#button_trash").html(txt.User.trash_0);
			} else {
				$("#button_trash").html(txt.User.trash_1);
			}
			Folders.open(0);
		}
	}
});
