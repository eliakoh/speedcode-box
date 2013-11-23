$(document).ready(function() {
    
    $('.toggle').click(function() {
        var selector = $(this).attr('rel');
        $(selector).fadeToggle('fast');
        
        return false;
    });
});