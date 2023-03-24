jQuery(document).ready(function ($) {

    jQuery(document).on("click", ".createButton", function (e) {

        e.preventDefault();


        createmissingSearchIndex(this, jQuery(this).data("model"));

    });


    jQuery(document).on("click", ".clearButton", function (e) {

        e.preventDefault();


        clearmissingSearchIndex(this, jQuery(this).data("model"));

    });


});

function createmissingSearchIndex(button, modelname) {


//     var url = $(this).prop("href");

    var url = "?p=createsearchindex&ajaxCall=true&command=createsearchindex&model=" + modelname;

    jQuery.ajax({type: 'GET', cache: false, url: url, data: {}, async: true})
        .done(function (data) {

            var d = JSON.parse(data);

            var val = parseInt(jQuery("div.wrapper-" + modelname + " .amount_done").html());
            jQuery("div.wrapper-" + modelname + " .amount_done").html(val + d.amount_done);

            if (d.amount_done > 0) {
                createmissingSearchIndex(button, modelname);
            }

        });


}


function clearmissingSearchIndex(button, modelname) {


    //     var url = $(this).prop("href");

    var url = "?p=createsearchindex&ajaxCall=true&command=clearsearchindex&model=" + modelname;

    jQuery.ajax({type: 'GET', cache: false, url: url, data: {}, async: true})
        .done(function (data) {

            var d = JSON.parse(data);
            if (d.success) {
                jQuery("div.wrapper-" + modelname + " .amount_done").html(0);
            } else {
                alert("failed");
            }

        });


}