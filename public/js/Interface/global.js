/*var QC = 
{
    // Initialisation de l'interface
    //
    init: function()
    {
        // Chargement du contenu de l'onglet "Général"
    }
};*/

/* global.js : User's interface */

var Box;
var Area = 0; // 0 : desktop div, 1 : file, 2 : folder

// Box class. Show a div 'box' when user uses right click inside desktop div, close the box when user uses left click

var box = class {
    
    constructor() {
        this.box_div = document.querySelector("#box");
    }
    
    left_click(x, y) {
        // If the user uses left click inside the 'box'
        if((x > this.x && x < this.x + this.box_div.clientWidth) && (y > this.y && y < this.y + this.box_div.clientHeight)) {
            // Action
        }
        else {
            // Otherwise, hide 'box'
            this.box_div.style.display = 'none';
            Area = 0;
        }
    }
    
    right_click(x, y, id) {
        // Show box at position x, y
        this.x = x;
        this.y = y;
        
        this.box_div.style.left = x+'px';
        this.box_div.style.top = y+'px';
        
        if(id === undefined) {
            Area = 0;
        }
        
        // Content according to area
        switch(Area) {
            case 0:
                this.box_div.innerHTML = '<p>'+txt.Interface.upfile+'</p><p>'+txt.Interface.updir+'</p>';
                break;
            case 1:
                this.box_div.innerHTML = '<p>'+txt.Interface.rdfile+'</p><p>'+txt.Interface.rnmfile+'</p><p>'+txt.Interface.rmfile+'</p><p>'+txt.Interface.fileinfo+'</p>';
                break;
            case 2:
                this.box_div.innerHTML = '<p>'+txt.Interface.rddir+'</p><p>'+txt.Interface.rnmdir+'</p><p>'+txt.Interface.rmdir+'</p><p>'+txt.Interface.dirinfo+'</p>';
        }
        this.box_div.style.display = 'block';
    }
};

window.oncontextmenu = function(event) {
    // Disable right click outside desktop div
    return false;
}

window.onclick = function(event) {
    // Left click
    Box.left_click(event.clientX, event.clientY);
}

window.onload = function() {
    
    // Get txt from user's language json (language.js)
    getJSON();
    
    /*
    */
    
    Box = new box();
    
    // Right click inside desktop div
    document.querySelector("#desktop").addEventListener("contextmenu", function(event) {
        if(Area == 0) {
            // If we are inside desktop but not inside its children
            Box.right_click(event.clientX, event.clientY);
        }
        else {
            // If we are inside its children, set Area to 0 because this function is always called when user call file's actions or folder's actions
            // Next, we will be able to use right click inside desktop div (area = 0) and when we call file's actions or folder's actions, 'box' for 'desktop' area will not be displayed
            Area = 0;
        }
        return false;
    });
    
    // Right click inside divs with file's class (these divs are children of 'desktop')
    // After the execution of the function below, the function for 'desktop' above will be called automatically (because we are inside desktop) and will set Area to 0 without displaying a new 'box'
    // Files actions
    var files = document.querySelectorAll(".file");
    for (var i = 0; i < files.length; i++) {
        // For each file
        files[i].addEventListener("contextmenu", function(event) {
            // Right click
            Area = 1;
            // Call right_click function with div's id
            Box.right_click(event.clientX, event.clientY, this.id);
            return false;
        });
    }
    
    // Right click inside divs with folder's class (these divs are children of 'desktop')
    // After the execution of the function below, the function for 'desktop' above will be called automatically (because we are inside desktop) and will set Area to 0 without displaying a new 'box'
    // Folders actions
    var folders = document.querySelectorAll(".folder");
    for (var i = 0; i < folders.length; i++) {
        // For each folder
        folders[i].addEventListener("contextmenu", function(event) {
            // Right click
            Area = 2;
            // Call right_click function with div's id
            Box.right_click(event.clientX, event.clientY, this.id);
            return false;
        });
    }
}