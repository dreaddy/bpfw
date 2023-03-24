var imgCropperArray = [];

var currentTabPage = 1;

function activateTab(tab) {
    var dialogid = ".manipulateDataDialog";
    if (getIdOfCurrentDialog() !== undefined)
        dialogid = "#" + getIdOfCurrentDialog() + ".manipulateDataDialog";


    var cssid = dialogid + ' .nav-tabs a[href="#' + tab + '"]';

    jQuery(cssid).tab('show');


    // $tabcontentid = "tab-".($this->editmode?"edit":"add")."-".$page."-content";

}


function setPaginationCssFor(oldpage, newpage, makeTabActive) {

    var isEdit = jQuery("#" + getIdOfCurrentDialog() + ".editEntryDialog").is(":visible");

    // TODO: add or edit check

    var tabcontentid = "tab-" + getIdOfCurrentDialog() + "-" + newpage + "-content";
    var lastPage = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog #listviewForm').data("lastpage");


    if (makeTabActive)
        activateTab(tabcontentid);

    var dialog_id =
        "#" + getIdOfCurrentDialog();

    if (newpage > 1) {
        jQuery(dialog_id + " .defaultlistmodalview .directionButton.prevButton").show();
    } else {
        jQuery(dialog_id + " .defaultlistmodalview .directionButton.prevButton").hide();
    }


    if (jQuery(dialog_id + " .defaultlistmodalview .directionButton.continueButton").length > 0) {

        if (newpage !== lastPage) {

            jQuery(dialog_id + " .defaultlistmodalview .directionButton.continueButton").show();
            if (jQuery(dialog_id + " .defaultlistmodalview .directionButton.continueButton").length > 0) {
                jQuery(dialog_id + " .defaultlistmodalview .addAction").hide();
                jQuery(dialog_id + " .defaultlistmodalview .editAction").hide();
            }

        } else {

            jQuery(dialog_id + " .defaultlistmodalview .addAction").show();
            jQuery(dialog_id + " .defaultlistmodalview .editAction").show();
            jQuery(dialog_id + " .defaultlistmodalview .directionButton.continueButton").hide();

        }

    }

    currentTabPage = newpage;

}


function startEdit(id, page = false, isRootHierachy = false, temptable = 0) {

    //  e.preventDefault();
    // alert(jQuery(this).data("id"));
    // var id = jQuery(this).data("id");

    if (isRootHierachy) {
        CloseAllDialogues();
    }

    var filter = getQueryVariable("filter");

    if (filter === false) {
        filter = '';
    }

    var currentPage = "";

    if (page === false) {
        currentPage = getQueryVariable('p');
        if (currentPage === false) currentPage = "";
    } else {
        currentPage = page;
    }

    var hierachy = getCurrentDialogHierachy() + 1;


    if (hierachy > 0) {
        filter = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog').data("rowid");
    }


    if (temptable) {
        filter = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog' + " table[data-model=" + page + "]").data("filter");
    }

    var url = "?p=" + currentPage + "&ajaxCall=true&command=createEditDialog&filter=" + filter;

    jQuery.ajax({
        type: 'POST',
        cache: false,
        url: url,
        data: {filter: filter, hierachy: hierachy, rowid: id, temptable: temptable ? 1 : 0},
        async: true
    }).done(function (data) {

        var newdialog = undefined;
        try {
            newdialog = JSON.parse(data);
        } catch (exc) {
            alert("Can't create edit dialog:" + exc + "json: " + data);
            return;
        }

        var identifier = newdialog.css_id;

        jQuery("#" + identifier + '.manipulateDataDialog a#tab-' + getIdOfCurrentDialog() + '-1').tab('show');

        jQuery("#dialogWrapper").append(newdialog.html);

        var url = "?p=" + currentPage + "&ajaxCall=true&edit=" + id + "&command=getEditEntryHtml&filter=" + filter + "&id=" + id;

        var layer0identifier = identifier;
        if (jQuery(".manipulateDataDialog[data-hierachy='0']").length > 0) {
            layer0identifier = jQuery(".manipulateDataDialog[data-hierachy='0']").attr('id');
        }

        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: url,
            data: {
                id: id,
                filter: filter,
                parentformidentifier: identifier,
                layer0identifier: layer0identifier,
                hierachy: hierachy,
                rowid: id,
                temptable: temptable ? 1 : 0
            },
            async: true
        })
            .done(function (data) {

                // location.reload(); // TODO: irgendwann mal mit html updates lösen. Dann muss aber auch die ganze Checkbox-Sache neu generiert werden
                // window.location = window.location.href;


                jQuery("#" + identifier + ".editEntryDialog .modal-body .dialog-form-content").html(data);


                if (typeof (bpfw_refeshComponentScripts) === "function") {
                    bpfw_refeshComponentScripts(identifier);
                }

                // jQuery('.selectpicker').selectpicker('refresh');

                bpfw_refreshEditSubmitButtonFormHandler(document.querySelector("#" + identifier + '.editEntryDialog form'));

                bpfwRandomizeNames(".removeAutocomplete");

                handleBpfwEvent("startEdit", {model: currentPage, rowid: id, temptable: temptable, filter: filter});

            });

        layer0identifier = identifier;
        if (jQuery(".manipulateDataDialog[data-hierachy='0']").length > 0) {
            layer0identifier = jQuery(".manipulateDataDialog[data-hierachy='0']").attr('id');
        }

        var title_url = "?p=" + currentPage + "&ajaxCall=true&edit=" + id + "&command=getEditTitle&filter=" + filter + "&id=" + id;
        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: title_url,
            data: {
                id: id,
                filter: filter,
                command: "getEditTitle",
                rowid: id,
                parentformidentifier: identifier,
                layer0identifier: layer0identifier,
                temptable: temptable ? 1 : 0
            },
            async: true
        })
            .done(function (data) {

                jQuery("#" + identifier + ".editEntryDialog .modal-title").text(JSON.parse(data));

            });


        jQuery('.editEntryDialog').data('id', id);

        ///    

        if (jQuery("#" + identifier + '.editEntryDialog').data("shownonmodal") !== 1) {

            jQuery("#" + identifier + '.editEntryDialog').modal('show');

            if (isRootHierachy) {
                setPaginationCssFor(-1, 1, true);
            }

        } else {

            jQuery("#" + identifier + '.editEntryDialog').show();

            var ithis = jQuery("#" + identifier + '.editEntryDialog');

            disableDialog(getIdOfCurrentDialog());

            dialogShown(ithis);
        }


        jQuery("#" + identifier + ".editEntryDialog .errorMessages").html("");

    });

    return false;

}


function startIFrameDialog(id, iframeurl = "", page = false, isRootHierachy = false, filter_override = null) {

    // alert(jQuery(this).data("id"));

    if (isRootHierachy) {
        CloseAllDialogues();
    }

    var currentPage = "";
    if (page === false) {
        currentPage = getQueryVariable('p');
        if (currentPage === false) currentPage = "";
    } else {
        currentPage = page;
    }

    var hierachy = getCurrentDialogHierachy() + 1;

    if (hierachy > 0) {
        if (filter_override == null) {
            filter = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog').data("rowid");
        }
    }

    filter = filter_override;

    var url = "?p=" + currentPage + "&ajaxCall=true&command=createIFrameDialog&filter=" + filter;

    jQuery.ajax({
        type: 'POST',
        cache: false,
        url: url,
        data: {filter: filter, hierachy: hierachy, iframeurl: iframeurl},
        async: true
    }).done(function (data) {


        var newdialog = undefined;
        try {
            newdialog = JSON.parse(data);
        } catch (exc) {
            alert("Can't create IFRame dialog:" + exc + "json: " + data);
            return;
        }

        var identifier = newdialog.css_id;
        jQuery("#dialogWrapper").append(newdialog.html);


        if (jQuery("#" + identifier + '.manipulateDataDialog').data("shownonmodal") !== 1) {
            jQuery("#" + identifier + '.manipulateDataDialog').modal('show');

            if (isRootHierachy) {
                setPaginationCssFor(-1, 1, true);
            }

        } else {
            jQuery("#" + identifier + '.manipulateDataDialog').show();

            disableDialog(getIdOfCurrentDialog());

            var ithis = jQuery("#" + identifier + '.manipulateDataDialog');

            dialogShown(ithis);


        }

        handleBpfwEvent("startIFrameDialog", {});

        // jQuery("#" + identifier + ".manipulateDataDialog .errorMessages").html("");

    });

    return false;


}


function startDuplicate(id, page = false, isRootHierachy = false, temptable = 0) {

    // alert(jQuery(this).data("id"));

    if (isRootHierachy) {
        CloseAllDialogues();
    }

    var filter = getQueryVariable("filter");

    var currentPage = "";
    if (page === false) {
        currentPage = getQueryVariable('p');
        if (currentPage === false) currentPage = "";
    } else {
        currentPage = page;
    }

    var hierachy = getCurrentDialogHierachy() + 1;

    if (hierachy > 0) {
        filter = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog').data("rowid");
    }

    if (temptable) {
        filter = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog' + " table[data-model=" + page + "]").data("filter");
        // alert("123findme filter is " + filter );
    }
    // TODO: bug

    var url = "?p=" + currentPage + "&ajaxCall=true&command=createAddDialog&filter=" + filter;

    jQuery.ajax({
        type: 'POST',
        cache: false,
        url: url,
        data: {filter: filter, hierachy: hierachy, rowid: id, temptable: temptable ? 1 : 0},
        async: true
    }).done(function (data) {

        var newdialog = undefined;
        try {
            newdialog = JSON.parse(data);
        } catch (exc) {
            alert("Can't create edit dialog:" + exc + "json: " + data);
            return;
        }

        var identifier = newdialog.css_id;
        jQuery("#dialogWrapper").append(newdialog.html);


        var url = "?p=" + currentPage + "&ajaxCall=true&duplicate=" + id + "&command=getAddEntryHtml&filter=" + filter;

        jQuery("#" + identifier + ".manipulateDataDialog a#tab-" + getIdOfCurrentDialog() + "-1").tab('show');


        var layer0identifier = identifier;
        if (jQuery(".manipulateDataDialog[data-hierachy='0']").length > 0) {
            layer0identifier = jQuery(".manipulateDataDialog[data-hierachy='0']").attr('id');
        }

        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: url,
            data: {
                filter: filter,
                parentformidentifier: identifier,
                hierachy: hierachy,
                rowid: id,
                layer0identifier: layer0identifier,
                temptable: temptable ? 1 : 0
            },
            async: true
        })
            .done(function (data) {


                jQuery("#" + identifier + ".manipulateDataDialog .modal-body .dialog-form-content").html(data);


                if (typeof (bpfw_refeshComponentScripts) == "function")
                    bpfw_refeshComponentScripts(identifier);

                bpfw_refreshAddSubmitButtonFormHandler(document.querySelector("#" + identifier + '.manipulateDataDialog form'));


                bpfwRandomizeNames(".removeAutocomplete");

                handleBpfwEvent("startDuplicate", {
                    model: currentPage,
                    rowid: id,
                    temptable: temptable,
                    filter: filter
                });

            });

        var title_url = "?p=" + currentPage + "&ajaxCall=true&duplicate=" + id + "&command=getDuplicateTitle&filter=" + filter;
        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: title_url,
            data: {filter: filter, command: "getDuplicateTitle", rowid: id, temptable: temptable ? 1 : 0},
            async: true
        })
            .done(function (data) {

                jQuery("#" + identifier + ".manipulateDataDialog .modal-title").text(JSON.parse(data));

            });


        if (jQuery("#" + identifier + '.manipulateDataDialog').data("shownonmodal") !== 1) {
            jQuery("#" + identifier + '.manipulateDataDialog').modal('show');

            if (isRootHierachy) {
                setPaginationCssFor(-1, 1, true);
            }

        } else {
            jQuery("#" + identifier + '.manipulateDataDialog').show();

            disableDialog(getIdOfCurrentDialog());

            var ithis = jQuery("#" + identifier + '.manipulateDataDialog');

            dialogShown(ithis);


        }

        jQuery("#" + identifier + ".manipulateDataDialog .errorMessages").html("");

    });

    return false;

}


function bpfw_refreshAddSubmitButtonFormHandler(form) {


}

function bpfw_refreshEditSubmitButtonFormHandler(form) {


}

jQuery('body').on("submit", ".addEntryDialog form", function (e) {

    e.preventDefault();

    var page = jQuery("#" + getIdOfCurrentDialog()).data("model");
    submitAddDialog(page);

});


function destroyForm(css_id_parent) {

    jQuery("#" + css_id_parent).remove();

}

jQuery('body').on("submit", ".editEntryDialog form", function (e) {

    e.preventDefault();

    var page = jQuery("#" + getIdOfCurrentDialog()).data("model");
    submitEditDialog(page);


    //var css_id_parent = jQuery(this).find("#hiddensubmit").data("css_id");

    ///destroyForm(css_id_parent);


});

function CloseAllDialogues() {

    while (getIdOfCurrentDialog() !== undefined) {

        var diaid = getIdOfCurrentDialog();

        if (diaid !== undefined) {

            if (jQuery("#" + diaid + '.manipulateDataDialog').data("shownonmodal") !== 1) {
                jQuery("#" + diaid + ".manipulateDataDialog").modal('hide');
            } else {
                jQuery("#" + diaid + ".manipulateDataDialog").hide();

                var ithis = jQuery("#" + diaid + '.manipulateDataDialog');

                dialogHidden(ithis);

            }

        }


    }

}


function submitAddDialog(page = false) {

    bpfwRestoreNames(".removeAutocomplete");

    if (typeof tinyMCE !== 'undefined') {
        tinyMCE.triggerSave();
    }

    var formdata = new FormData(jQuery("#" + getIdOfCurrentDialog() + ".addEntryDialog").find("form")[0]);

    //  alert(imgCropperArray.length);

    var croppercount = jQuery("#" + getIdOfCurrentDialog() + ".addEntryDialog" + ' .image_cropper').length;

    if (croppercount === 0) {
        sendAddEntries(page, formdata);
    } else {

        var cropperentries = jQuery("#" + getIdOfCurrentDialog() + ".addEntryDialog .image_cropper").length;

        jQuery("#" + getIdOfCurrentDialog() + ".addEntryDialog .image_cropper").each(function (index) {


            var ithis = this;
            var name = jQuery(ithis).data("name");

            var viewport_type = jQuery(ithis).data("viewport_type");

            var extension = jQuery("input[data-name='" + name + "']").val().split('.').pop();
            var outputtype = "png";

            if (viewport_type !== "circle" && (extension === "jpg" || extension === "jpg")) {
                outputtype = "jpeg";
                extension = "jpg";
            } else {
                extension = "png";
            }

            // get add croppie Image blob. TODO: check if changed
            if (jQuery(ithis).hasClass("croppie_initialized")) {

                jQuery(ithis).croppie('result', {
                    type: 'blob',
                    size: 'viewport',
                    format: outputtype // or webp
                }).then(function (response) {

                    if (response !== null) {
                        // TODO: response kann auch leer sein, wenn es kein (neues)Bild gibt...


                        formdata.set(name, response, name + '.' + extension);

                        var preview = document.getElementById(name + '_previewImage');
                        // var preview2 = document.getElementById(name + '_previewImage_label');

                        var objectURL = URL.createObjectURL(response);
                        if (preview !== null) {
                            preview.src = objectURL;
                        }

                        // jQuery("#" + name + '_previewImage').show();

                        //alert(objectURL);

                    } else {

                        // bild nicht über croppie geändert
                        // formdata.delete(name);

                    }


                    cropperentries--;

                    if (cropperentries === 0) {

                        sendAddEntries(page, formdata);

                    }


                });

            } else {

                cropperentries--;

                if (cropperentries === 0) {

                    sendAddEntries(page, formdata);

                }

            }

        });
    }
}

function formatErrorJson(errormsgs) {

    var retval = "<ul class='bpfw_errorlist'>";

    var errFound = false;

    try {
        var errors = JSON.parse(errormsgs);

        if(errors === '' || errors === null || errors.length === 0){
            throw new Exception("error parsing errors: " + errormsgs);
        }

        for (var i = 0; i < errors.length; i++) {

            var error = errors[i];

            var errtype = "Fehler: ";

            if (error.type == "warning") {
                errtype = "Warnung: ";
            }

            if (error.type == "info") {
                errtype = "Information: ";
            }

            if (error.detail == undefined || error.detail.key == undefined) {
                errFound = true;
                retval += "<li class='msgtype_" + errtype + "'>" + errtype + error.msg + "</li>";

            }

            // errcontainer.append("<li>"+error.msg+"</li>");

        }

    } catch (ex) {

        retval += "<li class='msgtype_" + "Fehler: " + "'>" + "Fehler: " + errormsgs + "</li>";

    }

    retval += "</ul>";

    if (!errFound) {
        retval = "";
    }

    return retval;

}

function processErrors(errormsgs) {

    // if(errormsgs == undefined || errormsgs.trim() == "")return;
    // alert(errormsgs);

    var errors = JSON.parse(errormsgs);

    var errcontainer = jQuery("#" + getIdOfCurrentDialog() + ".manipulateDataDialog .errorMessages");

    errcontainer.html("");

    var firstTabWithError = -2;

    if (errors !== undefined) {

        errcontainer.hide();

        var errorContent = formatErrorJson(errormsgs);


        errcontainer.append("<ul>");

        var hasTopError = false;

        jQuery(".component").removeClass("component_has_error");
        jQuery(".component .component_errorcontainer").remove();

        for (var i = 0; i < errors.length; i++) {

            var error = errors[i];

            if (error.detail == undefined || error.detail.key == undefined) {
                // errcontainer.find("ul").append("<li>" + error.msg + "</li>");
                hasTopError = true;
            } else {

                if (firstTabWithError == -2)
                    firstTabWithError = errors[i].detail.page;

                var componentWithError = jQuery(".component.field_" + error.detail.key + "");


                componentWithError.addClass("component_has_error");
                componentWithError.data("errmsg", error.msg);

                componentWithError.append("<div class='component_errorcontainer errorMessages'>" + error.msg + "</div>");

            }


            // errcontainer.append("<li>"+error.msg+"</li>");

        }

        // errcontainer.append("</ul>");

        errcontainer.html(errorContent);

        if (hasTopError) {
            errcontainer.show();
        }

    } else {

        errcontainer.html(data);

    }

    currentTabPage = -1; //jQuery(e.target).data("page");

    if (currentTabPage !== firstTabWithError) {
        setPaginationCssFor(currentTabPage, firstTabWithError, true);
    }

}

function sendAddEntries(page, formdata) {


    var filter = getQueryVariable('filter');
    if (filter === false) {
        filter = "";
    }

    var currentPage = "";
    if (page === false) {
        currentPage = getQueryVariable('p');
        if (currentPage === false) {
            currentPage = "";
        }
    } else {
        currentPage = page;
    }

    if (getCurrentDialogHierachy() > 0) {
        filter = jQuery("#" + getIdOfParentDialog() + '.manipulateDataDialog').data("rowid");
    }

    var temptable = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog').data("temptable");


    //alert(filter);
    //alert(jQuery("#" + getIdOfParentDialog() + '.manipulateDataDialog').data("rowid"));
    // alert(getIdOfCurrentDialog());
    var url = "?p=" + currentPage + "&ajaxCall=true&temptable=" + temptable + "&openAdd=true&dialogid=" + getIdOfCurrentDialog() + "&command=AddEntrySubmit&filter=" + filter;

    if (typeof preprocessAddUrlBeforeSend === "function") {
        url = preprocessAddUrlBeforeSend(url);
    }

    if (typeof preprocessFormDataBeforeSend === "function") {
        formdata = preprocessFormDataBeforeSend(formdata);
    }

    jQuery.ajax({
        type: 'POST',
        url: url,
        data: formdata,
        processData: false,
        contentType: false,
        cache: false,
        success: function (data) {

            if (data !== null && data !== "" && data !== "ok") {

                processErrors(data);

            } else {

                // alert(jQuery("#" + getIdOfCurrentDialog() + ".addEntryDialog " + "#send_signature_select").hasClass("checkOn"));

                if (jQuery("#" + getIdOfCurrentDialog() + ".addEntryDialog " + "#send_signature_select").hasClass("checkOn")) {
                    alert("Link zur Unterschrift wurde an den Kunden per EMail an " + jQuery("#" + getIdOfCurrentDialog() + ".addEntryDialog " + "#send_signature_address").val() + " zugestellt. Bitte Seite neu laden und Unterschrift prüfen, sobald der Kunde unterschrieben hat.");
                }

                if (typeof bpfw_refreshPageAfterDataChange === "function") {
                    bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());
                }

                jQuery("#" + getIdOfCurrentDialog() + ".manipulateDataDialog .errorMessages").html("");


                if (jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog').data("shownonmodal") !== 1) {
                    jQuery("#" + getIdOfCurrentDialog() + ".manipulateDataDialog").modal('hide');
                } else {
                    /* var identifier = getIdOfCurrentDialog();

                   jQuery("#" + identifier + ".manipulateDataDialog").hide();
                  
                    var ithis = jQuery("#" + identifier + '.manipulateDataDialog');
                    dialogHidden(ithis);*/

                    // alert("Daten wurden gespeichert");

                }

            }

            if (typeof refreshAfterJsEvent === "function") {
                refreshAfterJsEvent();
            }

            handleBpfwEvent("AddEntrySubmit", {model: currentPage, temptable: temptable, filter: filter});

        }

    });

}

function submitEditDialog(page = false) {

    bpfwRestoreNames(".removeAutocomplete");

    if (typeof tinyMCE !== 'undefined') {
        tinyMCE.triggerSave();
    }

    var formdata = new FormData(jQuery("#" + getIdOfCurrentDialog() + ".editEntryDialog").find("form")[0]);


    // alert(imgCropperArray.length);

    // alert(imgCropperArray.length);


    var croppercount = jQuery("#" + getIdOfCurrentDialog() + ".editEntryDialog" + ' .image_cropper').length;

    if (croppercount === 0) {

        sendEditEntries(page, formdata);

    } else {

        var cropperentries = jQuery("#" + getIdOfCurrentDialog() + ".editEntryDialog .image_cropper").length;


        jQuery("#" + getIdOfCurrentDialog() + ".editEntryDialog" + ' .image_cropper').each(function (index) {

            var ithis = this;

            var name = jQuery(ithis).data("name");
            var rowkey = jQuery(ithis).data("rowkey");

            var viewport_type = jQuery(ithis).data("viewport_type");


            var extension = jQuery("input[data-name='" + name + "']").val().split('.').pop();
            var outputtype = "png";

            if (viewport_type !== "circle" && (extension === "jpg" || extension === "jpg")) {
                outputtype = "jpeg";
                extension = "jpg";
            } else {
                extension = "png";
            }

            // get croppie Image blob. TODO: check if changed
            if (jQuery(ithis).hasClass("croppie_initialized")) {

                jQuery(ithis).croppie('result', {
                    type: 'blob',
                    size: 'viewport',
                    format: outputtype // or webp
                }).then(function (response) {


                    if (response !== null) {

                        // TODO: response kann auch leer sein, wenn es kein (neues)Bild gibt...

                        formdata.set(name, response, name + '.' + extension);
                        var preview = document.getElementById(name + '_previewImage');
                        var preview2 = document.getElementById(name + '_previewImage' + rowkey + '_label');

                        var objectURL = URL.createObjectURL(response);

                        if (preview !== null) {
                            preview.src = objectURL;
                        }

                        if (preview2 !== null) {
                            preview2.src = objectURL;
                        }

                        // jQuery("#" + name + '_previewImage').show();

                        //alert(objectURL);

                    } else {

                        // bild nicht über croppie geändert
                        // formdata.delete(name);

                    }


                    cropperentries--;

                    if (cropperentries === 0) {

                        sendEditEntries(page, formdata);

                    }


                });

            } else {

                cropperentries--;

                if (cropperentries === 0) {

                    sendEditEntries(page, formdata);

                }

            }

        });

    }

    /*
    $image_crop.croppie('result', {
        type: 'blob',
        size: 'viewport'
    }).then(function (response) {


    // remove normal image, replace with croppie image
        //  formdata.delete("photo");
        formdata.set('photo', response, 'photo.png');

        // formdata["photo"].value = response;


        if (typeof preprocessFormDataBeforeSend === "function") {
            formdata = preprocessFormDataBeforeSend(formdata);
        }

    });*/

}

function sendEditEntries(page, formdata) {


    //var id = jQuery("#" + getIdOfCurrentDialog() + '.editEntryDialog').data('id'); // jQuery(this).data("id");

    var rowid = jQuery("#" + getIdOfCurrentDialog() + '.editEntryDialog').data('rowid'); // jQuery(this).data("id");

    var filter = getQueryVariable('filter');
    if (filter === false) filter = "";

    var currentPage = "";
    if (page === false) {
        currentPage = getQueryVariable('p');
        if (currentPage === false) currentPage = "";
    } else {
        currentPage = page;
    }

    var temptable = jQuery("#" + getIdOfCurrentDialog() + '.editEntryDialog').data('temptable'); // jQuery(this).data("id");

    // alert(rowid);

    if (getCurrentDialogHierachy() > 0) {
        filter = jQuery("#" + getIdOfParentDialog() + '.manipulateDataDialog').data("rowid");
    }

    var url = "?p=" + currentPage + "&ajaxCall=true&command=EditEntrySubmit&filter=" + filter + "&id=" + rowid + "&edit=" + rowid + "&temptable=" + temptable;

    if (typeof preprocessEditUrlBeforeSend === "function") {
        url = preprocessEditUrlBeforeSend(url);
    }

    if (typeof preprocessFormDataBeforeSend === "function") {
        formdata = preprocessFormDataBeforeSend(formdata);
    }

    jQuery.ajax({
        type: 'POST',
        url: url,
        data: formdata,
        processData: false,
        contentType: false,
        cache: false,
        success: function (data) {
            if (data !== null && data !== "" && data !== "") {

                //errorDialog("Fehler aufgetreten", data);

                // jQuery("#" + getIdOfCurrentDialog() + ".editEntryDialog .errorMessages").html(data);

                processErrors(data);

            } else {
                if (typeof bpfw_refreshPageAfterDataChange === "function")
                    bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());

                if (jQuery("#" + getIdOfCurrentDialog() + '.editEntryDialog').data("shownonmodal") !== 1) {
                    jQuery("#" + getIdOfCurrentDialog() + ".editEntryDialog").modal('hide');
                } else {
                    /*  var identifier = getIdOfCurrentDialog();
  
                      jQuery("#" + identifier + ".editEntryDialog").hide();
  
                      var ithis = jQuery("#" + identifier + '.editEntryDialog');
                      dialogHidden(ithis);*/

                    // alert("Daten wurden gespeichert");

                }


                jQuery("#" + getIdOfCurrentDialog() + ".editEntryDialog .errorMessages").html("");

            }


            if (typeof refreshAfterJsEvent === "function") {
                refreshAfterJsEvent();
            }

            handleBpfwEvent("EditEntrySubmit", {
                model: currentPage,
                rowid: rowid,
                temptable: temptable,
                filter: filter
            });

        }

    });

}


function startAdd(e, buttonid) {

    var filter = getQueryVariable("filter");


    page = jQuery(buttonid).data("model");

    var currentPage = false;
    if (page === false || page === "" || page === undefined) {
        currentPage = getQueryVariable('p');
        if (currentPage === false) currentPage = "";
    } else {
        currentPage = page;
    }

    // console.log(JSON.stringify(e.selector));
    //alert(jQuery(buttonid).hasClass("mainnavigation_button"));

    if (jQuery(buttonid).hasClass("root_button")) {
        CloseAllDialogues();

    }

    var hierachy = getCurrentDialogHierachy() + 1;
    if (hierachy > 0) {
        filter = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog').data("rowid");
    }

    temptable = 0;
    if (jQuery(buttonid).data("temptable") === 1) {

        temptable = 1;

        if (jQuery(buttonid).data("filterid") !== undefined) {
            filter = jQuery(buttonid).data("filterid");
        }

    }


    var url = "?p=" + currentPage + "&ajaxCall=true&command=createAddDialog&filter=" + filter;

    jQuery.ajax({
        type: 'POST',
        cache: false,
        url: url,
        data: {filter: filter, hierachy: hierachy, rowid: 0, temptable: temptable ? 1 : 0},
        async: true
    }).done(function (data) {

        // alert(jQuery(this).data("id"));
        var newdialog = undefined;
        try {
            newdialog = JSON.parse(data);
        } catch (exc) {
            alert("Can't create dialog:" + exc + "json: " + data);
            return;
        }

        var identifier = newdialog.css_id;
        jQuery("#dialogWrapper").append(newdialog.html);

        var layer0identifier = identifier;
        if (jQuery(".manipulateDataDialog[data-hierachy='0']").length > 0) {
            layer0identifier = jQuery(".manipulateDataDialog[data-hierachy='0']").attr('id');
        }

        var url = "?p=" + currentPage + "&ajaxCall=true&openAdd=true&command=getAddEntryHtml&filter=" + filter;

        jQuery("#" + identifier + ".manipulateDataDialog a#tab-" + getIdOfCurrentDialog() + "-1").tab('show');

        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: url,
            data: {
                filter: filter,
                hierachy: hierachy,
                parentformidentifier: identifier,
                layer0identifier: layer0identifier,
                rowid: 0,
                temptable: temptable ? 1 : 0
            },
            async: true
        })
            .done(function (data) {

                jQuery("#" + identifier + ".addEntryDialog .modal-body .dialog-form-content").html(data);

                if (typeof (bpfw_refeshComponentScripts) == "function")
                    bpfw_refeshComponentScripts(identifier);
                bpfw_refreshAddSubmitButtonFormHandler(document.querySelector('.addEntryDialog form'));

                bpfwRandomizeNames(".removeAutocomplete");

                handleBpfwEvent("startAdd", {model: currentPage, rowid: 0, temptable: temptable, filter: filter});

            });

        var layer0identifier = identifier;
        if (jQuery(".manipulateDataDialog[data-hierachy='0']").length > 0) {
            layer0identifier = jQuery(".manipulateDataDialog[data-hierachy='0']").attr('id');
        }

        var title_url = "?p=" + currentPage + "&ajaxCall=true&command=getAddTitle&filter=" + filter;
        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: title_url,
            data: {
                filter: filter,
                command: "getAddTitle",
                rowid: 0,
                parentformidentifier: identifier,
                layer0identifier: layer0identifier,
                temptable: temptable ? 1 : 0
            },
            async: true
        })
            .done(function (data) {

                jQuery("#" + identifier + ".manipulateDataDialog .modal-title").text(JSON.parse(data));

            });

        // setPaginationCssFor(-1, 1, true);


        if (jQuery("#" + identifier + '.manipulateDataDialog').data("shownonmodal") !== 1) {
            jQuery("#" + identifier + '.manipulateDataDialog').modal('show');

            //  setPaginationCssFor(-1, 1, true);

        } else {
            var ithis = jQuery("#" + identifier + '.manipulateDataDialog');

            jQuery("#" + identifier + '.manipulateDataDialog').show();

            disableDialog(getIdOfCurrentDialog());

            dialogShown(ithis);

        }
        jQuery("#" + identifier + ".manipulateDataDialog .errorMessages").html("");


    });
}

function addDialogwrapperContainerIfNotExisting() {


    if (jQuery("#dialogWrapper").length == 0) {
        jQuery("body").prepend('<div id="dialogWrapper" data-current-hierachy=" - 1"></div >');
    }

}


jQuery(document).ready(function () {


    addDialogwrapperContainerIfNotExisting();


    jQuery("body").on("click", ".add-button",

        function (e) {

            e.preventDefault();
            startAdd(e, this);

        });


    jQuery("body").on("click", ".addEntryDialog .bpfwbutton.btn-primary", function (e) {
        e.preventDefault();
        jQuery("#" + getIdOfCurrentDialog() + '.addEntryDialog form').submit();
        //jQuery("#" + getIdOfCurrentDialog() + '.addEntryDialog form').find("#hiddensubmit").click();
    });


    jQuery("body").on("click", ".editEntryDialog .bpfwbutton.btn-primary", function (e) {
        e.preventDefault();
        jQuery("#" + getIdOfCurrentDialog() + '.editEntryDialog form').submit();
        //jQuery("#" + getIdOfCurrentDialog() + '.editEntryDialog form').find("#hiddensubmit").click();
    });


    jQuery("body").on("click", ".defaultlistmodalview .directionButton.continueButton", function (e) {
        e.preventDefault();
        var isEdit = jQuery("#" + getIdOfCurrentDialog() + ".manipulateDataDialog").is(":visible");

        var lastPage = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog #listviewForm').data("lastpage");
        if (isEdit) {
            lastPage = jQuery("#" + getIdOfCurrentDialog() + '.manipulateDataDialog #listviewForm').data("lastpage");
        }

        if (currentTabPage !== lastPage) {
            currentTabPage++;
            setPaginationCssFor(currentTabPage - 1, currentTabPage, true);
        }

    });


    jQuery("body").on("click", ".defaultlistmodalview .directionButton.prevButton", function (e) {
        e.preventDefault();
        if (currentTabPage > 0) {
            currentTabPage--;
            setPaginationCssFor(currentTabPage + 1, currentTabPage, true);
        }

    });


    jQuery('body').on('shown.bs.tab', '.manipulateDataDialog a[data-toggle="tab"]', function (e) {

        //var target = $(e.target).attr("href") // activated tab
        //alert(target);

        currentTabPage = jQuery(e.target).data("page");
        setPaginationCssFor(-1, currentTabPage, false);

        var dialog_id =
            "#" + getIdOfCurrentDialog();

        if (jQuery(dialog_id + " .defaultlistmodalview .directionButton.continueButton").length <= 0) {

            ///  jQuery(dialog_id+" .defaultlistmodalview .addAction").show();
            ///  jQuery(dialog_id+" .defaultlistmodalview .editAction").show();
        }

    });

    var dialog_id =
        "#" + getIdOfCurrentDialog();

    if (jQuery(dialog_id + " .defaultlistmodalview .directionButton.continueButton").length <= 0) {
        ///  jQuery(dialog_id+" .defaultlistmodalview .addAction").show();
        ///  jQuery(dialog_id+" .defaultlistmodalview .editAction").show();
    }

    /*jQuery('body').on('shown.bs.modal', ".modal", function (event) {
        alert("shown");
    });*/

    jQuery('body').on('hide.bs.modal', ".editEntryDialog, .addEntryDialog", function (event) {

    });

    jQuery('body').on('hidden.bs.modal', ".manipulateDataDialog", function (event) {

        //alert("hidden");
        //alert(jQuery(this).attr('id'));
        dialogHidden(this);

    });

    jQuery('body').on('show.bs.modal', ".manipulateDataDialog", function (event) {
        disableDialog(getIdOfCurrentDialog());
    });

    jQuery('body').on('shown.bs.modal', ".manipulateDataDialog", function (event) {

        //alert("hidden");
        //alert(jQuery(this).attr('id'));

        //parentOfOpenedDialog = currentlyOpenedDialog;
        //currentlyOpenedDialog = jQuery(this);
        dialogShown(this);

    });


});

function getIdOfCurrentDialog() {
    if (layerDialogues.length > 0)
        return layerDialogues[layerDialogues.length - 1];

    return undefined;
}

function getIdOfParentDialog() {
    if (layerDialogues.length > 1)
        return layerDialogues[layerDialogues.length - 2];

    return undefined;
}

function dialogShown(ithis) {

    disableDialog(getIdOfCurrentDialog());

    jQuery("#dialogWrapper").data("current-hierachy", jQuery("#dialogWrapper").data("current-hierachy") + 1);

    layerDialogues[jQuery(ithis).data("hierachy")] = jQuery(ithis).attr("id");

}

function dialogHidden(ithis) {

    var keyid = jQuery(ithis).data('hierachy');
    layerDialogues.splice(keyid, 1);
    // parentOfOpenedDialog = currentlyOpenedDialog;
    // myArray.splice(key, jQuery(this).data('hierachy'));

    jQuery("#dialogWrapper").data("current-hierachy", jQuery("#dialogWrapper").data("current-hierachy") - 1);

    destroyForm(jQuery(ithis).attr('id'));

    enableDialog(getIdOfCurrentDialog());

    var diaid = getIdOfCurrentDialog();

    if (jQuery("#" + diaid + '.manipulateDataDialog').data("shownonmodal") === 1) {
        jQuery('body').removeClass('modal-open');
    } else {
        if (diaid !== undefined || errordialog_shown || confirmfialog_shown) {
            jQuery('body').addClass('modal-open');
        }
    }

}

function disableDialog(id) {
    if (id !== undefined)
        jQuery("#" + id + " .dialog_disabled_overlay").show();
}

function enableDialog(id) {

    if (id !== undefined)
        jQuery("#" + id + " .dialog_disabled_overlay").hide();

}


var layerDialogues = [];

function getCurrentDialogHierachy() {
    return jQuery("#dialogWrapper").data("current-hierachy");
}


jQuery('.quickupload_element .bpfw_fileinput_quickupload .bpfw_quickuploader_extended').on('fileuploaded', function (event, previewId, index, fileId) {
    //console.log('File uploaded', previewId, index, fileId);

    if (typeof bpfw_refreshPageAfterDataChange === "function")
        bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());

    // $("#quickupload .bpfw_fileinput_quickupload").fileinput('reset');
});


