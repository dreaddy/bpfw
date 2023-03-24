'use strict';

// get Translation

function __(word) { // jshint ignore:line

    if (typeof translations === "undefined") {
        return "??" + word + "??";
    }

    if (typeof translations[word] !== "undefined") { // jshint ignore:line
        return translations[word]; // jshint ignore:line
    }

    return "??" + word + "??";

}

function bpfw_getCurrentlyOpenedRowid() { // jshint ignore:line

    var filter = jQuery("#" + getIdOfCurrentDialog()).data("rowid"); // jshint ignore:line

    return filter;

}

function spoil(id, display) { // jshint ignore:line
    if (document.getElementById) {
        var divid = document.getElementById(id);
        divid.style.display = (divid.style.display !== 'none' && divid.style.display ? 'none' : display);
    }
}


function getQueryVariable(variable) { // jshint ignore:line
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] === variable) {
            return pair[1];
        }
    }
    return (false);
}


function bpfwRandomizeNames(selector) {

    jQuery(selector).each(function () {

        if (jQuery(this).attr("name") && (!jQuery(this).data("origname") || jQuery(this).data("origname") === "")) {

            var randval = Math.floor((Math.random() * 999999));

            jQuery(this).data("origname", jQuery(this).attr("name"));
            jQuery(this).attr("name", randval);
        }

    });

}

function bpfwRestoreNames(selector) {

    jQuery(selector).each(function () {

        if (jQuery(this).attr("name") && jQuery(this).data("origname") && jQuery(this).data("origname") !== "") {


            jQuery(this).attr("name", jQuery(this).data("origname"));

            jQuery(this).data("origname", "");


        }

    });

}


jQuery(document).ready(function () {

    bpfwRandomizeNames(".removeAutocomplete");

    /** mobile hamburger */
    jQuery("div.mobileHamburger").click(function () {

        if (jQuery("body").hasClass("mobileMenuOpened")) {
            jQuery("body").addClass("mobileMenuClosed");
            jQuery("body").removeClass("mobileMenuOpened");
        } else {
            jQuery("body").addClass("mobileMenuOpened");
            jQuery("body").removeClass("mobileMenuClosed");
        }

        jQuery('.dataTable').DataTable()
            .columns.adjust()
            .responsive.recalc();

    });

    // jQuery("body").addClass("mobileMenuClosed");
    // jQuery("body").removeClass("mobileMenuOpened");

    // #listviewForm reicht uach, wenn es Probleme gibt
    jQuery("form").submit(function () {
        bpfwRestoreNames(".removeAutocomplete");  // restore old names (if any)
    });


    registerBpfwEvent("startEdit", function (params) {

        if(params.model === "appsettings") {
            // component initializer of mainform is not always including selectpicker, fire again manually

            jQuery(".selectpicker").selectpicker("refresh");
        }

    });


});


var errordialog_shown = false;
var confirmfialog_shown = false;

var confirmdialog_executefunction;


function refreshScrollableBackgroundModalDialog() {

    var diaid = getIdOfCurrentDialog(); // jshint ignore:line

    if (jQuery("#" + diaid + '.manipulateDataDialog').data("shownonmodal") === 1) {
        jQuery('body').removeClass('modal-open');
    } else {
        if (diaid !== undefined || errordialog_shown || confirmfialog_shown) {
            jQuery('body').addClass('modal-open');
        } else {
            jQuery('body').removeClass('modal-open');
        }
    }

}


var errordialog_executefunction;
jQuery("#errorMessageBox").on("hidden.bs.modal", function () {

    if (errordialog_executefunction !== undefined) {
        errordialog_executefunction();
    }
    errordialog_shown = false;
    refreshScrollableBackgroundModalDialog();

});


jQuery("#confirmMessageBox").on("hidden.bs.modal", function () {
    confirmfialog_shown = false;
    refreshScrollableBackgroundModalDialog();

});


jQuery("#confirmMessageBox #confirmdialog_confirm").on("click", function () {

    if (confirmdialog_executefunction !== undefined) {
        confirmdialog_executefunction();
    }

});

function errorDialog(title, msg, executefunction) {

    errordialog_shown = true;

    jQuery('body').addClass('modal-open');

    jQuery("#errorMessageBox .modal-title").html(title);
    jQuery("#errorMessageBox .modal-body").html(msg);

    jQuery("#errorMessageBox").modal('show');

    errordialog_executefunction = executefunction;
}

/**
 *
 * @param {any} title Title in Dialogbox
 * @param {any} msg Message in Dialogbox
 * @param {any} executefunction execute function on ok click
 * @param {any} confirm_text Text confirm button
 * @param {any} cancel_text Text Cancel Button
 */
function confirmDialog(title, msg, executefunction = undefined, confirm_text = "Ok", cancel_text = "Abbrechen") { // jshint ignore:line

    confirmfialog_shown = true;

    jQuery('body').addClass('modal-open');

    jQuery("#confirmMessageBox .modal-title").html(title);
    jQuery("#confirmMessageBox .modal-body").html(msg);

    jQuery("#confirmMessageBox").modal('show');

    jQuery("#confirmMessageBox .modal-footer .buttonbar-afterform-wrap #confirmdialog_confirm>div>div").html(confirm_text);
    jQuery("#confirmMessageBox .modal-footer .buttonbar-afterform-wrap #confirmdialog_cancel>div>div").html(cancel_text);

    confirmdialog_executefunction = executefunction;

}

// TODO: outsource to component
/*
jQuery(document).ready(function () {

    jQuery(".intervalSelection").change(function () {
        alert(this.value);
    });

});*/


function toggleTableMenu(id) {

    var isOpen = jQuery(".list-button-tablefield[data-id=" + id + "] .datalist_button_wrapper").hasClass("list-enabled");

    jQuery(".list-button-tablefield .datalist_button_wrapper").removeClass("list-enabled");

    if (!isOpen) {
        jQuery(".list-button-tablefield[data-id=" + id + "] .datalist_button_wrapper").addClass("list-enabled");
    }

    return !isOpen;
}


jQuery(".minimize-button").on("click", function () {

    /*  if( jQuery(".mobileHamburger").is(":visible") ){*/

    if (jQuery("body").hasClass("mobileMenuClosed")) {
        jQuery("body").addClass("mobileMenuOpened");
        jQuery("body").removeClass("mobileMenuClosed");
    } else {
        jQuery("body").addClass("mobileMenuClosed");
        jQuery("body").removeClass("mobileMenuOpened");
    }

    jQuery('.dataTable').DataTable()
        .columns.adjust()
        .responsive.recalc();
    /* }else {

         jQuery(".navigationWrapper").addClass("minimizedNav");

         jQuery("#mainContentWrapper").addClass("minimizedNav");

     }*/

});

jQuery(".maximize-button").on("click", function () {


    /*  if (jQuery(".mobileHamburger").is(":visible")) {*/

    if (jQuery("body").hasClass("mobileMenuOpened")) {
        jQuery("body").addClass("mobileMenuClosed");
        jQuery("body").removeClass("mobileMenuOpened");
    } else {
        jQuery("body").addClass("mobileMenuOpened");
        jQuery("body").removeClass("mobileMenuClosed");
    }

    jQuery('.dataTable').DataTable()
        .columns.adjust()
        .responsive.recalc();

    /* } else {

         jQuery(".navigationWrapper").removeClass("minimizedNav");
         jQuery("#mainContentWrapper").removeClass("minimizedNav");

     }*/

});


jQuery("body").on("click", ".iframepopup", function (e) {

    var link = jQuery(this).attr("href");
    ///var hierachy = getCurrentDialogHierachy() + 1;

    jQuery('#fullsizeIframeDialog').modal('show');
    jQuery('#fullsizeIframeDialog iframe.contentframe').attr('src', link);
    e.preventDefault();

});


function openWindowWithPostdata(url, target = "_blank", data = {}) {

    var form = document.createElement("form");
    form.target = target;
    form.method = "POST";
    form.action = url;
    form.style.display = "none";

    for (var key in data) {
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = key;
        input.value = data[key];
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

}


jQuery(document).ready(function () {

    // open menu

    jQuery("#adminTable_ajax").on("click", "tbody tr > td:nth-child(2)", function (e) {

            var id = jQuery(this).find("div").data("id");
            // alert(id);
            //toggleTableMenu(id)

            var preventDefault = toggleTableMenu(id);

            if (preventDefault) {
                e.preventDefault();
            }

        }
    );

    var cronjoburl = "?p=ajax&is_js_execution=1";

    jQuery.ajax(
        {
            type: 'POST',
            cache: false,
            url: cronjoburl,
            data:
                {
                    p: "ajax",
                    ajaxCall: 1,
                    command: "handleCronjobs",
                    sigauth: "dsfjkln3289fdsijo3r90fadspo3rh80gruh9fwej90vdonbhteohijofvdsijohe",

                },
            async: true
        }
    )
        .done(
            function (data) {
                console.log("cron Output:" + data);
            }
        );


});


jQuery(document).ready(function (e) {

    jQuery('#statusFilter').on('hidden.bs.select', function (e) {

        if (typeof bpfw_refreshPageAfterDataChange === "function") {
            bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());
        }

    });

});


var eventsToCall = [];


function handleBpfwEvent(eventname, parameters) {

    if (eventsToCall[eventname] !== undefined) {

        jQuery.each(eventsToCall[eventname], function (i) {

            parameters.event = eventname;

            eventsToCall[eventname][i](parameters);

        });


    }


}

function registerBpfwEvent(eventnames, functionToCall) {

    const events = eventnames.split(" ");

    jQuery.each(events, function (i) {

        var eventname = events[i];

        if (eventsToCall[eventname] == undefined) {
            eventsToCall[eventname] = [];
        }

        eventsToCall[eventname].push(functionToCall);


    });

}


var consolecount = 0;
jQuery(document).on("click", ".show_debug", function (e) {

    consolecount++;

    if (consolecount >= 5) {

        jQuery(".debugConsole").toggle();

    }


});


function errorlog(msg) {

    console.log("log:" + msg);

    jQuery(".debugConsole").prepend(msg + "<br>\r\n");

}

function bpfw_getActiveLang() {

    var page = jQuery("body").data("current_lang");
    return page;
}

function bpfw_bpfwpath() {

    var path = jQuery("body").data("bpfwpath");

    if(path == undefined){
        return "/vendor/bpfw/bpfw/";
    }

    return path;

}


function bpfw_searchEnabled() {

    if (jQuery("#listcontent").data("search_enabled") !== undefined) {
        return jQuery("#listcontent").data("search_enabled");
    } else {
        return true;
    }

}

//jQuery(document).on("change click dblclick mousedown mouseenter mouseup", ".spoilericon", function (e) {

jQuery(document).on("change click dblclick mousedown tap", ".spoilericon", function (e) {


    jQuery(this).find(".spoilercontainer").toggle();


    /* $('*').bind('blur change click dblclick error focus focusin focusout hover keydown keypress keyup load mousedown mouseenter mouseleave mousemove mouseout mouseover mouseup resize scroll select submit', function (event) {
         event.stopPropagation();
     });*/

    e.stopPropagation();

    e.preventDefault();

    return false;


});

jQuery(document).on("mouseenter", ".spoilericon", function () {

    jQuery(this).find(".spoilercontainer").show();

});

jQuery(document).on("mouseleave", ".spoilericon", function () {

    jQuery(this).find(".spoilercontainer").hide();

});