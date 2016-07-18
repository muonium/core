/*var QC = 
{
    // Initialisation de l'interface
    //
    init: function()
    {
        // Chargement du contenu de l'onglet "Général"
    }
};*/

var Box;
var Area = 0;

var box = class {
    
    constructor() {
        this.box_div = document.querySelector("#box");
    }
    
    left_click(x, y) {
        if((x > this.x && x < this.x + this.box_div.clientWidth) && (y > this.y && y < this.y + this.box_div.clientHeight)) {
            // Action
        }
        else {
            // Hide box
            this.box_div.style.display = 'none';
        }
    }
    
    right_click(x, y) {
        // Show box at position x, y
        this.x = x;
        this.y = y;
        
        this.box_div.style.left = x+'px';
        this.box_div.style.top = y+'px';
        
        switch(Area) {
            case 0:
                this.box_div.innerHTML = '<p>Upload a file</p><p>Upload a dir</p>';
                break;
            case 1:
                this.box_div.innerHTML = '<p>Rename file</p><p>Remove file</p>';
        }
        this.box_div.style.display = 'block';
    }
};

window.oncontextmenu = function(event) {
    return false;
}

window.onclick = function(event) {
    // Left click
    Box.left_click(event.clientX, event.clientY);
}

window.onload = function() {
    Box = new box();
    
    document.querySelector("#desktop").addEventListener("contextmenu", function(event) {
        Box.right_click(event.clientX, event.clientY);
        return false;
    });
    
    // Files actions
    var files = document.querySelectorAll(".file");
    for (var i = 0; i < files.length; i++) {
        files[i].addEventListener("mouseover", function() {
            Area = 1;
        });
        files[i].addEventListener("mouseout", function() {
            Area = 0;
        });
    }
    
    // Folders actions
}