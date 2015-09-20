'use strict'

$(function() {

    var $grid = $("#grid"),
        emptyMsgDiv = $("<div class='empty-message'><span>Empty Criteria list</span></div>");

    var typeCriteriaFilter = ":Any;ALTER NAME:Alter Name;POSITIVE:Positive;NEGATIVE:Negative";
    var typeCriteriaEdit = {
        ALTER_NAME: "Alter Name",
        POSITIVE: "Positive",
        NEGATIVE: "Negative"
    };

    $grid.jqGrid({
        url: "/criteria/grid"
        , datatype: 'json'
        , jsonReader: { repeatitems: false }
        , colNames: ["criteriaId","topic","name", "type", "actions"]
        , colModel: [
            {index:'id', name:'id', jsonmap: function(obj) {return obj.cell[0]}, width:40, sorttype:"integer", searchoptions:{sopt:['eq','bw', 'cn', 'ne','le','lt','gt','ge']}}
            , {index:'topic', name:'topic', jsonmap: function(obj) {return obj.cell[1]}, width: 100, searchoptions:{sopt:['bw', 'cn', 'eq','ne','le','lt','gt','ge']}}
            , {index:'name', name:'name', jsonmap: function(obj) {return obj.cell[2]}, width: 100, searchoptions:{sopt:['bw', 'cn', 'eq','ne','le','lt','gt','ge']}}
            , {index:'type', name: 'type', jsonmap: function(obj) {return typeCriteriaEdit[obj.cell[3]]}, width:40, stype: 'select',
                searchoptions:{sopt: ['eq'], value: typeCriteriaFilter},

            }
            , {index: 'actions', name : 'actions', width:30, search:false
                , formatoptions: {
                    keys: true,
                    editbutton: false,
                    delOptions: {
                        url: "/criteria/remove/",
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
        , emptyrecords: "Empty Criteria List"
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
    var idCriteria = $(this).jqGrid("getRowData", idOfDeletedRow).id;

    $form.find("td.delmsg").eq(0).html("Do you really want delete the criteria with <b>id " +
        idCriteria + "</b>?");
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