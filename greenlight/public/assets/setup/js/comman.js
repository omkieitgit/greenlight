
// function comman_ajax_call(){
//         $.post(url,)
// }


function comman_ajax_call(url, datastring, _this, method, async_type, data_type, before_call, function_name) {
        
        $.ajax({
            type: method,
            url: url,
            data: datastring,
            async: async_type,
            dataType: data_type,
            success: function (data) {
                
                //console.log(data);
                if (typeof window[before_call] === "function")
                    window[before_call](data, _this); //To call the function dynamically!
                var obj = data;
    
                if (obj['redirect'] && obj['redirect'] == "yes" && obj['redirect_action'] == "login")
                    redirect(BASE_URL);
                if (obj['redirect'] && obj['redirect'] == "yes" && obj['redirect_action'] == "search")
                    redirect(BASE_URL + 'home/search');
                if (obj['redirect'] && parseInt(obj['redirect']) == 1) {
                    redirect(BASE_URL + '');
                } else if (obj['function_type'] == "no_action") {
                    //console.log('no action required.')
                } else if (typeof window[function_name] == "no_action") {
                    //console.log('no action required.')
                } else if (typeof window[function_name] === "function") {
                    window[function_name](obj, _this); //To call the function dynamically!
                } else {
                    //console.log('Please refresh page and try again.');
                }
    
            },
            error: function (xhr, textStatus, errorThrown) { 
                if(textStatus=='error'){
                       var data=JSON.parse(xhr.responseText);
                        alert(data.message);
                }
                if (typeof window[before_call] === "function")
                    window[before_call]('error', _this); //To call the function dynamically!
                //console.log('Server is down, Please refresh page and try again ');
                return false;
            }
        });
}


function alert_notification3(type, msg, class_css, disappear) {
        var notification = '';
        var custme_class = 'alert-danger';
        if (type == "warning") {
            custme_class = 'alert-warning';
        } else if (type == "info") {
            custme_class = 'alert-info';
        } else if (type == "success") {
            custme_class = 'alert-success';
        } else {
            custme_class = 'alert-danger';
        }
        notification = '<div class="w100 _disappear_alert alert ' + custme_class + ' alert-dismissable">' + '<button type="button" class="close close_hide" data-dismiss="alert" aria-hidden="true">&times;</button>' + msg + '</div>';
    
        if ($('.' + class_css).length > 0) {
            $('.' + class_css).html(notification);
    
            $('.' + class_css).focus();
            class_focus(class_css);
        }
    
        if (disappear == true) {
            setTimeout(function () {
                $("._disappear_alert").fadeOut(5000, function () {
                    $(this).remove();
                });
            }, 2000);
        }
}
    