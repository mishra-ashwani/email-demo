@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Prepare Email</h1>
                <input type="hidden" name="email_subject" class="email_subject" value="{{$email_subject}}">
                <input type="hidden" name="batch_number" class="batch_number" value="{{$batchNumber}}">
                <button class="btnSendEmail" type="button">Send Email</button>
                <div class="container">
                    <div class="row">

                      <div class="col-sm-12">

                        <div class="smtp-list-box">
                          <h3>SMTP List</h3>
                          @foreach ($smtps as $smtp)
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="smtps[]" value="{{$smtp['id']}}" checked disabled>
                                <label class="form-check-label">{{$smtp['account_name']}}</label>
                              </div>
                          @endforeach
                        </div>


                        <div class="recipients-list-box">
                          <h3>Recipients List</h3>
                          <table class="table" id="">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Recipient Email</th>
                                <th scope="col">Sender Email</th>
                                <th scope="col">Status</th>
                              <th scope="col">Comment</th>
                              </tr>
                            </thead>
                            <tbody>

                              @php($count=1)
                              @foreach ($assocData as $recipient )
                              <?php
                                    $fields=json_decode($recipentMeta,true);
                                    $customBody = $template_body;
                                    foreach ($fields as $key => $value) {
                                        $customBody = str_replace("[[".$value."]]",$recipient[$value],$customBody);
                                    }
                                ?>
                                <tr class="row_{{$count}}">
                                    <td scope="row">{{$count}}
                                        <input type="hidden" name="template_body_{{$count}}" class="template_body_{{$count}}" value="{{$customBody}}">
                                    </td>
                                  <td class="recipient_{{$count}}">{{$recipient['email']}}</td>
                                  <td class="sender_{{$count}}"></td>
                                  <td class="status_{{$count}}"></td>
                                  <td class="comment_{{$count}}"></td>
                                </tr>
                                @php($count++)
                              @endforeach

                            </tbody>
                          </table>
                          <input type="hidden" name="count" class="count" value="{{$count-1}}">

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



</script>
@endsection
