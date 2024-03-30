@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">List</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active"></li>
                </ol>

                <button type="button" name="btnAddNewSubUser" class="btnAddNewSubUser">Add New</button>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Lists
                    </div>
                    <div class="card-body">

                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subUsers as $recipient)
                                <tr>
                                    <td>{{ $recipient->name }}</td>
                                    <td>{{ $recipient->email }}</td>
                                    <td>
                                        @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('edit-sub-user'))
                                        <a href="javascript:void(0)" class="btnEditSubUser" data-id="{{$recipient->id}}" name="btnEditSubUser" style="padding: 5px;"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        @endif
                                        @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('delete-sub-user'))
                                        <button type="button" class="btnDeleteSubUser" data-id="{{$recipient->id}}" name="btnDeleteSubUser" style="padding: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addSubUserPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Create New Sub User</h5>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class='col-sm-12'>
                                    <div class="validation-error" style="display: none;">
                                        <ul class="error-line"></ul>
                                    </div>
                                    <form class='form-horizontal' id="frmCreateSubUser" name="frmCreateSubUser">
                                        <input type='hidden' name='parent_id' id="parent_id" value="{{getPrimaryUserId(Auth::user()->id)}}">
                                        <div class="form-group mb-2">
                                            <label for="name">Name</label>
                                            <input type='text' class="form-control" name="name" id='name' />
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Email</label>
                                            <input type='email' class="form-control" name="email" id='email' />
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Password</label>
                                            <input type='password' class="form-control" name="password" id='password' />
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Confirm Password</label>
                                            <input type='text' class="form-control" name="password_confirmation" id='confirm_password' />
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Choose Permissions</label><br>
                                            @foreach ($allPermissions as $role)
                                                <input type="checkbox" class="" value="{{$role->id}}" name="user_permission[]">{{$role->name}}<br>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-success" id="btnCreateSubUser" name="btnCreateSubUser">Create</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" id="updateSubUserPopup" tabindex="-1" role="dialog" aria-labelledby="updateSubUserPopup" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Sub User</h5>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class='col-sm-12'>
                                    <div class="update-validation-error" style="display: none;">
                                        <ul class="error-line"></ul>
                                    </div>
                                    <form class='form-horizontal' id="frmUpdateSubUser" name="frmUpdateSubUser">
                                        <input type='hidden' name='parent_id' id="parent_id" value="{{getPrimaryUserId(Auth::user()->id)}}">
                                        <input type='hidden' name='user_id' id="user_id" value="{{Auth::user()->id}}">
                                        <div class="form-group mb-2">
                                            <label for="name">Name</label>
                                            <input type='text' class="form-control" name="name" id='name' />
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Email</label>
                                            <input type='email' class="form-control" name="email" id='email' />
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Password</label>
                                            <input type='password' class="form-control" name="password" id='password' />
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Confirm Password</label>
                                            <input type='text' class="form-control" name="password_confirmation" id='confirm_password' />
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="email">Choose Permissions</label><br>
                                            @foreach ($allPermissions as $role)
                                                <input type="checkbox" class="edit_user_permission" value="{{$role->id}}" name="user_permission[]">{{$role->name}}<br>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-success" id="btnUpdateSubUser" name="btnUpdateSubUser">Update</button>
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
<script>
    $(document).ready(function() {
        $('.btnAddNewSubUser').on('click', function(){
            $('#addSubUserPopup').modal('show');
        });
        $('#btnCreateSubUser').on('click', function(){
            jQuery.ajax({
                url: '/sub-users/create',
                method: 'post',
                dataType:'json',
                contentType: false,
                processData: false,
                data:  new FormData($('#frmCreateSubUser')[0]),
                success: function(response){
                    if(response.success === true){
                        alert(response.message);
                        window.location.reload();
                    }else{
                        $('.validation-error ul.error-line').html('').show();
                        $('.validation-error').show();
                        response.validaion_error.forEach(element => {
                            $('.validation-error ul.error-line').append('<li>'+element+'</li>');
                        });
                    }

                }
            });
        });
        $('.btnDeleteSubUser').on('click', function(){
            let consent = confirm('Are you sure you want to delete this user?');
            if(consent){
                let user_id = $(this).data('id');
                jQuery.ajax({
                    url: '/sub-users/'+user_id+'/delete',
                    method: 'delete',
                    dataType:'json',
                    contentType: false,
                    processData: false,
                    data:  new FormData().append('user_id', user_id),
                    success: function(response){
                        alert(response.message);
                        window.location.reload();
                    }
                });
            }

        });
        $('.btnEditSubUser').on('click', function(){
            let user_id = $(this).data('id');
            jQuery.ajax({
                url: '/sub-users/'+user_id,
                method: 'get',
                dataType:'json',
                contentType: false,
                processData: false,
                data:  new FormData().append('user_id', user_id),
                success: function(response){
                    if(response.success){
                        $('#frmUpdateSubUser #user_id').val(user_id);
                        $('#frmUpdateSubUser #name').val(response.user.name);
                        $('#frmUpdateSubUser #email').val(response.user.email);
                        $('#frmUpdateSubUser #password').val('');
                        $('#frmUpdateSubUser #confirm_password').val('');

                        let permissions=response.permissions;

                        $('.edit_user_permission').each((i,elem)=>{
                            if(permissions.includes(parseInt($(elem).val()))){
                                $(elem).attr('checked',true);
                            }
                        })

                        $('#updateSubUserPopup').modal('show');
                    }
                }
            });


        });
        $('#btnUpdateSubUser').on('click', function(){
            let user_id = $('#frmUpdateSubUser #user_id').val()
            jQuery.ajax({
                url: '/sub-users/update',
                method: 'post',
                dataType:'json',
                contentType: false,
                processData: false,
                data:  new FormData($('#frmUpdateSubUser')[0]),
                success: function(response){
                    if(response.success === true){
                        alert(response.message);
                        window.location.reload();
                    }else{
                        $('.update-validation-error ul.error-line').html('').show();
                        $('.update-validation-error').show();
                        response.validaion_error.forEach(element => {
                            $('.update-validation-error ul.error-line').append('<li>'+element+'</li>');
                        });
                    }

                }
            });
        });

    });
</script>
@endsection
