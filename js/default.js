jQuery(document).ready(function(){
    jQuery('.datepicker').datepicker({dateFormat:"dd-mm-yy"});

    jQuery('#adding').on('click', function(){
            jQuery('.dialogbox').dialog();
    });
});
