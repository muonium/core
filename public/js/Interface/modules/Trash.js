var Trash = (function() {
	// If State = 0, set State to 1 and load contents from trash.
	// If State = 1, set State to 0 and load other contents.
	return {
		state : 0,
		switch : function() {
			Trash.state = Math.abs(Trash.state-1);
			Folders.open(0);
		},

		open : function() {
			Trash.state = 1;
			Folders.open(0);
		},

		close : function() {
			Trash.state = 0;
			Folders.open(0);
		}
	}
});
