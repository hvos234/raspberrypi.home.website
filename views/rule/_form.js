/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    // on click Add Condition button (RuleCondition_add)
    // loop trough all the table rows and set by the
    // first one the display on table-row and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleCondition_add').on('click', function() {
        $('.RuleCondition-row').each(function( index ) {
            if('none' == $(this).css('display')){
                $(this).find('.RuleCondition-value').val('');
                $(this).css('display', 'table-row');
                RuleConditionShowHideButton();
                return false;
            }
        });
        RuleConditionShowHideButton();
        return false;
    });
    
    // on click Remove Condition button (RuleCondition_remove)
    // loop trough all the table rows and set by the
    // last one the display on none and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleCondition_remove').on('click', function() {
        // first in reverse order
        $($('.RuleCondition-row').get().reverse()).each(function(index) { 
            if('table-row' == $(this).css('display')){
                $(this).find('.RuleCondition-value').val('{$none}');
                $(this).css('display', 'none');
                RuleConditionShowHideButton();
                return false;
            }
        });
        RuleConditionShowHideButton();
        return false;
    });
    
    // loop trough all the RuleCondition-row, and
    // count how many are visible (display table-row)
    // and show or hide the Add Condition button (RuleCondition_add) 
    // or the Remove Condition button (RuleCondition_remove)
    function RuleConditionShowHideButton(){
        var count = 0;
        var visible = 0;
        $('.RuleCondition-row').each(function( index ) {
            count++;
            if('table-row' == $(this).css('display')){
                visible++;
            }
        });
        if(1 >= visible){
            $('#RuleCondition_add').css('display', 'inline-block');
            $('#RuleCondition_remove').css('display', 'none');
        }
        if(1 < visible){
            $('#RuleCondition_add').css('display', 'inline-block');
            $('#RuleCondition_remove').css('display', 'inline-block');
        }
        if(count <= visible){
            $('#RuleCondition_add').css('display', 'none');
            $('#RuleCondition_remove').css('display', 'inline-block');
        }
    }
    
    RuleConditionShowHideButton();
    
    // on click Add Condition button (RuleCondition_add)
    // loop trough all the table rows and set by the
    // first one the display on table-row and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleAction_add').on('click', function() {
        $('.RuleAction-row').each(function( index ) {
            if('none' == $(this).css('display')){
                $(this).find('.RuleAction-value').val('');
                $(this).css('display', 'table-row');
                RuleActionShowHideButton();
                return false;
            }
        });
        RuleActionShowHideButton();
        return false;
    });
    
    // on click Remove Condition button (RuleCondition_remove)
    // loop trough all the table rows and set by the
    // last one the display on none and exit.
    // Oh yeah and set the name and the value fields
    $('#RuleAction_remove').on('click', function() {
        // first in reverse order
        $($('.RuleAction-row').get().reverse()).each(function(index) { 
            if('table-row' == $(this).css('display')){
                $(this).find('.RuleAction-value').val('{$none}');
                $(this).css('display', 'none');
                RuleActionShowHideButton();
                return false;
            }
        });
        RuleActionShowHideButton();
        return false;
    });
    
    // loop trough all the RuleCondition-row, and
    // count how many are visible (display table-row)
    // and show or hide the Add Condition button (RuleCondition_add) 
    // or the Remove Condition button (RuleCondition_remove)
    function RuleActionShowHideButton(){
        var count = 0;
        var visible = 0;
        $('.RuleAction-row').each(function( index ) {
            count++;
            if('table-row' == $(this).css('display')){
                visible++;
            }
        });
        if(1 >= visible){
            $('#RuleAction_add').css('display', 'inline-block');
            $('#RuleAction_remove').css('display', 'none');
        }
        if(1 < visible){
            $('#RuleAction_add').css('display', 'inline-block');
            $('#RuleAction_remove').css('display', 'inline-block');
        }
        if(count <= visible){
            $('#RuleAction_add').css('display', 'none');
            $('#RuleAction_remove').css('display', 'inline-block');
        }
    }
    
    RuleActionShowHideButton();
});