/*function getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] === variable) { return pair[1]; }
    }
    return (false);
}*/

function refreshFields(selectclass, valuefield) {

    var fields = "";
    var first = true;
    jQuery(selectclass).each(
        function () {

            if (this.checked) {

                if (!first) {
                    fields += ",";
                }

                fields += jQuery(this).attr('id');
                first = false;

            }
        }
    );
    //alert(valuefield);
    jQuery(valuefield).val(fields);

}

jQuery(document).ready(
    function () {

        jQuery(".edit-content-form .documentSelectCheckbox, .edit-content-form .attachmentSelectCheckbox").change(function () {
            var category = jQuery(this).data("category");
            refreshFields(".category_" + category, ".edit-content-form .documentstodisplay");


            refreshFields(".timeline_" + category, ".edit-content-form .filestodisplay");

            refreshFields(".emailattachments_" + category, ".edit-content-form .emailattachments");

        });


        jQuery(".edit-content-form .documentSendMail").click(
            function () {

                if (confirm('Email wirklich mit ausgewählten Anhängen abschicken?')) {

                    var category = jQuery(this).data("category");

                    refreshFields(".category_" + category, ".edit-content-form .documentstodisplay");
                    refreshFields(".timeline_" + category, ".edit-content-form .filestodisplay");
                    refreshFields(".emailattachments_" + category, ".edit-content-form .emailattachments");

                    // var files = jQuery("input[data-category=[" + category + "]").val();


                    var fields = jQuery("#documentstodisplay_" + category).val();

                    var emailattachments = jQuery("#emailattachments_" + category).val();

                    var files = jQuery("#filestodisplay_" + category).val();

                    if (fields === null || fields === "" || fields === "undefined") {
                        fields = null;
                        //alert("keine Formulare gewählt");
                    } //else {

                    var url = "?p=" + getQueryVariable("p") + "&ajaxCall=true&command=PdfmanagerSendMail"+ "&filter="+getQueryVariable("filter")+ "&modelused="+getQueryVariable("modelused");

                    // alert(files);
                    tinyMCE.triggerSave();

                    $.post(url, {
                        filter: getQueryVariable("filter"),
                        generatePdf: fields,
                        files: files,
                        emailattachments: emailattachments,
                        receiver: jQuery("#" + category + "_receiver").val(),
                        title: jQuery("#" + category + "_title").val(),
                        pdf_attachment_name: jQuery("#" + category + "_attachment_title").val(),
                        text: jQuery("#" + category + "_text").val()
                    }).done(function (data) {
                        alert(data);
                    });


                }

            }
        );

        jQuery(".edit-content-form .documentPreview").click(
            function () {

                var category = jQuery(this).data("category");

                refreshFields(".category_" + category, ".edit-content-form .documentstodisplay");
                refreshFields(".timeline_" + category, ".edit-content-form .filestodisplay");
                refreshFields(".emailattachments_" + category, ".edit-content-form .emailattachments");

                var fields = jQuery("#documentstodisplay_" + category).val();

                var emailattachments = jQuery("#emailattachments_" + category).val();

                var files = jQuery("#filestodisplay_" + category).val();

                var mailtext = jQuery("#" + category + "_text").val();
                var mailtitle = jQuery("#" + category + "_title").val();

                if ((mailtext === null || mailtext === "") && (fields === null || fields === "" || fields === "undefined")) {
                    alert("keine Formulare gewählt und/oder vorhanden" + fields);
                } else {

                    openWindowWithPostdata(
                        "?p="+getQueryVariable("p")+"&ajaxCall=true&command=generatePdf&processViews=true" + "&category=" + category + "&modelused=" + getQueryVariable("modelused") + "&filter=" + getQueryVariable("filter") + "&generatePdf=" + fields,

                        "_blank",

                        {
                            mailtext: mailtext,
                            mailtitle: mailtitle,
                            emailattachments: emailattachments,
                            files: files
                        }
                    );


                }

            }
        );


        // refreshFields();

    });


function sendMailWithPdf(mailAddress, topic, text, generatePdf) {

    var url = "?p="+getQueryVariable("p")+"&ajaxCall=true&processViews=true&sendmail=true&filter=" + getQueryVariable("filter") + "&modelused=" + getQueryVariable("modelused") + "";

    jQuery.post(url, {mailAddress: mailAddress, topic: topic, text: text, generatePdf: generatePdf})
        .done(function (data) {

            var values = JSON.parse(data);

            if (values["success"] === "true") {
                alert("Mail wurde erfolgreich an " + mailAddress + " verschickt.");
            } else {
                alert("Fehler beim Senden der Mail an " + mailAddress);
            }

        });

}


jQuery(document).ready(function () {


    refreshFields(".advisor_beraterpaket", ".edit-content-form .documentstodisplay");
    refreshFields(".timeline_beraterpaket", ".edit-content-form .filestodisplay");
    refreshFields(".emailattachments_beraterpaket", ".edit-content-form .emailattachments");

    jQuery(".edit-content-form").hide();
    jQuery(".edit-content-form:first").show();

});


jQuery(".selectAllCheckbox").change(function () {
    var category = $(this).data("category");

    jQuery(".category_" + category).prop('checked', jQuery(this).prop('checked'));

    if (jQuery(this).prop('checked') === true) {

        jQuery(".category_" + category + ".groupedCheckbox").prop('checked', false);
        jQuery(".category_" + category + ".groupedCheckbox:first").prop('checked', true);
    }

});


jQuery(".documentSelectCheckbox").change(function () {
    if (jQuery(this).prop('checked') === false) {

        var category = $(this).data("category");

        jQuery("#" + category + "_all").prop('checked', false);

    }
});


jQuery(".groupedCheckbox").change(function () {

    var group = jQuery(this).data("group");

    if (group !== "" && group !== null) {
        jQuery(".groupedCheckbox[data-group='" + group + "']").prop('checked', false);
        jQuery(this).prop('checked', true);

        // jQuery("#" + category + "_all").prop('checked', true);
    }


});


function activateMailForm(selected) {

    jQuery(".edit-content-form").hide();
    jQuery("#" + selected + "_form").show();

    var hasAttachments = jQuery(".attachment_list:visible li").length;

    if (hasAttachments) {
        jQuery(".attachment_header").show();
        jQuery(".attachment_header_2").show();
        jQuery(".documentPreview").show();
    } else {
        jQuery(".attachment_header").hide();
        jQuery(".attachment_header_2").hide();
        jQuery(".documentPreview").show(); // Anschreiben als pdf ist drin, für fachkundige Stellungnahme etwa notwendig
    }

}

$('#activePdfCategoryWrap select.selectpicker').on('change', function () {


    var selected = $('.selectpicker option:selected').val();

    activateMailForm(selected);


});


function getQueryParams(qs) {
    qs = qs.split("+").join(" ");
    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}


jQuery(document).ready(function () {
    //$('#activePdfCategoryWrap select.selectpicker').select("intern_before");
    var $_GET = getQueryParams(document.location.search);

    if (typeof $_GET["activemail"] !== 'undefined') {
        $('#activePdfCategoryWrap select.selectpicker').selectpicker('val', $_GET["activemail"]);
        var selected = $('.selectpicker option:selected').val();
        activateMailForm(selected);
    }

});

