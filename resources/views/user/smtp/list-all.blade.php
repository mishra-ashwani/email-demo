@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Dashboard</h1>
                @if (count($smtps))
                    <button type="button" name="btnTestAllSmtp" class="btnTestAllSmtp">Test SMTP</button>
                @endif
                <div class="card mb-4 smtp-list-test">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        SMTP List
                    </div>
                    <div class="card-body">

                        <table id="tblSmtpList">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th>From Name</th>
                                    <th>From Email</th>
                                    <th>Reply Email</th>
                                    <th>Server</th>
                                    <th>Port</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($smtps as $smtp)
                                <tr class="smtp_row_{{$smtp->id}}" data-smtp="{{$smtp->id}}">
                                    <td>{{ $smtp->account_name }}</td>
                                    <td>{{ $smtp->from_name }}</td>
                                    <td>{{ $smtp->from_email }}</td>
                                    <td>{{ $smtp->reply_email }}</td>
                                    <td>{{ $smtp->server }}</td>
                                    <td>{{ $smtp->port }}</td>
                                    <td>
                                        @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('smtp-permission'))
                                            <a href="{{ route('edit-smtp',['id'=>$smtp->id])}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        @endif
                                        @if (is_null(Auth::user()->parent_id) || Auth::user()->validatePermission('smtp-permission'))
                                            <button type="button" class="btnDeleteSmtp" data-id="{{$smtp->id}}" name="btnDeleteSmtp"><i class="fa-solid fa-trash"></i></button>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Account Name</th>
                                    <th>From Name</th>
                                    <th>From Email</th>
                                    <th>Reply Email</th>
                                    <th>Server</th>
                                    <th>Port</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>
        </main>
        @include('user.footer')
    </div>
</div>
@endsection
