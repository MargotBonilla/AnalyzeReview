/**
 * Javascript to hidden the topic field if type is different of Alter Name
 */

'use strict'

$(function() {

    var type = document.getElementById("form_type");

    type.onchange = function(e){
        if(type.value!== "ALTER_NAME")
        {
            $("#topic").addClass("hidden");
        } else {
            $("#topic").removeClass("hidden");
        }
    };

    if(type.value!== 'ALTER_NAME')
    {
        $("#topic").addClass("hidden");
    } else {
        $("#topic").removeClass("hidden");
    }

});