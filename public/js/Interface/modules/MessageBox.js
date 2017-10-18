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

            $drag.style.left = left +"px";
            $drag.style.top = top +"px";
        }
    };

	// Constructor
	function MessageBox(msg) {
        var me = this;
        this.$msg = msg;
        this.$inputs = [];
        this.coordsSet = false;

        // No support of multiples messages box without callback from previous message box for now
        if($("#MessageBox").length) {
            if($("#MessageBox").css('display') === 'block') return false;
        }

        this.$elem = document.createElement("div");
        this.$elem.id = 'MessageBox';
        this.$elem.addEventListener("mousedown", function(e) {
            if(document.activeElement.tagName != 'INPUT' && document.activeElement.tagName != 'TEXTAREA') {
                $drag = me.$elem;
                var rect = $drag.getBoundingClientRect();
                $diffLeft = e.pageX - rect.left;
                $diffTop = e.pageY - rect.top;
            }
        });

        this.$elemClose = document.createElement("div");
        this.$elemClose.className = 'MessageBoxClose';
        this.$elemClose.innerHTML = 'x';
        this.$elemClose.addEventListener("click", this.close.bind(me));

        this.$elemMsg = document.createElement("div");
        this.$elemMsg.className = 'MessageBoxMsg';
        this.$elemMsg.innerHTML = this.$msg;

        this.$elemToggle = document.createElement("div");
        this.$elemToggle.className = 'MessageBoxToggle';

        this.$elemInput = document.createElement("div");
        this.$elemInput.className = 'MessageBoxInput';

        this.$elemBtns = document.createElement("div");
        this.$elemBtns.className = 'MessageBoxBtns';

        this.$elem.appendChild(this.$elemClose);
        this.$elem.appendChild(this.$elemMsg);
        this.$elem.appendChild(this.$elemToggle);
        this.$elem.appendChild(this.$elemInput);
        this.$elem.appendChild(this.$elemBtns);
	};

	// Public
    MessageBox.prototype.addButton = function(value, callback) {
        var me = this;
        var button = document.createElement("input");
        button.type = 'button';
        button.value = value;
        button.addEventListener("click", function() {
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
		console.log(me);
        var lblLeft = document.createElement("span");
        lblLeft.innerHTML = leftText;
        this.$elemToggle.appendChild(lblLeft);

        var lswitch = document.createElement("label");
        lswitch.className = 'switch';

        var toggle = document.createElement("input");
        toggle.type = 'checkbox';
        toggle.addEventListener("click", function() {
            if(this.checked) {
                callback.bind(me)();
            }
        });

        var slider = document.createElement("div");
        slider.className = 'slider';

        lswitch.appendChild(toggle);
        lswitch.appendChild(slider);
        this.$elemToggle.appendChild(lswitch);

        var lblRight = document.createElement("span");
        lblRight.innerHTML = rightText;
        this.$elemToggle.appendChild(lblRight);

        return this;
	};

    MessageBox.prototype.addInput = function(name, params = null) {
        var me = this;

        var input = document.createElement("input");
        input.type = 'text';

        if(params !== null && typeof(params) === 'object') {
            for(var i in params) {
                if(typeof(params[i]) === 'function') {
                    params[i] = params[i].bind(me);
                }
                input[i] = params[i];
            }
        }

        this.$elemInput.appendChild(input);
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
        this.$elem.style.width = width+'px';
        this.$elem.style.height = height+'px';
        return this;
	};

    MessageBox.prototype.close = function() {
        $('#MessageBox').fadeOut(200, function() {
            $(this).remove();
        });
	};

	MessageBox.prototype.show = function() {
        if(this.$elemBtns === undefined) return false;
        else if(this.$elemBtns.innerHTML == '') {
            // If there is no button, add a "OK" button
            this.addButton('OK');
        }

        if($("#MessageBox").length) {
            $("#MessageBox").html(this.$elem.innerHTML);
        }
        else {
            document.querySelector("body").insertBefore(this.$elem, document.querySelector("body").firstChild);
        }

        $('#MessageBox').fadeIn(400);

        if(this.$elemInput.firstChild !== null) {
            this.$elemInput.firstChild.focus();
            // small hack to place cursor at the end of value
            var content = this.$elemInput.firstChild.value;
            this.$elemInput.firstChild.value = '';
            this.$elemInput.firstChild.value = content;
        }
	};

	return MessageBox;
});
