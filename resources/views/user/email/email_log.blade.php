@extends('layouts.master')

@section('content')
<div id="layoutSidenav">

    @include('user.sidebar')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Email Logs</h1>

                <div class="container">
                    <div class="row">

                      <div class="col-sm-12">




                        <div class="email-list-box">

                          <table class="table" id="emailLogs">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Recipient Email</th>
                                <th scope="col">Sender Email</th>
                                <th scope="col">Status</th>
                                <th scope="col">Comment</th>
                              </tr>
                            </thead>
                            <tbody>

                              @foreach ($email_logs as $email )

                                <tr>
                                  <td >{{$email->created_at}}</td>
                                  <td >{{$email->recipent_email}}</td>
                                  <td >{{$email->from_email}}</td>
                                  <td >{{$email->status}}</td>
                                  <td >{{$email->comments}}</td>

                                </tr>

                              @endforeach

                            </tbody>
                          </table>
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
