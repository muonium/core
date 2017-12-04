var MessageBox = (function() {
	// Private
    var $elem;
    var $drag = null;
    var $diffLeft = 0;
    var $diffTop = 0;

    document.onmouseup = function(e) {
        $drag = null;
    };

    document.onmousemove = function(e) {
        if($drag !== null) {
            $drag.style.transform = 'none';
            var left = e.pageX - $diffLeft;
            var top = e.pageY - $diffTop;

            if(left < 0) left = 0;
            if(left + $drag.clientWidth > document.body.clientWidth) left = document.body.clientWidth - $drag.clientWidth - 2;
            if(top < 0) top = 0;
            if(top + $drag.clientHeight > document.body.clientHeight) top = document.body.clientHeight - $drag.clientHeight - 5;

            $drag.style.left = left + "px";
            $drag.style.top = top + "px";
        }
    };

	// Constructor
	function MessageBox(msg) {
        var me = this;
        this.$msg = msg;
        this.$inputs = [];
        this.coordsSet = false;

        this.$elem = $('<div class="MessageBox"></div>')[0];
        $(this.$elem).on("mousedown", function(e) {
            if(document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                $drag = me.$elem;
                var rect = $drag.getBoundingClientRect();
                $diffLeft = e.pageX - rect.left;
                $diffTop = e.pageY - rect.top;
            }
        });

        this.$elemClose = $('<div class="MessageBoxClose">x</div>')[0];
        this.$elemMsg = $('<div class="MessageBoxMsg">'+ this.$msg +'</div>')[0];
        this.$elemToggle = $('<div class="MessageBoxToggle"></div>')[0];
        this.$elemInput = $('<div class="MessageBoxInput"></div>')[0];
        this.$elemBtns = $('<div class="MessageBoxBtns"></div>')[0];

		$(this.$elemClose).on("click", this.close.bind(me));

        $(this.$elem).append(this.$elemClose);
        $(this.$elem).append(this.$elemMsg);
        $(this.$elem).append(this.$elemToggle);
        $(this.$elem).append(this.$elemInput);
        $(this.$elem).append(this.$elemBtns);
		$('body').append(this.$elem);
	};

	// Public
    MessageBox.prototype.addButton = function(value, callback) {
        var me = this;
        var button = $('<input type="button" value="'+ value +'">')[0];
        $(button).on('click', function() {
            me.close.bind(me)();
            if(typeof(callback) === 'function') {
                callback.bind(me)();
            }
        });
        this.$elemBtns.appendChild(button);
        return this;
	};

    MessageBox.prototype.addToggle = function(leftText, rightText, callback) {
        var me = this;
        var lblLeft = $('<span>'+ leftText +'</span>')[0];
		var lblRight = $('<span>'+ rightText +'</span>')[0];
        var lswitch = $('<label class="switch"></label>')[0];
        var toggle = $('<input type="checkbox">')[0];
        $(toggle).on('click', function() {
            if(this.checked) callback.bind(me)();
        });

        var slider = $('<div class="slider"></div>')[0];

		$(this.$elemToggle).append(lblLeft);
        $(lswitch).append(toggle);
        $(lswitch).append(slider);
        $(this.$elemToggle).append(lswitch);
        $(this.$elemToggle).append(lblRight);

        return this;
	};

    MessageBox.prototype.addInput = function(name, params = null) {
        var me = this;
        var input = $('<input type="text">')[0];

        if(params !== null && typeof(params) === 'object') {
            for(var i in params) {
                if(typeof(params[i]) === 'function') {
                    params[i] = params[i].bind(me);
                }
                input[i] = params[i];
            }
        }
        $(this.$elemInput).append(input);
        this.$inputs[name] = input;
        return this;
	};

    MessageBox.prototype.setCoords = function(x, y) {
        this.$elem.style.left = x+'px';
        this.$elem.style.top = y+'px';
        this.coordsSet = true;
        return this;
	};

    MessageBox.prototype.setSize = function(width, height) {
        this.$elem.style.width  = parseInt(width) == width ? width+'px' : width;
        this.$elem.style.height = parseInt(height) == height ? height+'px' : height;
        return this;
	};

    MessageBox.prototype.close = function() {
        $(this.$elem).fadeOut(200, function() {
            $(this).remove();
        });
	};

	MessageBox.prototype.show = function() {
        if(this.$elemBtns === undefined) return false;
        else if(this.$elemBtns.innerHTML == '') {
			// If there is no button, add a "OK" button
            this.addButton('OK');
        }

		$(this.$elem).fadeIn(400);

        if(this.$elemInput.firstChild !== null) {
            this.$elemInput.firstChild.focus();
            // small hack to place cursor at the end of value
            var content = this.$elemInput.firstChild.value;
            this.$elemInput.firstChild.value = '';
            this.$elemInput.firstChild.value = content;
        }
		return this;
	};

	return MessageBox;
});
