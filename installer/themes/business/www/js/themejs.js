jQuery(document).ready(function () {

    jQuery(document).on("click", ".navigationheader", function (e) {
        var category = jQuery(this).data("category");
        jQuery(".navelement[data-category='" + category + "']").toggle();
        jQuery(this).toggleClass("opened");
    });

    jQuery(document).on("click", ".openUsermenu", function (e) {
        jQuery(".usermenuContent").toggle();
    });

});

jQuery(document).ready(function ($) {

    function enableEdit(dialogid) {
        jQuery("#" + dialogid + " .bpfwbutton.editAction").show();
        jQuery("#" + dialogid + " .bpfwbutton.cancelAction > div > div").html(__("Discard"));
    }

    jQuery(document).on("change keyup", ".selectpicker, .checkbox, input, textarea", function () {
        if (typeof getIdOfCurrentDialog === 'function') {
            enableEdit(getIdOfCurrentDialog());
        }
    });

    jQuery(document).on("mouseup mouseout touchend", ".pad", function (e) {
        if (typeof getIdOfCurrentDialog === 'function') {
            enableEdit(getIdOfCurrentDialog());
        }
    });

    jQuery(document).on('changed.bs.select', " select.selectpicker", function () {

        if (typeof getIdOfCurrentDialog === 'function') {
            enableEdit(getIdOfCurrentDialog());
        }
    });

});