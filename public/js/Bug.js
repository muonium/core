$(document).ready(function () {    
    var userAgent = navigator.userAgent;
    var platform = navigator.platform;
    if (userAgent.indexOf('Edge') > -1){
        $("#browser").val('Edge');
    }else if (userAgent.indexOf('Chrome') > -1){
        $("#browser").val('Chrome');
    }else if (userAgent.indexOf('Firefox') > -1){
        $("#browser").val('Firefox');
    }else if (userAgent.indexOf('Safari') > -1){
        $("#browser").val('Safari');
    }else if (userAgent.indexOf('Opera') > -1){
        $("#browser").val('Opera');
    }else if (userAgent.indexOf('MSIE') > -1){
        $("#browser").val('Explorer');
    }
    if (platform.indexOf('Linux') > -1){
        if(platform.indexOf('arm') > -1 || platform.indexOf('Android') > -1){
            $("#os").val('Android');
        }else{
            $("#os").val('Linux');
        }
    }else if (platform.indexOf('Win') > -1){
        $("#os").val('Win');
    }else if (platform.indexOf('Mac') > -1){
        $("#os").val('Mac');
    }else if (platform.match(/(iPhone|iPod|iPad)/i)){
        $("#os").val('iOS');
    }
});