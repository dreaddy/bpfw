/* will be auto included if the examplecomplexmodel is the model */

jQuery(document).ready(

    function(e) {
        console.log("Hello from autoincluded js");

        // example for an event. You can use them to manipulate the forms after they are loaded
        registerBpfwEvent("startEdit", function (params) {
            // recalculate values etc
            console.log("startEdit of " + params.model);
        });

        // or to verify them on submit
        registerBpfwEvent("EditEntrySubmit", function (params) {
            console.log("EditEntrySubmit of row " + params.rowid + " of " + params.model);
        });

    }

);

jQuery("#dialogWrapper").on("click", ".addEntryDialog #button-addedit-test_button", function(e){
    console.log("called button click on add dialogue");
    alert("custom button in add");
});

jQuery("#dialogWrapper").on("click", ".editEntryDialog #button-addedit-test_button", function(e){
    console.log("called button click on edit dialogue");
    alert("custom button in edit");
});

jQuery("#dialogWrapper").on("click", "#button-addedit-test_button", function(e){

    console.log("ajax test call");

    var currentPage = getQueryVariable('p');

    if (currentPage === false){
        currentPage = "";
    }

    var url = "?p=" + currentPage + "&ajaxCall=true&command=exampleAjaxCommand";

    jQuery.ajax({
        type: 'POST',
        url: url,
        data: null,
        processData: false,
        contentType: false,
        cache: false,
        success: function (data) {
            console.log("called exampleAjaxCommand");
            alert("Example Ajax call with result: " + data);
        }
    });

});

