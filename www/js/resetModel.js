jQuery(document).ready(function ($) {

    jQuery(document).on("click", "#resetModels", function (e) {

        var value_sel = jQuery("i.checkOn[data-colkey='export']");

        if (value_sel.length == 0) {
            alert("keine Modell gewählt");
            return;
        }

        var models = [];
        var mstrings = "";
        var first = true;
        for (var val in value_sel) {
            // alert(JSON.stringify(value_sel));
            if (jQuery(value_sel[val]).data("rowvalue") != null) {
                models.push(jQuery(value_sel[val]).data("rowvalue"));

                if (!first) {
                    mstrings += ", ";
                }

                mstrings += jQuery(value_sel[val]).data("rowvalue");

                first = false;
            }


        }


        if (confirm("Ausgewählte Tabellen der gewählten Modelle wirklich unwiderruflich Löschen? Alle Daten gehen verloren. \r\n\r\nTabellen: " + mstrings)) {

            var url = "?p=reset&ajaxCall=true&command=performReset";

            jQuery.ajax({type: 'POST', cache: false, url: url, data: {models: models}, async: true}).done(function (d) {

                alert(d);

            });

            // alert(JSON.stringify(models));

        }


        e.preventDefault();
    });


});