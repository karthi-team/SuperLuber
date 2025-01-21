@extends('layouts/main_page')
@section('page_title','Profile')
@section('header_content')
@endsection
@section('main_content')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        body {
            margin-top: 20px;
            color: #1a202c;
            text-align: left;
            background-color: #e2e8f0;
        }
        .main-body {
            padding: 15px;
        }
        .card {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
        }
        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 0 solid rgba(0, 0, 0, .125);
            border-radius: .25rem;
        }
        .card-body {
            flex: 1 1 auto;
            min-height: 1px;
            padding: 1rem;
        }

        .gutters-sm {
            margin-right: -8px;
            margin-left: -8px;
        }

        .gutters-sm>.col,
        .gutters-sm>[class*=col-] {
            padding-right: 8px;
            padding-left: 8px;
        }

        .mb-3,
        .my-3 {
            margin-bottom: 1rem !important;
        }

        .bg-gray-300 {
            background-color: #e2e8f0;
        }

        .h-100 {
            height: 100% !important;
        }

        .shadow-none {
            box-shadow: none !important;
        }
    </style>

<body>
    <div class="container">
        <div class="main-body">

            <div class="row gutters-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                {{-- <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Admin"
                                    class="rounded-circle" width="150"> --}}

                                    <img src="{{ asset('storage/employee_images/'.$market->image_name) }}" alt="Employee Image" width="200" height="200">
                                   
                                <div class="mt-3">
                                    <h4>

                                        {{ $market->manager_name }}

                                    </h4>
                                    <p class="text-secondary mb-1"></p>
                                    {{-- <p class="text-muted font-size-sm">Bay Area, San Francisco, CA</p> --}}
                                    {{-- <button class="btn btn-primary">Follow</button>
                                    <button class="btn btn-outline-primary">Message</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Full Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">

                                    {{ $market->manager_name }}

                                </div>
                            </div>
                            {{-- <hr> --}}
                            {{-- <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <a href="#" class="__cf_email__"
                                        data-cfemail="ef89869faf859a84829a87c18e83">{{$employee_creation->email_address}}</a>
                                </div>
                            </div> --}}
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Phone</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">

                                   {{ $market->contact_no }}

                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">WhatsApp</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    {{ $market->whatsapp_no }}

                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Address</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">

                                    {{ $market->address }}

                                </div>
                            </div>
                            <hr>
                            {{-- <div class="row">
                                <div class="col-sm-12">
                                    <a class="btn btn-info " target="__blank"
                                        href="https://www.bootdey.com/snippets/view/profile-edit-data-and-skills">Edit</a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    {{-- <div class="row gutters-sm"> --}}
                        <div >
                            <div class="card h-100">
                                <div class="card-body">
                                    <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                                        <li class="media">
                                          <div class="media-body">
                                            <div class="media-title">Java</div>
                                          </div>
                                          <div class="media-progressbar p-t-10">
                                            <div class="progress" data-height="6">
                                              <div class="progress-bar bg-primary" data-width="70%"></div>
                                            </div>
                                          </div>
                                        </li>
                                        <li class="media">
                                          <div class="media-body">
                                            <div class="media-title">Web Design</div>
                                          </div>
                                          <div class="media-progressbar p-t-10">
                                            <div class="progress" data-height="6">
                                              <div class="progress-bar bg-warning" data-width="80%"></div>
                                            </div>
                                          </div>
                                        </li>
                                        <li class="media">
                                          <div class="media-body">
                                            <div class="media-title">Photoshop</div>
                                          </div>
                                          <div class="media-progressbar p-t-10">
                                            <div class="progress" data-height="6">
                                              <div class="progress-bar bg-green" data-width="48%"></div>
                                            </div>
                                          </div>
                                        </li>
                                      </ul>
                                </div>
                            </div>
                        </div>
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"></script>
</body>

@endsection
@section('footer_content')
{{-- <script src="../assets/js/page/advance_creation.js"></script> --}}
@endsection
<?php session_write_close(); ?>
