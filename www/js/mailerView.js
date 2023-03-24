jQuery(document).ready(function () {

    var lang = {};

    if (bpfw_getActiveLang() === "de") {
        lang = {
            "url": bpfw_bpfwpath() + "libs/datatables/German.json"
        };
    }

    // main Datatables
    jQuery(".mailerlisttable").DataTable({


        "language": lang,

        responsive: false,
        iDisplayLength: 999999,
        "order": [[0, "desc"]],

    });

});

jQuery("body").on(
    "click",
    ".newsletter_receiver.switchable .iconswitcher",
    function () {

        jQuery(this).toggleClass("checkOn");
        jQuery(this).toggleClass("checkOff");

        jQuery(this).toggleClass("fa-times");
        jQuery(this).toggleClass("fa-check");
        //alert("test");

        jQuery(this).parent().parent().parent().toggleClass("enabled");
        jQuery(this).parent().parent().parent().toggleClass("disabled");


    }
);


jQuery("body").on("click", ".toggle_all_rcpt",

    function () {

        var audience = jQuery(this).data("audience");

        var selector = "#" + audience + "listtable";
        var elementselector = ".newsletter_receiver.switchable i";
        jQuery(selector).find(elementselector).toggleClass("checkOff");
        jQuery(selector).find(elementselector).toggleClass("checkOn");

        jQuery(selector).find(elementselector).toggleClass("fa-check");
        jQuery(selector).find(elementselector).toggleClass("fa-times");

        jQuery(selector).find(".newsletter_receiver.switchable").toggleClass("enabled");
        jQuery(selector).find(".newsletter_receiver.switchable").toggleClass("disabled");
    }
);

jQuery("body").on("click", ".deactivate_all_rcpt",

    function () {


        var audience = jQuery(this).data("audience");

        var selector = "#" + audience + "listtable";
        var elementselector = ".newsletter_receiver.switchable i";

        jQuery(selector).find(elementselector).addClass("checkOff");
        jQuery(selector).find(elementselector).removeClass("checkOn");

        jQuery(selector).find(elementselector).removeClass("fa-check");
        jQuery(selector).find(elementselector).addClass("fa-times");

        jQuery(selector).find(".newsletter_receiver.switchable").removeClass("enabled");
        jQuery(selector).find(".newsletter_receiver.switchable").addClass("disabled");

    }
);

jQuery("body").on("click", ".activate_all_rcpt",

    function () {

        var audience = jQuery(this).data("audience");

        var selector = "#" + audience + "listtable";
        var elementselector = ".newsletter_receiver.switchable i";

        jQuery(selector).find(elementselector).removeClass("checkOff");
        jQuery(selector).find(elementselector).addClass("checkOn");

        jQuery(selector).find(elementselector).addClass("fa-check");
        jQuery(selector).find(elementselector).removeClass("fa-times");

        jQuery(selector).find(".newsletter_receiver.switchable").addClass("enabled");
        jQuery(selector).find(".newsletter_receiver.switchable").removeClass("disabled");
    }
);

jQuery("body").on("click", ".test_mail",

    function () {

        var filter = getQueryVariable("filter");

        var currentPage = getQueryVariable('p');

        if (currentPage === false) currentPage = "";
        var title_url = "?p=" + currentPage + "&ajaxCall=true&command=testmail&filter=" + filter;

        var audience = jQuery(this).data("audience");

        tinyMCE.triggerSave();

        var rcpt = jQuery("#tab-mailer-" + audience + "-content .testmail_rcpt").val();
        var title = jQuery("#tab-mailer-" + audience + "-content .mailtitle").val();
        var txt = jQuery("#tab-mailer-" + audience + "-content .mailtext_tinymce").val();


        jQuery.ajax({
            type: 'POST', cache: false,
            url: title_url,
            data: {
                filter: filter,
                command: "testmail",
                rcpt: rcpt,
                title: title,
                txt: txt,
                audience: audience
            }, async: true
        })
            .done(function (data) {
                alert(data);
            });

        // alert("test");

    }
);

jQuery("body").on("click", ".send_mail",
    function () {


        tinyMCE.triggerSave();


        var currentPage = getQueryVariable('p');

        var audience = jQuery(this).data("audience");

        var userids = [];

        // alert(jQuery("#tab-mailer-" + audience + "-content .newsletter_receiver.enabled").data("userid"));

        jQuery("#tab-mailer-" + audience + "-content .newsletter_receiver.enabled").each(function (index, element) {
//                console.log(jQuery(element).data("userid"));
            userids.push(jQuery(element).data("userid"));
        });


        var mailtitle = jQuery("#mailtitle_" + audience).val();

        var mailtext = jQuery("#mailtext_tinymce_" + audience).val();

        console.log(audience);

        console.log(mailtitle);
        console.log(mailtext);
        console.log(userids);


        var url = "?p=" + currentPage + "&ajaxCall=true&command=createNewsletterInOutbox";

        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: url,
            data: {
                audience: audience,
                userids: JSON.stringify(userids),
                mailtitle: mailtitle,
                mailtext: mailtext
            },
            async: true
        })
            .done(function (data) {

                alert(data);


            });


    }
);
