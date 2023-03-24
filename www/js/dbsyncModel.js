jQuery(document).ready(function ($) {

    jQuery(document).on("click", ".syncAll", function (e) {

        e.preventDefault();
        startSyncAll();

    });


});

function startSyncAll() {

    $allActions = jQuery(".modify_db_link");

    var allactions = $allActions.length;

    var count = 0;

    if (allactions == 0) {
        alert("Datenbank ist auf dem aktuellsten Stand");
    }

    $allActions.each(function (e) {


        var url = $(this).prop("href");

        jQuery.ajax({type: 'GET', cache: false, url: url, data: {autocall: true}, async: true})
            .done(function (data) {

                count++;

                console.log($(this).prop("href"));
                jQuery(".syncAllStatus").html("Status: " + count + " von " + allactions);

            });


    });


}