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
            if($("#transfers").css('display') === 'block') {
                Transfers.close();
            } else {
                Transfers.open();
            }
        },

        open : function() {
            $('#transfers').fadeIn(400);
        },

        close : function() {
            $('#transfers').fadeOut('fast');
        },

        isOpened : function() {
            return $('#transfers').css('display') === 'block' ? true : false;
        },

        minimize : function() {
            if(minimized) {
                $("#transfers .toggle, #transfers .content").show();
            } else {
                $("#transfers .toggle, #transfers .content").hide();
            }
            minimized = !minimized;
        },

        showUp : function() {
            $("#transfers .toggle ul > li:first-child").addClass('selected');
            $("#transfers .toggle ul > li:last-child").removeClass('selected');
            $("#transfers .content > .transfers_upload").show();
            $("#transfers .content > .transfers_download").hide();
        },

        showDl : function() {
            $("#transfers .toggle ul > li:first-child").removeClass('selected');
            $("#transfers .toggle ul > li:last-child").addClass('selected');
            $("#transfers .content > .transfers_upload").hide();
            $("#transfers .content > .transfers_download").show();
        }
    }
});
