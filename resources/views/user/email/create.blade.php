@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Send Email</h1>



                <div class="card mb-4">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="sender-account-box">
                                  {{-- <h3>Account Setting</h3> --}}
                                  <form name="frmSendEmail" id="frmSendEmail" method="POST" action="{{route('prepare-email')}}">
                                    @csrf
                                    <div class="row">
                                      <div class="col-sm-6">
                                        <div class="form-group">
                                          <label for="recipient_list">Recipients</label>
                                          <select name="recipient_list" class="recipient_list form-control" id="recipient_list">
                                            <option value="">Select</option>
                                            @foreach ($recipients as $recipient)
                                              <option value="{{$recipient->id}}" data-meta="{{$recipient->recipient_meta}}">{{$recipient->recipient_list_name}}</option>
                                            @endforeach
                                          </select>
                                          @error('recipient_list')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        <div class="form-group">
                                          <label for="email_subject">Email Subject</label>
                                          <input
                                              type="text" class="form-control"
                                              id="email_subject" name="email_subject"
                                              aria-describedby="email_subject" placeholder="Email Subject"
                                              value="{{ old('email_subject')}}"
                                          >
                                          @error('email_subject')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        <div class="form-group email-meta-container" style="display: none;">
                                            <label for="email_template">Email Meta</label>
                                            <div class="email-meta-list">

                                            </div>
                                        </div>

                                        <div class="form-group">
                                          <label for="email_template">Email Template</label>
                                          <select name="email_template" class="email_template form-control" id="email_template_list">
                                            <option value="">Blank</option>
                                            @foreach ($email_templates as $email_template)
                                              <option value="{{$email_template->id}}"  {{ old('email_template') == $email_template->id ? "selected" : "" }}>{{$email_template->template_title}}</option>
                                            @endforeach
                                          </select>
                                          @error('email_template')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>


                                      </div>

                                      <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email_subject">Choose SMTP Group</label>
                                            <select name="smtp_group_id" id="smtp_group_id" class="form-control @error('smtp_group_id') is-invalid @enderror">
                                                <option value="">Select Group</option>
                                                @foreach ($smtpGroups as $smtpGroup)
                                                    <option value="{{$smtpGroup->id}}" {{ old('smtp_group_id') == $smtpGroup->id ? "selected" : "" }}>{{$smtpGroup->group_name}}</option>
                                                @endforeach
                                            </select>
                                            @error('smtp_group_id')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="email_subject">Choose SMTP</label>
                                            @foreach ($smtps as $smtp)
                                                <label for="smtp_{{$smtp->id}}">
                                                    <input
                                                    type="checkbox"
                                                    name="smtp_ids[]"
                                                    value="{{$smtp->id}}"
                                                    id="smtp_{{$smtp->id}}"
                                                    @if(is_array(old('smtp_ids')) && in_array($smtp->id,old('smtp_ids'))) checked @endif
                                                    class="cb_smtp"  />{{$smtp->account_name}}
                                                </label>
                                            @endforeach

                                            @error('smtp_ids')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                      </div>

                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="template_body">Email Template</label>
                                          <textarea name="template_body" id="template_body">{{ old('template_body')}}</textarea>
                                          @error('template_body')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                      </div>

                                    </div>

                                    @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('email-permission'))
                                        <button type="submit" class="btn btn-success" id="btnSendEmail" name="btnSendEmail">Send Now</button>
                                    @endif
                                    @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('email-permission'))
                                        <button type="button" class="btn btn-primary" id="btnScheduleEmail" name="btnScheduleEmail">Send Later</button>
                                    @endif
                                </form>
                                </div>
                                <div class="alert smtp-test-response" style="display: none;">
                                </div>
                                @if(session()->has('message'))
                                    <div class="alert {{session()->get('classes')}}">
                                        {{ session()->get('message') }}
                                    </div>
                                @endif
                              </div>
                            </div>
                          </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="scheduleEmailPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Schedule Email</h5>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class='col-sm-12'>
                                    <form class='form-horizontal' id="frmScheduleEmail">
                                        <input type='hidden' name='recipient_list' value=''>
                                        <input type='hidden' name='email_subject' value=''>
                                        <input type='hidden' name='template_body' value=''>
                                        <input type='hidden' name='smtp_group_id' value=''>
                                        <input type='hidden' name='smtp_ids' value=''>
                                        <div class="form-group">
                                            <label for="dtEmail">Select Date Time</label>
                                            <input type='text' class="form-control" name="schedule_date_time" id='dtEmail' />
                                        </div>
                                        <button type="button" class="btn btn-success" id="btnScheduleNow" name="btnScheduleNow">Schedule Now</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
        @include('user.footer')
    </div>
</div>
<style>
    .badge{
        color: green !important;
    }
</style>
<script src="{{ asset('assets/js/tinymce/tinymce.min.js') }}"></script>
<script>
  tinymce.init({
      selector: "textarea#template_body",
      plugins: "code",
      toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code image_upload",
      menubar:false,
      statusbar: false,
      content_style: ".mce-content-body {font-size:15px;font-family:Arial,sans-serif;}",
      height: 400,
      relative_urls : false,
      remove_script_host : false,
      document_base_url : config.APP_URL,
      setup: function(ed) {

          var fileInput = $('<input id="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
          $(ed.getElement()).parent().append(fileInput);

          fileInput.on("change",function(){
              var file = this.files[0];
              var reader = new FileReader();
              var formData = new FormData();
              var files = file;
              formData.append("file",files);
              formData.append('filetype', 'image');
              jQuery.ajax({
                  url: "/service/template/upload-image",
                  type: "post",
                  data: formData,
                  contentType: false,
                  processData: false,
                  async: false,
                  success: function(response){
                      var fileName = response.path;
                      if(fileName) {
                          ed.insertContent('<img src="'+fileName+'"/>');
                      }
                  }
              });
              reader.readAsDataURL(file);
          });

          ed.ui.registry.addButton('image_upload', {
              tooltip: 'Upload Image',
              icon: 'image',
              onAction: function () {
                  fileInput.trigger('click');
              }
          });
      }
  });

  $(document).ready(function () {
    $('#recipient_list').on('change',function(){
        let metadata =$(this).find(':selected').data('meta');
        if(metadata){
            $('.email-meta-container').show();
            $('.email-meta-list').html('');
            $.each(metadata, function(index, item) {
                let itemData = '<span class="badge badge-info">[['+item+']]</span>';
                $('.email-meta-list').append(itemData);
            });
        }else{
            $('.email-meta-list').html('');
            $('.email-meta-container').hide();
        }

    })

    $('#smtp_group_id').on('change', function(){
        let metadata =$(this).find(':selected').val();
        if(metadata != ''){
            $('input[name="smtp_ids[]"]').each(function() {
                this.checked = false;
            });
        }
    })

    $('.cb_smtp').on('change', function(){
        if ($(this).prop('checked')==true){
            $('#smtp_group_id').prop('selectedIndex',0);
        }
    })

    $('#btnScheduleEmail').on('click', function(){
        let recipient_list=$('#recipient_list').val();
        let email_subject=$('#email_subject').val();
        let template_body = tinymce.get("template_body").getContent();
        let smtp_group_id=$('#smtp_group_id').val();

        if(!recipient_list){
            alert('Please select recipients');
            $('#recipient_list').focus();
            return false;
        }
        if(!email_subject.length){
            alert('Please enter email subject');
            $('#email_subject').focus();
            return false;
        }
        if(!template_body.length){
            alert('Please enter email content');
            tinyMCE.get('template_body').focus()
            return false;
        }
        let ids=[];
        $("input.cb_smtp:checked").each(function(){
            ids.push($(this).val());
        });

        if(!smtp_group_id && ids.length == 0 ){
            alert('Please select smtp');
            $('#smtp_group_id').focus();
            return false;
        }

        $("#frmScheduleEmail input[name='recipient_list']").val(recipient_list);
        $("#frmScheduleEmail input[name='email_subject']").val(email_subject);
        $("#frmScheduleEmail input[name='template_body']").val(template_body);
        $("#frmScheduleEmail input[name='smtp_group_id']").val(smtp_group_id);
        $("#frmScheduleEmail input[name='smtp_ids']").val(ids.join(','));

        $('#scheduleEmailPopup').modal('show');
    });
    $('#dtEmail').datetimepicker({
        minDate: new Date(),
        step: 30,
        minTime: new Date().getMinutes()
    });

    $('#btnScheduleNow').on('click', function() {
        let dt = $('#dtEmail').val();
        let fd = new FormData($('#frmScheduleEmail')[0]);

        jQuery.ajax({
            url: '/service/email/schedule-email',
            type: 'post',
            dataType:'json',
            data: fd,
            cache: false,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.success){
                    $('#scheduleEmailPopup').modal('hide')
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Back'
                    }).then((result) => {
                        if (result['isConfirmed']){
                            window.location.reload();
                        }
                    })
                }
            }
        });

    });
  });



</script>
@endsection
