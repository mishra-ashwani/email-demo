@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">SMTP</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Update SMTP</li>
                </ol>


                <div class="card mb-4">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="sender-account-box">
                                  {{-- <h3>Account Setting</h3> --}}
                                  <form name="frmAddSMTP" method="POST" action="{{ route('update-smtp',['id'=>$smtp->id]) }}">
                                    @method('PUT')
                                    @csrf
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="account_name">Account Name</label>
                                          <input
                                              type="text"
                                              class="form-control @error('account_name') is-invalid @enderror"
                                              id="account_name"
                                              name="account_name"
                                              aria-describedby="account_name"
                                              placeholder="Enter Account Name"
                                              value="{{ $smtp->account_name}}"
                                          >
                                          @error('account_name')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        <div class="form-group">
                                          <label for="from_name">From Name</label>
                                          <input
                                                type="text" class="form-control @error('from_name') is-invalid @enderror"
                                                id="from_name" name="from_name"
                                                aria-describedby="from_name" placeholder="Enter From Name"
                                                value="{{ $smtp->from_name }}"
                                          >
                                          @error('from_name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        <div class="form-group">
                                          <label for="from_email">From Email</label>
                                          <input
                                                type="email" class="form-control @error('from_email') is-invalid @enderror"
                                                id="from_email" name="from_email"
                                                aria-describedby="from_email" placeholder="Enter From Email"
                                                value="{{ $smtp->from_email }}"
                                          >
                                          @error('from_email')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        <div class="form-group">
                                          <label for="reply_email">Reply Email</label>
                                          <input
                                              type="email" class="form-control @error('reply_email') is-invalid @enderror"
                                              id="reply_email" name="reply_email"
                                              aria-describedby="reply_email" placeholder="Enter Reply Email"
                                              value="{{ $smtp->reply_email }}"
                                          >
                                          @error('reply_email')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                      </div>
                                      </div>
                                      <div class="col-sm-3">
                                        <div class="form-group">
                                          <label for="server">SMTP Server</label>
                                          <input
                                                type="text" class="form-control @error('server') is-invalid @enderror"
                                                id="server" name="server"
                                                aria-describedby="server" placeholder="Enter SMTP Server"
                                                value="{{ $smtp->server }}"
                                          >
                                          @error('server')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                      </div>
                                      <div class="col-sm-3">
                                        <div class="form-group">
                                          <label for="port">SMTP Port</label>
                                          <input
                                                type="text" class="form-control @error('port') is-invalid @enderror"
                                                id="port" name="port"
                                                aria-describedby="port" placeholder="Enter SMTP Port"
                                                value="{{ $smtp->port}}"
                                          >
                                          @error('port')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                      </div>
                                      <div class="col-sm-3">
                                        <div class="form-group">
                                          <label for="use_auth">Use Authentication</label>
                                          <select class="form-control" name="use_auth" id="use_auth">
                                            <option value="1">Yes</option>
                                              <option value="0">No</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-sm-3">
                                        <div class="form-group">
                                          <label for="use_ssl">Use SSL</label>
                                          <select class="form-control" name="use_ssl" id="use_ssl">
                                            <option value="1">Yes</option>
                                              <option value="0">No</option>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="form-group">
                                          <label for="user_email">SMTP User Email</label>
                                          <input
                                                type="email" class="form-control @error('user_email') is-invalid @enderror"
                                                id="user_email" name="user_email"
                                                aria-describedby="user_email" placeholder="Enter User Email"
                                                value="{{ $smtp->user_email }}"
                                          >
                                          @error('user_email')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="form-group">
                                          <label for="user_password">SMTP User Password</label>
                                          <input
                                                type="password" class="form-control @error('user_password') is-invalid @enderror"
                                                id="user_password" name="user_password"
                                                aria-describedby="user_password" placeholder="Enter User Password"
                                                value="{{ $smtp->user_password }}"
                                          >
                                          @error('user_password')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                      </div>
                                    </div>

                                    <button type="button" class="btn btn-primary" id="btnTestSmtp">Test Connection</button>
                                    <button type="submit" class="btn btn-success" id="btnSaveSmtp">Update</button>
                                  </form>
                                </div>
                                <div class="alert smtp-test-response" style="display: none;">
                                </div>
                                @if(session()->has('message'))
                                    <div class="alert alert-success">
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
        @include('user.footer')
    </div>
</div>
@endsection
