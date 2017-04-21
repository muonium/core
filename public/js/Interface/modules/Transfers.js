// Transfers module. Loaded in window.onload()
var Transfers = (function() {
    // Private
    var minimized = false;

    // Public
    return {
        number : 0,
        numberUp : 0,
        numberDl : 0,

        toggle : function() {
            if(document.querySelector("#transfers").style.display == 'block') {
                Transfers.close();
            }
            else {
                Transfers.open();
            }
        },

        open : function() {
            //document.querySelector("#transfers").style.display = 'block';
            $('#transfers').fadeIn(400);
        },

        close : function() {
            //document.querySelector("#transfers").style.display = 'none';
            $('#transfers').fadeOut('fast');
        },

        isOpened : function() {
            return $('#transfers').css('display') === 'block' ? true : false;
        },

        minimize : function() {
            if(minimized) {
                document.querySelector("#transfers #toggle").style.display = 'block';
                document.querySelector("#transfers #content").style.display = 'block';
            }
            else {
                document.querySelector("#transfers #toggle").style.display = 'none';
                document.querySelector("#transfers #content").style.display = 'none';
            }
            minimized = !minimized;
        },

        showUp : function() {
            document.querySelector("#transfers #toggle ul > li:first-child").className = 'selected';
            document.querySelector("#transfers #toggle ul > li:last-child").className = '';
            document.querySelector("#transfers #content > #transfers_upload").style.display = 'block';
            document.querySelector("#transfers #content > #transfers_download").style.display = 'none';
        },

        showDl : function() {
            document.querySelector("#transfers #toggle ul > li:first-child").className = '';
            document.querySelector("#transfers #toggle ul > li:last-child").className = 'selected';
            document.querySelector("#transfers #content > #transfers_upload").style.display = 'none';
            document.querySelector("#transfers #content > #transfers_download").style.display = 'block';
        }
    }
});
