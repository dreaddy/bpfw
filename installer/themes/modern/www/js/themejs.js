jQuery(document).ready(function () {


    jQuery(document).on("click", ".navigationheader", function () {

        var category = jQuery(this).data("category");

        if (jQuery(this).data("link") !== '') {
            window.location.href = "?p="+jQuery(this).data("link");
            return;
        }

        jQuery(".navelement[data-category='" + category + "']").toggle();


        jQuery(this).toggleClass("opened");



    });


    jQuery(document).on("click", ".openUsermenu", function () {

        jQuery(".usermenuContent").toggle();

    });



});




jQuery(document).ready(function () {

    function enableEdit(dialogid) {

        jQuery("#" + dialogid + " .bpfwbutton.editAction").show();
        jQuery("#" + dialogid + " .bpfwbutton.cancelAction > div > div").html(__("Discard"));

    }

    jQuery(document).on("change keyup", ".selectpicker, .checkbox, input, textarea", function () {
        if (typeof getIdOfCurrentDialog === 'function') {
            enableEdit(getIdOfCurrentDialog());
        }
    });



    jQuery(document).on("mouseup mouseout touchend", ".pad", function () {
        if (typeof getIdOfCurrentDialog === 'function') {
            enableEdit(getIdOfCurrentDialog());
        }

    });


    jQuery(document).on('changed.bs.select', " select.selectpicker", function () {

        if (typeof getIdOfCurrentDialog === 'function') {
            enableEdit(getIdOfCurrentDialog());
        }

    });


    jQuery("#collectionSelector").change(function () {

        var id = jQuery("#collectionSelector").val();

        var url = "?p=collection&ajaxCall=true&edit=" + id + "&command=setActiveCollection&newid=" + id;

        jQuery.ajax({ type: 'POST', cache: false, url: url, data: { newid: id }, async: true })
            .done(function () {
                location.reload();
            });


    });


});