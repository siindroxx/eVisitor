$(document).ready(function() {

    $('#dropdown').change(function() {
        if( $(this).val() == 1) {
            $('#fechafin').prop( "disabled", false );
            $('#horafin').prop( "disabled", false );
        } else {
            $('#fechafin').prop( "disabled", true );
            $('#horafin').prop( "disabled", true );
        }
    });

});
