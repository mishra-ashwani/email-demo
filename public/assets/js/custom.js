$(document).ready(function(){

    $('div.alert-success').delay(2000).fadeOut('slow');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(){
            $('.page-loader').show();
        },
        complete: function(){
            $('.page-loader').hide();
        }
    });


    $('#btnTestSmtp').on('click',function(e){

        e.preventDefault();


        jQuery.ajax({
            url: config.routes.test_smtp,
            method: 'post',
            dataType:'json',
            data: {
                host: jQuery('#server').val(),
                port: jQuery('#port').val(),
                from_name: jQuery('#from_name').val(),
                user_email: jQuery('#user_email').val(),
                user_password: jQuery('#user_password').val(),
            },
            success: function(response){
                if(response.result == 'success'){
                    $('#btnSaveSmtp').attr('disabled',false);
                    $('.smtp-test-response').html('Connection Successfully Tested.').addClass('alert-success').show().delay(3000).fadeOut();
                }else{
                    $('#btnSaveSmtp').attr('disabled',true);
                    $('.smtp-test-response').html(response.message).addClass('alert-danger').show().delay(5000).fadeOut();
                }

            }
        });
    });

    $(document).on('click','.btnDeleteSmtp',function(e){
        var id=$(this).data('id');

        jQuery.ajax({
            url: '/service/smtp/delete/'+id,
            type: 'delete',
            dataType:'json',
            data: {
                id: id,
            },
            success: function(response){
                window.location.reload();
            }
        });
    });
    $(document).on('click','.btnDeleteSmtpGroup',function(e){
        e.preventDefault();
        var id=$(this).data('id');
        jQuery.ajax({
            url: '/service/smtp-group/delete/'+id,
            type: 'delete',
            dataType:'json',
            data: {
                id: id,
            },
            success: function(response){
                window.location.reload();
            }
        });
        return true;
    });
    $(document).on('click','.btnDeleteRecipientsList',function(e){
        var id=$(this).data('id');

        jQuery.ajax({
            url: '/service/recipient/delete/'+id,
            type: 'delete',
            dataType:'json',
            data: {
                id: id,
            },
            success: function(response){
                window.location.reload();
            }
        });
    });
    $(document).on('click','.btnDeleteTemplate',function(e){
        var id=$(this).data('id');

        jQuery.ajax({
            url: '/service/template/delete/'+id,
            type: 'delete',
            dataType:'json',
            data: {
                id: id,
            },
            success: function(response){
                window.location.reload();
            }
        });
    });

    $('body').on('click','.btnSendEmail',function(){
        var smtps=[];


        $('input:checkbox[name="smtps[]"]').each(function(){
            if($(this).is(':checked'))
            smtps.push($(this).val());
        });

        let total_smtp=smtps.length;
        let smtp_counter=0;

        for(let i=1;i<=$('.count').val();i++){

            setTimeout(function(){
                var fd = new FormData();
                fd.append('email_subject',$('.email_subject').val());
                fd.append('batch_number',$('.batch_number').val());
                fd.append('smtp',smtps[smtp_counter]);
                fd.append('counter',i);
                fd.append('recipient',$('.recipient_'+i).html());
                fd.append('template_body',$('.template_body_'+i).val());

                jQuery.ajax({
                    url: '/service/email/send-email',
                    type: 'post',
                    dataType:'json',
                    data: fd,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('.row_'+i).addClass(data.status);
                        $('.sender_'+i).html(data.from_email);
                        $('.status_'+i).html(data.status);
                        $('.comment_'+i).html(data.comment);
                    }
                });

                smtp_counter++;
                if(smtp_counter == total_smtp){
                    smtp_counter=0;
                }
            },1000);
        }

    })


    $('body').on('click','.btnTestAllSmtp',function(){

        var fd = new FormData();

        $('table#tblSmtpList > tbody  > tr').each(function() {
            let smtp_id = $(this).data('smtp');
            fd.append('smtp_id',smtp_id);

            jQuery.ajax({
                url: '/service/smtp/test-smtp-by-id',
                type: 'post',
                dataType:'json',
                data: fd,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('.smtp_row_'+smtp_id).addClass(data.result);
                }
            });


         });
    })
    $('body').on('click','.btnShowTemplate',function(){

        let fd = new FormData();
        let temp_id = $(this).data('t');

        jQuery.ajax({
            url: '/service/show-template/'+temp_id,
            type: 'get',
            dataType:'json',
            data: fd,
            cache: false,
            processData: false,
            contentType: false,
            success: function(data) {

                $('#emailTemplate .modal-body').html(data.content);
                $('#emailTemplate').modal('show');
            }
        });


    })

    $('#email_template_list').on('change',function(){
        if($(this).val()){
            let fd = new FormData();
            let temp_id = $(this).val();
            jQuery.ajax({
                url: '/service/show-template/'+temp_id,
                type: 'get',
                dataType:'json',
                data: fd,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data) {

                    tinymce.get('template_body').setContent(data.content);
                }
            });
        }else{
            tinymce.get('template_body').setContent('');
        }

    })


})
