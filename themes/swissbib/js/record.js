/*global __dialogHandle, displayFormError, extractController, extractSource, getLightbox, path, toggleMenu*/

/**
 * Functions and event handlers specific to record pages.
 */

function checkRequestIsValid(element, requestURL) {
    var recordId = requestURL.match(/\/Record\/([^\/]+)\//)[1];
    var vars = {}, hash;
    var hashes = requestURL.slice(requestURL.indexOf('?') + 1).split('&');

    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        var x = hash[0];
        var y = hash[1];
        vars[x] = y;
    }
    vars['id'] = recordId;

    var url = path + '/AJAX/JSON?' + $.param({method:'checkRequestIsValid', id: recordId, data: vars});
    $.ajax({
        dataType: 'json',
        cache: false,
        url: url,
        success: function(response) {
            if (response.status == 'OK') {
                if (response.data.status) {
                    $(element).removeClass('checkRequest ajax_hold_availability').html(response.data.msg);
                } else {
                    $(element).remove();
                }
            } else if (response.status == 'NEED_AUTH') {
                $(element).replaceWith('<span class="holdBlocked">' + response.data.msg + '</span>');
            }
        }
    });
}

function setUpCheckRequest() {
    $('.checkRequest').each(function(i) {
        if($(this).hasClass('checkRequest')) {
            var isValid = checkRequestIsValid(this, this.href);
        }
    });
}

function deleteRecordComment(element, recordId, recordSource, commentId) {
    var url = path + '/AJAX/JSON?' + $.param({method:'deleteRecordComment',id:commentId});
    $.ajax({
        dataType: 'json',
        url: url,
        success: function(response) {
            if (response.status == 'OK') {
                $($(element).parents('li')[0]).remove();
            }
        }
    });
}

function refreshCommentList(recordId, recordSource) {
    var url = path + '/AJAX/JSON?' + $.param({method:'getRecordCommentsAsHTML',id:recordId,'source':recordSource});
    $.ajax({
        dataType: 'json',
        url: url,
        success: function(response) {
            if (response.status == 'OK') {
                $('#commentList').empty();
                $('#commentList').append(response.data);
                $('#commentList a.deleteRecordComment').unbind('click').click(function() {
                    var commentId = $(this).attr('id').substr('recordComment'.length);
                    deleteRecordComment(this, recordId, recordSource, commentId);
                    return false;
                });
            }
        }
    });
}

function registerAjaxCommentRecord() {
    $('form[name="commentRecord"]').unbind('submit').submit(function(){
        if (!$(this).valid()) { return false; }
        var form = this;
        var id = form.id.value;
        var recordSource = form.source.value;
        var url = path + '/AJAX/JSON?' + $.param({method:'commentRecord',id:id});
        $(form).ajaxSubmit({
            url: url,
            dataType: 'json',
            success: function(response, statusText, xhr, $form) {
                if (response.status == 'OK') {
                    refreshCommentList(id, recordSource);
                    $(form).resetForm();
                } else if (response.status == 'NEED_AUTH') {
                    var $dialog = getLightbox('MyResearch', 'Login', id, null, 'Login');
                    $dialog.dialog({
                        close: function(event, ui) {
                            // login dialog is closed, check to see if we can proceed with followup
                            if (__dialogHandle.processFollowup) {
                                 // trigger the submit event on the comment form again
                                 $(form).trigger('submit');
                            }
                        }
                    });
                } else {
                    displayFormError($form, response.data);
                }
            }
        });
        return false;
    });
}

$(document).ready(function(){
    // register the record comment form to be submitted via AJAX
    registerAjaxCommentRecord();

    // bind click action to export record menu
    $('a.exportMenu').click(function(){
        toggleMenu('exportMenu');
        return false;
    });

    var id = document.getElementById('record_id').value;

    // bind click action on toolbar links
    $('a.citeRecord').click(function() {
        var controller = extractController(this);
        var $dialog = getLightbox(controller, 'Cite', id, null, this.title);
        return false;
    });
    $('a.smsRecord').click(function() {
        var controller = extractController(this);
        var $dialog = getLightbox(controller, 'SMS', id, null, this.title);
        return false;
    });



    $('a.mailRecord').click(function() {

        var currentElement = this;

        $.ajax({

            //GH: I tried to use a more generalized way to refactor the ajax call into a common used function
            //this doesn't work because of the asychronized character of AJAX. the calling function doesn't receive the correct answer
            //I guess I have to define a generalized callback function
            url:  '/AJAX/SHIBLOGIN',
            dataType: 'json',
            success: function(response) {


                if (response.status == 'OK') {

                    if (response.data.toLowerCase() == "true") {
                        //todo: GH:  I need a php flashmessage "please login" created in JavaScript???
                        window.location.href = "/MyResearch/Home";
                    } else {
                        var controller = extractController(currentElement);
                        var $dialog = getLightbox(controller, 'Email', id, null, this.title, controller, 'Email', id);
                    }

                } else {
                    //something went wrong - go to login dialog in any case
                    window.location.href = "/MyResearch/Home";
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // something went wrong
                // GH:we need a strategy how to log such situations on the server
                console.log(textStatus, errorThrown);
                //by now: send the user to the login dialog
            }
        });


        return false;

    });
    $('a.tagRecord').click(function() {


        var currentElement = this;

        $.ajax({
            url:  '/AJAX/SHIBLOGIN',
            dataType: 'json',
            success: function(response) {


                if (response.status == 'OK') {

                    if (response.data.toLowerCase() == "true") {
                        //todo: GH:  I need a php flashmessage "please login" created in JavaScript???
                        window.location.href = "/MyResearch/Home";
                    } else {
                        var controller = extractController(currentElement);
                        var $dialog = getLightbox(controller, 'AddTag', id, null, currentElement.title, controller, 'AddTag', id);
                    }

                } else {
                    //something went wrong - go to login dialog in any case
                    window.location.href = "/MyResearch/Home";
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // something went wrong
                // GH:we need a strategy how to log such situations on the server
                console.log(textStatus, errorThrown);
                //by now: send the user to the login dialog
            }
        });


        return false;


    });
    $('a.deleteRecordComment').click(function() {
        var commentId = this.id.substr('recordComment'.length);
        var recordSource = extractSource(this);
        deleteRecordComment(this, id, recordSource, commentId);
        return false;
    });

    // add highlighting to subject headings when mouseover
    $('a.subjectHeading').mouseover(function() {
        var subjectHeadings = $(this).parent().children('a.subjectHeading');
        for(var i = 0; i < subjectHeadings.length; i++) {
            $(subjectHeadings[i]).addClass('highlight');
            if ($(this).text() == $(subjectHeadings[i]).text()) {
                break;
            }
        }
    });
    $('a.subjectHeading').mouseout(function() {
        $('.subjectHeading').removeClass('highlight');
    });

    $('.checkRequest').each(function(i) {
        if($(this).hasClass('checkRequest')) {
            $(this).addClass('ajax_hold_availability');
        }
    });

    setUpCheckRequest();
});