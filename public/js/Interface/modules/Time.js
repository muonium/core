// Time module. Loaded in window.onload()
// Used for tests, not necessary otherwise
var Time = (function() {
	// Private
	var s = 0, e = 0;

	// Public
	return {
		start : function() {
			s = new Date().getTime();
		},

		stop : function() {
			e = new Date().getTime();
		},

		elapsed : function(unit) {
			var diff = e-s;
			switch(unit) {
				case 'ms':
					return diff;
				case 'cs':
					return diff/10;
				case 'ds':
					return diff/100;
				case 's':
					return diff/1000;
				default:
					return diff;
			}
		}
	}
});
