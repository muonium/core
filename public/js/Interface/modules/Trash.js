var Trash = (function() {
	// Private

	// Public
	// If State = 0, set State to 1 and load contents from trash.
	// If State = 1, set State to 0 and load other contents.
	return {
		State : 0,
		switch : function() {
			Trash.State = Math.abs(Trash.State-1);
			if(Trash.State == 0)
				document.querySelector("#button_trash").innerHTML = txt.User.trash_0;
			else
				document.querySelector("#button_trash").innerHTML = txt.User.trash_1;
			Folders.open(0);
		}
	}
});
