jQuery(function($){
    $("#lym-popup-toggle-m").on("click", function(e){
        e.stopPropagation();
        $("#lym-popup-m").toggleClass("lym-popup-hide-m");
        $("#lym-popup-toggle-m").toggleClass("lym-popup-toggle-m-expend");
    });
});
