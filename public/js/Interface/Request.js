// Request.js
// modulesLoaded : Modules in interface.js

// Thank you Martin Wantke for the code below (addEventListener & removeEventListener)
EventTarget.prototype.addEventListenerBase = EventTarget.prototype.addEventListener;
EventTarget.prototype.addEventListener = function(type, listener) {
    if(!this.EventList) { this.EventList = []; }
    this.addEventListenerBase.apply(this, arguments);
    if(!this.EventList[type]) { this.EventList[type] = []; }
    var list = this.EventList[type];
    for(var index = 0; index != list.length; index++) {
        if(list[index] === listener) { return; }
    }
    list.push(listener);
};

EventTarget.prototype.removeEventListenerBase = EventTarget.prototype.removeEventListener;
EventTarget.prototype.removeEventListener = function(type, listener) {
    if(!this.EventList) { this.EventList = []; }
    if(listener instanceof Function) { this.removeEventListenerBase.apply(this, arguments); }
    if(!this.EventList[type]) { return; }
    var list = this.EventList[type];
    for(var index = 0; index != list.length;) {
        var item = list[index];
        if(!listener) {
            this.removeEventListenerBase(type, item);
            list.splice(index, 1); continue;
        } else if(item === listener) {
            list.splice(index, 1); break;
        }
        index++;
    }
    if(list.length == 0) { delete this.EventList[type]; }
};

//

var Request = {
    modulesLoaded: false,
    load : function(controller, action) {
		var url = controller+'/'+action;
        if(action === 'DefaultAction' || action === undefined) {
            url = controller;
		}
        console.log("Loading "+url);

        if(url.length > 0) {
            // Load page
            xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if(xhr.status == 200 && xhr.readyState == 4) {
                    history.pushState({ path: this.path }, '', url);
                    document.documentElement.innerHTML = xhr.responseText;

                    switch(url) {
                        case "User":
                            // Call loader from interface.js to load "User" page features
                            UserLoader(Folders.id);
                            break;
                        case "Favorites":
                            // Remove all keydown events from interface.js
                            window.removeEventListener("keydown");
                            break;
                    }
                }
            }
            xhr.send(null);
        }
    }
} || {};
