'use strict'

$(function() {

    var $grid = $("#grid"),
        emptyMsgDiv = $("<div class='empty-message'><span>Empty Review list</span></div>");

    $grid.jqGrid({
        url: "/review/grid"
        , datatype: 'json'
        , colNames: ["reviewId","content","score", "total", "actions"]
        , colModel: [
            {index:'id', name:'id', width:40, sorttype:"integer", searchoptions:{sopt:['eq','bw', 'cn', 'ne','le','lt','gt','ge']}}
            , {index:'content', name:'content', width: 250, searchoptions:{sopt:['bw', 'cn', 'eq','ne','le','lt','gt','ge']}}
            , {index:'score', name:'score', width: 100, searchoptions:{sopt:['bw', 'cn', 'eq','ne','le','lt','gt','ge']}}
            , {index:'total', name:'total', width:40, searchoptions:{sopt:['bw', 'cn','eq','ne','le','lt','gt','ge']}}
            , {index: 'actions', name : 'actions', width:30, search:false
            , formatoptions: {
                    keys: true,
                    editbutton: false,
                    delOptions: {
                        url: "/review/remove/",
                        onclickSubmit: onclickSubmit,
                        beforeShowForm: beforeShowForm,
                        afterComplete: afterComplete
                    },
                    processing: true
                }
                ,formatter: 'actions'
            }
        ]
        , loadComplete: rowCount
        , pager: "#toolbar"
        , emptyrecords: "Empty Review List"
        , viewrecords: true
        , scroll : false
        , rowNum:5
        , loadonce: true
        , autowidth: true
        , height: 'auto'
        , multiselect: false
        , gridview: true
    });
    $grid.jqGrid('filterToolbar',{stringResult: true, searchOnEnter : false, searchOperators : true });

    emptyMsgDiv.insertAfter($grid.parent());

    function rowCount() {
        var ts = this;
        if (ts.p.reccount === 0) {
            $(this).hide();
            emptyMsgDiv.show();
        } else {
            $(this).show();
            emptyMsgDiv.hide();
        }
    }

});



function beforeShowForm($form)
{
    var idOfDeletedRow = $("#DelData>td:nth-child(1)").text();
    var idReview = $(this).jqGrid("getRowData", idOfDeletedRow).id;

    $form.find("td.delmsg").eq(0).html("Do you really want delete the review with <b>id " +
        idReview + "</b>?");
}

function onclickSubmit(options, rowid) {
    var rowData = $(this).jqGrid("getRowData", rowid);

    options.url += rowData.id;

    return {};
}


function afterComplete() {
    var p = $(this)[0].p;

    var newPage = p.page; // Gets the current page
    if (p.reccount === 0 && newPage > p.lastpage && newPage > 1) {
        // if after deleting there are no rows on the current page and lastpage != firstpage than
        newPage--; // go to the previous page
    }
    // reload grid to make the row from the next page visable.
    $(p.pager + " input.ui-pg-input").val(newPage); //Here setting the new page into textbox before loading in case of longer grid it would look nice
    $(this).trigger("reloadGrid", [{page: newPage}]); // reloading grid to previous page
    showMessageRemoved();
}

function showMessageRemoved() {
    $('#message').addClass("hidden");
    $('#message_remove').removeClass("hidden").html("The review has been removed correctly");
}
function showMessageWait() {
    $('#box-loading').removeClass("hidden");
}