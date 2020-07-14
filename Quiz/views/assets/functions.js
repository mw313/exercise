function timing(){
    var time = $('.deadline>span').html();        

    var interval = setInterval(function(){
        time--;
        if(time == 0){
            clearInterval(interval);
            setTimeout(function(){
                // $('#QuestionForm').submit();
                $('#save').remove();
                $('#next').css('display', 'inline-block');
                saveAnswer(function(){
                    $('#QuestionForm').submit();
                });                    
            }, 1000);
        }
        else if(time < 10){
            $('.deadline').removeClass('orange');
            $('.deadline').addClass('red');
        }
        else if(time < 25){
            $('.deadline').addClass('orange');
        }

        $('.deadline>span').html(time_convert(time));
        $('.deadline').css("display", "block");
    }, 1000);
}

function editFormTiming(){
    var time = $('.deadline>span').html();        

    var interval = setInterval(function(){
        time--;
        if(time == 0){
            clearInterval(interval);
            setTimeout(function(){
                // $('#QuestionForm').submit();
                $('#saveItems').remove();
                $('#next').css('display', 'inline-block');
                saveAll(function(){
                    // $('#QuestionForm').submit();
                    
                });                    
            }, 1000);
        }
        else if(time < 10){
            $('.deadline').removeClass('orange');
            $('.deadline').addClass('red');
        }
        else if(time < 25){
            $('.deadline').addClass('orange');
        }

        $('.deadline>span').html(time_convert(time));
        $('.deadline').css("display", "block");
    }, 1000);
}

function time_convert(num)
{ 
    var hours = Math.floor(num / 60);  
    var minutes = num % 60;
    if(hours<10) hours = "0"+hours
    if(minutes<10) minutes = "0"+minutes;
    return hours + ":" + minutes;         
}

function saveAnswer(callback=null){
    var id = $('#answer_id').val();
    var data = {
        answer_id: id        
    };

    if($("[name='answer_option']").length > 0 ){
        var answer_option = $("[name='answer_option']:checked").val();
        data['answer_option'] = answer_option;
    }else{
        // var answer_text = tinyMCE.get('answer_text').getContent();
        var answer_text = $("#answer_text").val();
        data['answer_text'] = answer_text;
    }
    

    $.ajax({
        method: "POST",
        url: "/main/exercice/Quiz/ajax.php",
        data: data,
        beforeSend: function(xhr){
            $('.loading-container').fadeIn('400');
        },
        success: function(response){
            // console.log(response);
            if(response == "1"){
                $.alert({
                    title: 'پیغام سیستم!',
                    content: 'ثبت پاسخ با موفقیت انجام شد!',
                    buttons: {
                        "بسیارخب": {
                            btnClass: 'btn btn-light-blue',
                            action: function(){}
                        },
                        "سوال بعد": {
                            btnClass: 'btn btn-deep-orange',
                            action: nextAnswer
                        }
                    }
                });
                $('#next').css('display', 'inline-block');
                if(callback != null){
                    callback();
                }
            }
        },
        error: function(xhr,status,error){
            if(xhr.status == "422"){
                var elements = xhr.responseJSON.errors;
                Object.keys(elements).forEach(function(element){
                    $("#"+element+"-error").css("display", 'block');
                    var errors = elements[element].join('<br/>');
                    $("#"+element+"-error").html(errors);
                });
            }                                
        },
        complete: function(){
            $('.loading-container').fadeOut('1000');
        }
    });
}

function saveAll(callback=null){
    // var answer_type = tinyMCE.get('answer_text').getContent();
    var answer_type = $('#answer_type').val();
    var id = $('#answer_tbl_id').val();
    var count = $('#count').val();

    var data = {answer_type: answer_type};
    var aid = 0;
    if(answer_type == 5){
        for(var i = 1; i <= count; i++){
            aid = $('#id_'+i).val();
            // data['answer_text_'+aid] = tinyMCE.get('answer_text_'+i).getContent();
            data['answer_text_'+aid] = $('#answer_text_'+i).val();
        }
    }
    else{
        for(var i = 1; i <= count; i++){
            aid = $('#id_'+i).val();
            data['answer_option_'+aid] = $("[name='answer_option_"+aid+"']:checked").val();
        }
    }

    $.ajax({
        method: "POST",
        url: "/main/exercice/Quiz/ajax.php",
        data: data,
        beforeSend: function(xhr){
            $('.loading-container').fadeIn('400');
        },
        success: function(response){
            // console.log(response);
            // window.location.reload();
            if(parseInt(response) > 0){
                $.alert({
                    title: 'پیغام سیستم!',
                    content: 'تغییرات ذخیره گردید!!',
                    buttons: {
                        "بسیارخب": {
                            btnClass: 'btn-light-blue',
                            action: function(){}
                        }
                    }
                });
                $('#next').css('display', 'inline-block');
            }
            else if(response == "0"){
                $.alert({
                    title: 'پیغام سیستم!',
                    content: 'تغییر جدیدی دیده نشد!!',
                    buttons: {
                        "بسیارخب": {
                            btnClass: 'btn-light-blue',
                            action: function(){}
                        }
                    }
                });
                $('#next').css('display', 'inline-block');
            }

            if(callback != null){
                callback();
            }
        },
        error: function(xhr,status,error){
            if(xhr.status == "422"){
                var elements = xhr.responseJSON.errors;
                Object.keys(elements).forEach(function(element){
                    $("#"+element+"-error").css("display", 'block');
                    var errors = elements[element].join('<br/>');
                    $("#"+element+"-error").html(errors);
                });
            }                                
        },
        complete: function(){
            $('.loading-container').fadeOut('1000');
        }
    });
}

// window.onbeforeunload = function(){
//     return "صحفه ترک شود?"
// }

function nextAnswer(){
    $('#NextQuestionForm').submit();
}

function initTinyMCE(selector){
    tinymce.init({
        selector: selector,
        script_url : '../js/tinymce/jscripts/tiny_mce/tiny_mce.js',
        // theme : "advanced",
        content_css: "/main/css/MDB/font/sahel/style.css",
        content_style: "body { font-family: 'sahel';}",
        // skin: 'oxide-dark',
        // content_css: 'dark',
        statusbar: false,
        menubar: false,
        height : 300,
        directionality :'rtl',
        plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker textpattern noneditable help formatpainter pageembed charmap mentions quickbars linkchecker emoticons advtable imagetools',
        // toolbar: 'code undo redo | bold italic | bullist numlist | image | ltr rtl',
        toolbar: 'code undo redo | bold italic | bullist numlist | ltr rtl',
        images_upload_url: '/main/inc/lib/tinymce_5.2.1/upload.php',
        // plugins: 'code,image,table,imagetools,advcode,media,powerpaste,codesample,paste,textpattern',
        // toolbar: 'code undo redo | bold italic alignleft aligncenter alignright | bullist numlist outdent indent image | ltr rtl',
        // images_upload_base_path: 'http://ikvu.ac.ir/',
        images_upload_credentials: true				
    });
}

function endQuiz(){
    var exercise_answer_id = $('#exercise_answer_id').val();

    $.ajax({
        method: "POST",
        url: "/main/exercice/Quiz/ajax.php",
        data: { 
            exercise_answer_id: exercise_answer_id
        },
        beforeSend: function(xhr){
            $('.loading-container').fadeIn('400');
        },
        success: function(response){
            // console.log(response);
            window.location.reload();            
        },
        error: function(xhr,status,error){            
        },
        complete: function(){
            $('.loading-container').fadeOut('1000');
        }
    });
}
