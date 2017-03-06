var MessageBox = (function() {
	// Private
    var $msg;
    var $elem;
    var $elemClose;
    var $elemMsg;
    var $elemBtns;

    var $drag = null;
    var $diffLeft = 0;
    var $diffTop = 0;

    document.onmouseup = function(e) {
        $drag = null;
    };

    document.onmousemove = function(e) {
        if($drag !== null) {
            var left = e.pageX - $diffLeft;
            var top = e.pageY - $diffTop;

            if(left < 0) left = 0;
            if(left + $drag.clientWidth > window.innerWidth) left = window.innerWidth - $drag.clientWidth - 2;
            if(top < 0) top = 0;
            if(top + $drag.clientHeight > window.innerHeight) top = window.innerHeight - $drag.clientHeight - 5;

            $drag.style.left = left +"px";
            $drag.style.top = top +"px";
        }
    };

	// Constructor
	function MessageBox(msg) {
        $msg = msg;

        $elem = document.createElement("div");
        $elem.id = 'MessageBox';
        $elem.style.left = Math.round((window.innerWidth - $elem.clientWidth) / 2)+'px';
        $elem.style.top = Math.round((window.innerHeight - $elem.clientHeight) / 2)+'px';
        $elem.addEventListener("mousedown", function(e) {
            $drag = $elem;
            $diffLeft = e.pageX - parseInt($drag.style.left.replace('px', ''));
            $diffTop = e.pageY - parseInt($drag.style.top.replace('px', ''));
            e.preventDefault();
        });

        $elemClose = document.createElement("div");
        $elemClose.className = 'MessageBoxClose';
        $elemClose.innerHTML = 'x';
        $elemClose.addEventListener("click", this.close);

        $elemMsg = document.createElement("div");
        $elemMsg.className = 'MessageBoxMsg';
        $elemMsg.innerHTML = $msg;

        $elemBtns = document.createElement("div");
        $elemBtns.className = 'MessageBoxBtns';

        $elem.appendChild($elemClose);
        $elem.appendChild($elemMsg);
        $elem.appendChild($elemBtns);
	};

	// Public
    MessageBox.prototype.addButton = function(value, callback) {
        var that = this;
        var button = document.createElement("input");
        button.type = 'button';
        button.value = value;
        button.addEventListener("click", function() {
            that.close();
            if(typeof callback === 'function') {
                callback();
            }
        });
        $elemBtns.appendChild(button);
        return this;
	};

    MessageBox.prototype.setCoords = function(x, y) {
        $elem.style.left = x+'px';
        $elem.style.top = y+'px';
        return this;
	};

    MessageBox.prototype.setSize = function(width, height) {
        $elem.style.width = width+'px';
        $elem.style.height = height+'px';
        return this;
	};

    MessageBox.prototype.close = function() {
        $elem.parentNode.removeChild($elem);
	};

	MessageBox.prototype.show = function() {
        if($elemBtns.innerHTML == '') {
            // If there is no button, add a "Ok" button
            this.addButton('Ok');
        }

        if(document.querySelector("#MessageBox")) {
            document.querySelector("#MessageBox").innerHTML = $elem.innerHTML;
        }
        else {
            document.querySelector("body").insertBefore($elem, document.querySelector("body").firstChild);
        }
        document.querySelector("#MessageBox").style.display = 'block';
	};

	return MessageBox;
});
