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
                                              <option value="{{$email_template->id}}">{{$email_template->template_title}}</option>
                                            @endforeach
                                          </select>
                                          @error('email_template')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>


                                      </div>

                                      <div class="col-sm-6">
                                        <div class="form-group">
                                          <label for="email_subject">Choose SMTP</label>
                                          @foreach ($smtps as $smtp)

                                              <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                  <div class="input-group-text">
                                                    <input type="checkbox" aria-label="Checkbox for following text input" name="smtps[]" value="{{$smtp->id}}"  {{ (is_array(old('smtps')) and in_array($smtp->id, old('smtps'))) ? ' checked' : '' }}>
                                                  </div>
                                                </div>
                                                <label for="email_subject">{{$smtp->account_name}}</label>
                                              </div>

                                              @endforeach

                                              @error('smtps')
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


                                    <button type="submit" class="btn btn-success" id="btnSendEmail" name="btnSendEmail">Send</button>
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
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Your Website 2022</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
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
  });



</script>
@endsection
