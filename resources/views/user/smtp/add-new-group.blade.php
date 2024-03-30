@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">SMTP Groups</h1>
                {{-- <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Add New</li>
                </ol> --}}


                <div class="card mb-4">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="sender-account-box">
                                  <form name="frmAddSMTPGroup" method="POST" action="{{ route('save-smtp-group') }}">
                                    @csrf
                                    <div class="row">
                                      <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="account_name">Group Name</label>
                                          <input
                                              type="text"
                                              class="form-control @error('group_name') is-invalid @enderror"
                                              id="group_name"
                                              name="group_name"
                                              aria-describedby="group_name"
                                              placeholder="Enter Group Name"
                                              value="{{ old('group_name')}}"
                                          >
                                          @error('group_name')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        @if (count($smtps))
                                            @php
                                                $hideSaveButton = false;
                                            @endphp
                                        <div class="form-group">
                                            <label for="">Select SMTP's</label>
                                            @foreach ($smtps as $smtp)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="smtp_id[]" value="{{ $smtp->id }}" @if(is_array(old('smtp_id')) && in_array($smtp->id, old('smtp_id'))) checked @endif>
                                                    <label class="form-check-label" for="inlineCheckbox1">{{$smtp->account_name}}</label>
                                                </div>
                                            @endforeach
                                            @error('smtp_id')
                                              <div class="alert alert-danger">{{ $message }}</div>
                                          @enderror
                                        </div>
                                        @else
                                            @php
                                                $hideSaveButton = true;
                                            @endphp
                                        @endif
                                      </div>
                                    </div>
                                    @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('smtp-permission'))
                                        @if (!$hideSaveButton)
                                            <button type="submit" class="btn btn-success" id="btnSaveSmtpGroup">Save Group</button>
                                        @else
                                            <div class="alert alert-danger">Please create SMTP's First<a class="nav-link" href="{{ route('add-new-smtp')}}">Add New SMTP</a></div>
                                        @endif
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
                <div class="card mb-4">
                  <div class="card-body">
                      <div class="container">
                          <div class="row">
                            <div class="col-sm-12">
                              <div class="sender-account-box">

                                  <div class="row">
                                    <div class="col-sm-12">
                                      <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Group Name</th>
                                                <th>SMTP's</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          @if (count($smtpGroups))
                                            @php
                                                $counter=1;
                                            @endphp


                                            @foreach ($smtpGroups as $smtpGroup)
                                              <tr>
                                                <td>{{ $counter++}}</td>
                                                <td>{{ $smtpGroup->group_name }}</td>
                                                <td>{{ $smtpGroup->account_name }}</td>
                                                <td>
                                                    @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('smtp-permission'))
                                                        <a href="javascript:void(0)" class="btnDeleteSmtpGroup" data-id="{{ $smtpGroup->id }}"><i class="fa-solid fa-trash"></i></a>
                                                    @endif
                                                </td>
                                              </tr>
                                            @endforeach
                                          @else
                                            <tr>
                                                <td colspan="3">No Data Found</td>
                                            </tr>
                                          @endif


                                          </tbody>
                                      </table>

                                    </div>
                                  </div>
                              </div>
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
