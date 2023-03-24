var adminTables = null;
var adminTablesAjax = null;


function bpfw_hasAjaxTable() {
    return jQuery('.hasNoAjaxTable').length <= 0;
}

function bpfw_reload_admintableAjax(currentDialogID) {
    // Haupttabelle
    if (adminTablesAjax !== null) {
        if (jQuery('#adminTable_ajax').is(":visible")) {
            adminTablesAjax.ajax.reload(null, false);
        }

    }

    // datatable Components 
    // TODO: könnte nicht immer sinnvoll sein auf is visible zu prüfen, sofern die Haupttabelle die Daten darin ändern kann. 
    // Dann muss da noch ein is visible event rein mit reload.
    jQuery(".datatable_component").each(function (e) {
        if (jQuery(this).is(":visible")) {
            jQuery(this).DataTable().ajax.reload(null, false);
        }
    });

}

function bpfw_refreshPageAfterDataChange(currentDialogID) {

    if (bpfw_hasAjaxTable()) {
        bpfw_reload_admintableAjax(currentDialogID);
    } else {
        location.reload();
    }

}

function deleteEntry(key, page = false, temptable = 0, confirmmessage = "default") {

    var filter = getQueryVariable("filter");

    var id = key;

    if (page === false) {
        var currentPage = getQueryVariable('p');
        if (currentPage === false) currentPage = "";
    } else {
        currentPage = page;
    }

    var title_url = "?p=" + currentPage + "&ajaxCall=true&command=getDeleteTitle&filter=" + filter + "&id=" + id;


    jQuery.ajax({
        type: 'POST',
        cache: false,
        url: title_url,
        data: {filter: filter, command: "getDeleteTitle", temptable: temptable},
        async: true
    })
        .done(function (data) {


            /*   BootstrapDialog.confirm('Hi Apple, are you sure?', function (result) {
                   if (result) {
                       alert('Yup.');
                   } else {
                       alert('Nope.');
                   }
               }); */


            if (confirmmessage === "default") {
                confirmmessage = JSON.parse(data);
            }

            confirmDialog(__("Confirm deletion?"), confirmmessage, function () {

                var realdelete = true; //confirm(confirmmessage); // 'Möchten Sie den Eintrag ' + key + ' wirklich löschen?');


                if (realdelete) {

                    var filter = getQueryVariable('filter');
                    if (filter === false) filter = "";

                    if (page === false) {
                        var currentPage = getQueryVariable('p');
                        if (currentPage === false) currentPage = "";
                    } else {
                        currentPage = page;
                    }

                    var url = "?p=" + currentPage + "&ajaxCall=true&delete=" + key + "&command=deleteEntry&temptable=" + temptable + "&filter=" + filter;

                    var postvalues = null;

                    jQuery.ajax({
                        type: 'POST',
                        cache: false,
                        url: url,
                        data: postvalues,
                        async: false
                    }).done(function (data) {
                        if (data !== null && data !== "" && data !== "") {

                            var formattederr = formatErrorJson(data);

                            if(formattederr === ""){
                                errorDialog("Delete error", ""+data+"");
                            }else{
                                errorDialog("Delete error", ""+formatErrorJson(data)+"");
                            }


                           //

                            //alert("'" + data + "'"); // TODO: JSON FEhlermeldung an der Stelle, besser machen

                        } else {
                            if (typeof bpfw_refreshPageAfterDataChange === "function") {
                                bpfw_refreshPageAfterDataChange(getIdOfCurrentDialog());
                            }

                            handleBpfwEvent("deleteEntry", {
                                model: currentPage,
                                temptable: temptable,
                                filter: filter,
                                rowkey: key
                            });

                        }
                    }); // TODO: nur den jeweiligen Bereich neu laden

                }

            });

        });


    return false;


}

function gotDatatableData(d) {

    //  activePage = getQueryVariable("p");

    d.ajaxCall = true;
    d.command = "getDatatableEntries";

    if (typeof adminTablesAjaxDataFilter === "function") {
        d = adminTablesAjaxDataFilter(d);
    }

}


jQuery(document).ready(function () {

    // main Datatables

    var order = [];

    if (jQuery('.admintable').data("defaultsort") !== null && jQuery('.admintable').data("defaultsort") !== "" &&
        jQuery('.admintable').data("defaultsortorder") !== null && jQuery('.admintable').data("defaultsortorder") !== "") {
        order = [jQuery('.admintable').data("defaultsort"), jQuery('.admintable').data("defaultsortorder")];
    }

    var lang = {};

    if (bpfw_getActiveLang() == "de") {
        lang = {
            "url": bpfw_bpfwpath()+"libs/datatables/German.json"
        };
    }


    adminTables = jQuery('#adminTable').DataTable({

        searchDelay: 750,

        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false
        }],

        "language": lang,

        "searching": bpfw_searchEnabled(),


        responsive: true,
        iDisplayLength: 100,

        "order": order

    });

    adminTables.columns.adjust()
        .responsive.recalc();

    var lang = {};
    if (bpfw_getActiveLang() == "de") {
        lang = {
            "url": bpfw_bpfwpath()+"libs/datatables/German.json"
        };
    }

    adminTablesAjax = jQuery('#adminTable_ajax').DataTable({

        searchDelay: 750,

        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false
        }],

        "language": lang,

        responsive: true,
        iDisplayLength: 50,

        "searching": bpfw_searchEnabled(),

        "order": order,

        "processing": true,
        "serverSide": true,

        //  "deferRender": true,

        "ajax": {
            "url": "",
            // "type": "POST",
            "data": gotDatatableData
        },

        "drawCallback": function (settings) {
            //  alert('DataTables has redrawn the table');
            // adminTablesAjax.responsive.recalc();
        }

    });


    //adminTablesAjax.columns.adjust()
    //.responsive.recalc();


    adminTablesAjax.on('draw', function () {

        if (typeof redraw_admin_table === "function") {
            redraw_admin_table();
        }

        adminTablesAjax.columns.adjust()
            .responsive.recalc();

        jQuery(".spoilercontent").attr("style", "");

        adminTablesAjax.responsive.recalc();
        // alert("ok");


    });

    /*
     function setPaginationCssFor(oldpage, newpage) {

         var lastPage = jQuery('#listviewForm').data("lastpage");

         jQuery(".page-" + oldpage).hide();
         jQuery(".page-" + newpage).show();

         if (newpage > 1) {
             jQuery(".prevButton").show();
         } else {
             jQuery(".prevButton").hide();
         }

         if (lastPage === newpage) {
             jQuery(".continueButton").hide();
             jQuery(".addAction").show();
         } else {
             jQuery(".continueButton").show();
             jQuery(".addAction").hide();
         }

         if (newpage === -1) {
             jQuery(".component").show(); // show all Elements for Error handling ...
             jQuery(".continueButton").hide();
             jQuery(".addAction").show();
         }

     }*/


    function setPaginationCssFor(oldpage, newpage) {

        var lastPage = jQuery('.defaultlistview #listviewForm').data("lastpage");

        jQuery(".defaultlistview .page-" + oldpage).hide();
        jQuery(".defaultlistview .page-" + newpage).show();

        if (newpage > 1) {
            jQuery(".defaultlistview .prevButton").show();
        } else {
            jQuery(".defaultlistview .prevButton").hide();
        }

        if (lastPage === newpage) {
            jQuery(".defaultlistview .continueButton").hide();
            jQuery(".defaultlistview .addAction").show();
        } else {
            jQuery(".defaultlistview .continueButton").show();

            jQuery(".defaultlistview .addAction").hide();
        }

        if (newpage === -1) {
            jQuery(".defaultlistview .component").show(); // show all Elements for Error handling ...
            jQuery(".defaultlistview .continueButton").hide();
            jQuery(".defaultlistview .addAction").show();
        }

    }


    jQuery("body").on("click", ".defaultlistview .addAction", function (e) {
        e.preventDefault();
        setPaginationCssFor(currentPage, -1);
    });

    var currentPage = 1;


    jQuery("body").on("click", ".defaultlistview .directionButton.continueButton", function (e) {
        e.preventDefault();
        var lastPage = jQuery('.defaultlistview #listviewForm').data("lastpage");
        if (currentPage !== lastPage) {
            currentPage++;
            setPaginationCssFor(currentPage - 1, currentPage);
        }

    });


    jQuery("body").on("click", ".defaultlistview .directionButton.prevButton", function (e) {
        e.preventDefault();
        if (currentPage > 0) {
            currentPage--;
            setPaginationCssFor(currentPage + 1, currentPage);
        }

    });


});