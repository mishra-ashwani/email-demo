@extends('layouts.master')

@section('content')
<div id="layoutSidenav">
    
    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Email Template</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Create</li>
                </ol>
                
                
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="sender-account-box">
                                  {{-- <h3>Account Setting</h3> --}}
                                  <form name="frmAddSMTP" method="POST" action="{{ route('save-template') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="template_title">Template Title</label>
                                          <input 
                                              type="text" 
                                              class="form-control @error('template_title') is-invalid @enderror" 
                                              id="template_title" 
                                              name="template_title" 
                                              aria-describedby="template_title" 
                                              placeholder="Enter Template Title"
                                              value="{{ old('template_title')}}"
                                          >
                                          
                                          @error('template_title')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        <div class="form-group">
                                          <label for="template_body">Template Body</label>
                                          <textarea name="template_body" id="template_body">{{ old('template_body')}}</textarea>
                                          @error('template_body')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                       
                                      </div>
                                      
                                   
                                    
                                     
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success" id="btnSaveTemplate">Save Template</button>
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
{{-- <script src="tinymce/tinymce.min.js"></script> --}}
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