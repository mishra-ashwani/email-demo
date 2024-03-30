@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Recipient</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Upload</li>
                </ol>


                <div class="card mb-4">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="sender-account-box">
                                  {{-- <h3>Account Setting</h3> --}}
                                  <form name="frmAddSMTP" method="POST" action="{{ route('recipients-upload') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="recipient_list_name">Recipients Group Name</label>
                                          <input
                                              type="text"
                                              class="form-control @error('recipient_list_name') is-invalid @enderror"
                                              id="recipient_list_name"
                                              name="recipient_list_name"
                                              aria-describedby="recipient_list_name"
                                              placeholder="Enter Group Name"
                                              value="{{ old('recipient_list_name')}}"
                                          >
                                          @error('recipient_list_name')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        <div class="form-group">
                                          <label for="recipient_list">Recipients List</label>
                                          <input
                                                type="file" class="form-control @error('recipient_list') is-invalid @enderror"
                                                id="recipient_list" name="recipient_list"
                                                aria-describedby="recipient_list"
                                          >
                                          @error('recipient_list')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                      </div>
                                    </div>
                                    @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('recipients-permission'))
                                    <button type="submit" class="btn btn-success" id="btnSaveSmtp">Update</button>
                                    @endif
                                  </form>
                                </div>
                                <div class="alert smtp-test-response" style="display: none;">
                                </div>
                                @if(session()->has('message'))
                                    <div class="alert {{session()->get('classes')}}">
                                        {{ session()->get('message') }}
                                    </div>
                                    <div class="alert {{session()->get('file')}}">
                                        {{ session()->get('file') }}
                                    </div>
                                @endif
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
@endsection
