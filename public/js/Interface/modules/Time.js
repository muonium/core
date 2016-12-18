// Time module. Loaded in window.onload()
// Used for tests, not necessary otherwise
var Time = (function() {
	// Private
	var s = 0, e = 0;

	// Constructor
	function Time() {
		this.start();
	};

	// Public
	Time.prototype.start = function() {
		s = new Date().getTime();
	};

	Time.prototype.stop = function() {
		e = new Date().getTime();
	};

	Time.prototype.elapsed = function(unit) {
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
	};

	return Time;
});
