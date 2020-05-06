var userId;
var userName;
var partnerId;
var partnerName;

$(document).ready(function(){
    userId = $('#user-id').val();
    userName = $('#user-name').val();

    $(".user-contact").on("click", initCave);
    $("#new-message-text").on("keydown", send);
    $("#msg-send-btn").on("click", send);
    $(".file-button").on("click", function(){
        $("#file")[0].click();
    });
    $("#file").on("change", function(){
        var filename = $(this)[0].files[0].name;
        $("#file-name").html(filename);
        $("#upload-file-button").show();
    });

    $('#uploadForm').submit(function(e) {
        var filename = $("#file")[0].files[0].name;
        if($('#file').val()) {
            e.preventDefault();
            $(this).ajaxSubmit({
                // target:   '#targetLayer',
                beforeSubmit: function() {
                    $("#progress-bar").width('0%');
                },
                uploadProgress: function (event, position, total, percentComplete){
                    $("#progress-bar").width(percentComplete + '%');
                    $("#progress-bar").html('<div id="progress-status">' + percentComplete +' %</div>')
                },
                success:function (name){
                    if (name === 'fail') {
                        alert("File Submission failed.");
                    } else {
                        var $log = $('#msg-content');
                        //Add text to log
                        $log.append("<p class='text-info my-text'>" + userName + "</p>");
                        $log.append("<p class='my-text'><a target='_blank' href='download.php?file=" + name + "'>" + filename + "</a></p>");
                        //Autoscroll
                        $log[0].scrollTop = $log[0].scrollHeight - $log[0].clientHeight;

                    }
                },
                error:function(){

                },
                complete: function(){
                    $("#file").val('');
                    $("#file-name").html('');
                    $("#upload-file-button").hide();
                    $("#progress-bar").hide();
                },
                resetForm: true
            });
            return false;
        }
    });
    $("#clear-store-btn").on("click", function(){
        if (!userId || !partnerId) {
            alert("Select a contact.");
            return;
        }
        if (confirm("Are you sure to clear the store?")) {
            clearStore();
        }
    });

    checkNew();
    setInterval(checkNew, 5000);
});

function initCave(){
    if ($(this).hasClass('disabled')) return;
    if ($(this).hasClass('active-user')) return;
    $(this).removeClass('new-message-alert');
    partnerId = $(this).data('id');
    partnerName = $(this).data('name');
    $('#msg-content').html('');
    $('.active-user').removeClass('active-user');
    $(this).addClass('active-user');
    $('#partner-id').val(partnerId);
    getFishes(0);
}

function send(e){
    if (((e.keyCode === 13 && !e.shiftKey) || e.type === 'click') && partnerId) {
        e.preventDefault();
        var $that = $("#new-message-text");
        var txt = $that.val().trim();
        $that.val('');
        if (txt === '') return false;
        $.ajax({
            url: 'get.php',
            type: 'POST',
            data: {
                'action': 'send-fish',
                'user-id': userId,
                'partner-id': partnerId,
                'type': 'text',
                'content': utf8_to_b64(txt)
            },
            success: function(res){
                var out = $.parseJSON(res);
                if (out.status === 'success') {
                    logMine({type: 'text', content: utf8_to_b64(txt)}, out.timestamps);
                } else {
                    if (out.code === 'no_permission') {
                        window.location.href = 'out.php';
                    } else {
                        $('#msg-content').append('<p class="text-danger">Failed...</p>');
                    }
                }
            }
        });
    }
}

function getFishes(offset) {
    $.ajax({
        url: 'get.php',
        type: 'POST',
        data: {
            action: 'get-msg',
            'user-id': $('#user-id').val(),
            'partner-id': partnerId,
            'offset': offset
        },
        success: function(res){
            var data = $.parseJSON(res);
            var maxId = 0;
            if (data.status === 'error') {
                $('#msg-content').html('<p class="text-danger">Can\'t get data.</p>');
                return;
            }
            $('#msg-content').data({
                'offset': data.offset,
                'has-older': data.hasOlder
            });

            if (data.data.length === 0) {
                $('#msg-content').html('<p class="text-info">Start discussion...</p>');
            }

			while(fish = data.data.pop()){
                if (fish.to === userId) {
                    logOther(fish);
                } else {
                    logMine(fish, fish.send_date);
                }
                if (parseInt(fish.id) > maxId) {
                    maxId = parseInt(fish.id);
                    $('#msg-content').data('last', maxId);
                }
            }
        }
    });
}

function checkNew(){
    var maxId = $('#msg-content').data('last');
    $.ajax({
        url: 'get.php',
        type: 'POST',
        data: {
            action: 'check-new',
            'last': maxId
        },
        success: function(res){
            var data = $.parseJSON(res);
            if (data.status === 'success') {
                $.each(data.fishes, function (i, fish) {
                    if (fish.from == partnerId) {
                        logOther(fish);
                        if (parseInt(fish.id) > maxId) {
                            maxId = parseInt(fish.id);
                        }
                    } else {
                        $('#user-' + fish.from).addClass('new-message-alert');
                    }
                });
                $('#msg-content').data('last', maxId);
                $('.online-status').removeClass('onlineoffline_19767943');
                $.each(data.users, function(i, user) {
                	$('#user-' + user.id).find('.online-status').addClass('onlineoffline_19767943');
                });
            }
        }
    });
}

function clearStore(){
    $.ajax({
        url: 'get.php',
        type: 'POST',
        data: {
            action: 'clear-store',
            'user-id': userId,
            'partner-id': partnerId
        },
        success: function(res) {
            if (res === 'success') {
                $("#msg-content").html("<p class='text-info'>Start discussion...</p>");
            }
        }
    });
}

function logMine( object, time ) {
    var $log = $('#msg-content');
    //Add text to log
    $log.append("<p class='text-info my-text'>" + userName + "<span class='msg-time pull-right'>" + time + "</span></p>");
    if (object.type === 'text') {
        $log.append("<p class='my-text'>" + b64_to_utf8(object.content) + "</p>");
    } else if (object.type === 'file') {
        $log.append("<p class='my-text'><a href='download.php?file=" + object.modified_name + "'>" + object.original_name + "</a></p>");
    }
    //Autoscroll
    $log.scrollTop($log.prop('scrollHeight'));
}

function logOther(object){
    var $log = $("#msg-content");

    $log.append("<p class='text-info other-text'>" + partnerName + "<span class='msg-time pull-right'>" + object.send_date + "</p>");

    if (object.type === 'text') {
        $log.append("<p data-fish='" + object.id + "' class='other-text'>" + b64_to_utf8(object.content) + "</span></p>");
    } else if (object.type === 'file') {
        $log.append("<p class='other-text'><a href='download.php?file=" + object.modified_name + "'>" + object.original_name + "</a></p>");
    }
    //Autoscroll
    $log.scrollTop($log.prop('scrollHeight'));
}

function dateTimeConverter(){
    var a = new Date();
    //var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var year = a.getFullYear();
    var month = (a.getMonth() + 1).toString();
    month = month.length === 1? "0" + month: month;
    //var month = months[a.getMonth()];
    var date = a.getDate().toString();
    date = date.length === 1? "0" + date: date;
    var hour = a.getHours().toString();
    hour = hour.length === 1? "0" + hour: hour;
    var min = a.getMinutes().toString();
    min = min.length === 1? "0" + min: min;
    var sec = a.getSeconds().toString();
    sec = sec.length === 1? "0" + sec: sec;
    return year + '-' + month + '-' + date + ' ' + hour + ':' + min + ':' + sec;
}

function b64_to_utf8( str ) {
    return decodeURIComponent(escape(window.atob( str )));
}

function utf8_to_b64( str ) {
    return window.btoa(unescape(encodeURIComponent( str )));
}