@extends('layouts/main_page')
@section('page_title','Dashboard')
@section('header_content')
@endsection
@section('main_content')
<div class="row ">
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <div class="card">
      <div class="card-statistic-4">
        <div class="align-items-center justify-content-between">
          <div class="row ">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
              <div class="card-content">
                <h5 class="font-15">Sales Rep</h5>
                <h2 class="mb-3 font-18">{{$sales_count}}</h2>
                {{-- <p class="mb-0"><span class="col-green">{{($sales_count/100)*100}}%</span> Increase</p> --}}
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
              <div class="banner-img">
                <img src="{!! asset('assets/img/banner/1.png') !!}" alt="">
              </div>
            </div>
            {{-- {!! asset('assets/css/page/button.css') !!} --}}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <div class="card">
      <div class="card-statistic-4">
        <div class="align-items-center justify-content-between">
          <div class="row ">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
              <div class="card-content">
                <h5 class="font-15"> Dealers</h5>
                <h2 class="mb-3 font-18">{{$dealers}}</h2>
                {{-- <p class="mb-0"><span class="col-green">09%</span> Decrease</p> --}}
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
              <div class="banner-img">
                <img src="{!! asset('assets/img/banner/a1.jpg') !!}"  alt="">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <div class="card">
      <div class="card-statistic-4">
        <div class="align-items-center justify-content-between">
          <div class="row ">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
              <div class="card-content">
                <h5 class="font-15">Shops</h5>
                <h2 class="mb-3 font-18">{{$shop}}</h2>
                {{-- <p class="mb-0"><span class="col-green">18%</span>
                  Increase</p> --}}
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
              <div class="banner-img">
                <img src="  {!! asset('assets/img/banner/a2.jpg ') !!}" alt="">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <div class="card">
      <div class="card-statistic-4">
        <div class="align-items-center justify-content-between">
          <div class="row ">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
              <div class="card-content">
                <h5 class="font-15">Order Receipt</h5>
                <h2 class="mb-3 font-18">{{$c_to_d}}</h2>
                {{-- <p class="mb-0"><span class="col-green">42%</span> Increase</p> --}}
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
              <div class="banner-img">
                <img src="{!! asset('assets/img/banner/a3.jpg ') !!}" alt="">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- Nxt Line --}}
<div class="row ">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15">Secondary Sales</h5>
                  <h2 class="mb-3 font-18">{{$d_to_s}}</h2>
                  {{-- <p class="mb-0"><span class="col-green">10%</span> Increase</p> --}}
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="{!! asset('assets/img/banner/a4.jpg') !!}" alt="">
                </div>
              </div>
              {{-- {!! asset('assets/css/page/button.css') !!} --}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15"> Order Dispatch</h5>
                  <h2 class="mb-3 font-18">{{$delivery}}</h2>
                  {{-- <p class="mb-0"><span class="col-green">09%</span> Decrease</p> --}}
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="{!! asset('assets/img/banner/a5.webp') !!}" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15">Income (<?php echo date('Y');?>)</h5>
                  <h2 class="mb-3 font-18">₹{{$delivery_sub_total}}</h2>
                  {{-- <p class="mb-0"><span class="col-green">18%</span>
                    Increase</p> --}}
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="{!! asset('assets/img/banner/4.png ') !!}" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="card">
        <div class="card-statistic-4">
          <div class="align-items-center justify-content-between">
            <div class="row ">
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                <div class="card-content">
                  <h5 class="font-15">Sales Return</h5>
                  <h2 class="mb-3 font-18">₹{{$return_sub_total}}</h2>
                  {{-- <p class="mb-0"><span class="col-green">42%</span> Increase</p> --}}
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                <div class="banner-img">
                  <img src="{!! asset('assets/img/banner/a7.jpg ') !!}" alt="">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{-- End --}}
<div class="row">
  <div class="col-12 col-sm-12 col-lg-12">
    <div class="card ">
      <div class="card-header">
        <h4>Income chart</h4>
        {{-- <div class="card-header-action">
          <div class="dropdown">
            <a href="#" data-toggle="dropdown" class="btn btn-warning dropdown-toggle">Options</a>
            <div class="dropdown-menu">
              <a href="#" class="dropdown-item has-icon"><i class="fas fa-eye"></i> View</a>
              <a href="#" class="dropdown-item has-icon"><i class="far fa-edit"></i> Edit</a>
              <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item has-icon text-danger"><i class="far fa-trash-alt"></i>
                Delete</a>
            </div>
          </div>
          <a href="#" class="btn btn-primary">View All</a>
        </div> --}}
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-9">
            <div id="chart1" data-monthly-data="{{ $delivery_sub_monthly_data_json }}"></div>
            <div class="row mb-0">
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <div class="list-inline text-center">

                  <div class="list-inline-item p-r-30">
                     @if($delivery_sub_weekly_total >= 1000)
                    <i data-feather="arrow-up-circle" class="col-green"></i>
                    @else
                        <i data-feather="arrow-down-circle" class="col-orange"></i>
                    @endif
                    @if($delivery_sub_weekly_total)
                    <h5 class="m-b-0">₹{{$delivery_sub_weekly_total}}</h5>
                    @else
                    <h5 class="m-b-0">₹0</h5>
                    @endif
                    <p class="text-muted font-14 m-b-0">Weekly Earnings</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <div class="list-inline text-center">
                  <div class="list-inline-item p-r-30">
                    @if($delivery_sub_monthly_total >= 7000)
                    <i data-feather="arrow-up-circle" class="col-green"></i>
                    @else
                    <i data-feather="arrow-down-circle" class="col-orange"></i>
                    @endif
                    @if($delivery_sub_monthly_total)
                    <h5 class="m-b-0">₹{{$delivery_sub_monthly_total}}</h5>
                    @else
                    <h5 class="m-b-0">₹0</h5>
                    @endif
                    <p class="text-muted font-14 m-b-0">Monthly Earnings</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <div class="list-inline text-center">
                  <div class="list-inline-item p-r-30">
                    @if($delivery_sub_total >= 100000)
                    <i data-feather="arrow-up-circle" class="col-green"></i>
                    @else
                    <i data-feather="arrow-down-circle" class="col-orange"></i>
                    @endif
                    <h5 class="mb-0 m-b-0">₹{{$delivery_sub_total}}</h5>
                    <p class="text-muted font-14 m-b-0">Yearly Earnings</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="row mt-5">
              <div class="col-7 col-xl-7 mb-3">{{--Total customers--}}</div>
              <div class="col-5 col-xl-5 mb-3">
                <span class="text-big">{{--8,257--}}</span>
                {{-- <sup class="col-green">+09%</sup> --}}
              </div>
              <div class="col-7 col-xl-7 mb-3">{{--Total Income--}}</div>
              <div class="col-5 col-xl-5 mb-3">
                <span class="text-big">{{--$9,857--}}</span>
                {{-- <sup class="text-danger">-18%</sup> --}}
              </div>
              <div class="col-7 col-xl-7 mb-3">{{--Project completed--}}</div>
              <div class="col-5 col-xl-5 mb-3">
                <span class="text-big">{{--28--}}</span>
                {{-- <sup class="col-green">+16%</sup> --}}
              </div>
              <div class="col-7 col-xl-7 mb-3">{{--Total expense--}}</div>
              <div class="col-5 col-xl-5 mb-3">
                <span class="text-big">{{--$6,287--}}</span>
                {{-- <sup class="col-green">+09%</sup> --}}
              </div>
              <div class="col-7 col-xl-7 mb-3">{{--New Customers--}}</div>
              <div class="col-5 col-xl-5 mb-3">
                <span class="text-big">{{--684--}}</span>
                {{-- <sup class="col-green">+22%</sup> --}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12 col-sm-12 col-lg-4">
    <div class="card">
      <div class="card-header">
        <h4>Product Wise Sales Chart</h4>
      </div>
      <div class="card-body">
        <div id="donutchart" style="width:100%;height: 208px;"></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-12 col-lg-4">
    <div class="card">
      <div class="card-header">
        <h4>Expense Chart</h4>
      </div>
      <div class="card-body">
        <div class="summary">
          <div class="summary-chart active" data-tab-group="summary-tab" id="summary-chart">
            <canvas id="myChart" style="width:100%;height: 208px;" expense-data="{{ $expense_sub_monthly_data }}"></canvas>
          </div>
          <div data-tab-group="summary-tab" id="summary-text">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-12 col-lg-4">
    <div class="card">
      <div class="card-header">
        <h4>Sales Return Chart</h4>
      </div>
      <div class="card-body">
        <canvas id="myChart_1" style="width:100%;height: 208px;" return-data=""></canvas>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4>Sales Rep and Dealer Table</h4>
        <div class="card-header-form">
          <form>
            <div class="input-group">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." title="Type in a name">
            </div>
          </form>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped" id="myTable">
            <tr>
              <th class="text-center" hidden>
              </th>
              <th class="text-center">
              </th>
              <th>Sales Rep Name</th>
              <th>Dealers</th>
              <th>Task Status</th>
              <th>Start Date</th>
              <th>End Date</th>
              <th>Priority</th>
            </tr>
            @foreach ($all_dealers as $dealers_value)
            <tr>
                <td class="p-0 text-center" hidden>
                  {{$dealers_value->dealer_name}}
                </td>
                <td class="p-0 text-center">
                </td>
                <td>{{ $dealers_value->sales_ref_name}}</td>
                <td class="text-truncate">

                    <ul class="list-unstyled order-list m-b-0 m-b-0">
                        <li class="team-member team-member-sm">
                            <img class="rounded-circle"
                            src="{{ asset('storage/dealer_img/' . $dealers_value->image_name) }}"
                            alt="user" data-toggle="tooltip" title=""
                            data-original-title="{{$dealers_value->dealer_name}}">

                        </li>
                        {{-- <li class="avatar avatar-sm"><span class="badge badge-primary">+4</span></li> --}}
                    </ul>

                </td>
                <td class="align-middle">
                    <div class="progress-text">50%</div>
                    <div class="progress" data-height="6">
                        <div class="progress-bar bg-success" data-width="70%"></div>
                    </div>
                </td>
                <td>{{ date('Y-m') . '-01' }}</td>
                <td>{{ date('Y-m-d') }}</td>
                <td>
                    <div class="badge badge-success">Low</div>
                </td>
            </tr>
        @endforeach

          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6 col-lg-12 col-xl-6">
    <!-- Support tickets -->
    <div class="card">
      <div class="card-header">
        <h4>Support Ticket</h4>
        <form class="card-header-form">
          <input type="text" name="search" class="form-control" placeholder="Search">
        </form>
      </div>
      <div class="card-body">
        <div class="support-ticket media pb-1 mb-3">
          <img src="{!! asset('assets/img/users/user-1.png') !!}" class="user-img mr-2" alt="">
          <div class="media-body ml-3">
            <div class="badge badge-pill badge-success mb-1 float-right">Feature</div>
            <span class="font-weight-bold">#89754</span>
            <a href="javascript:void(0)">Please add advance table</a>
            <p class="my-1">Hi, can you please add new table for advan...</p>
            <small class="text-muted">Created by <span class="font-weight-bold font-13">John
                Deo</span>
              &nbsp;&nbsp; - 1 day ago</small>
          </div>
        </div>
        <div class="support-ticket media pb-1 mb-3">
          <img src="{!! asset('assets/img/users/user-2.png') !!}" class="user-img mr-2" alt="">
          <div class="media-body ml-3">
            <div class="badge badge-pill badge-warning mb-1 float-right">Bug</div>
            <span class="font-weight-bold">#57854</span>
            <a href="javascript:void(0)">Select item not working</a>
            <p class="my-1">please check select item in advance form not work...</p>
            <small class="text-muted">Created by <span class="font-weight-bold font-13">Sarah
                Smith</span>
              &nbsp;&nbsp; - 2 day ago</small>
          </div>
        </div>
        <div class="support-ticket media pb-1 mb-3">
          <img src="{!! asset('assets/img/users/user-3.png') !!}" class="user-img mr-2" alt="">
          <div class="media-body ml-3">
            <div class="badge badge-pill badge-primary mb-1 float-right">Query</div>
            <span class="font-weight-bold">#85784</span>
            <a href="javascript:void(0)">Are you provide template in Angular?</a>
            <p class="my-1">can you provide template in latest angular 8.</p>
            <small class="text-muted">Created by <span class="font-weight-bold font-13">Ashton Cox</span>
              &nbsp;&nbsp; -2 day ago</small>
          </div>
        </div>
        <div class="support-ticket media pb-1 mb-3">
          <img src="{!! asset('assets/img/users/user-6.png') !!}" class="user-img mr-2" alt="">
          <div class="media-body ml-3">
            <div class="badge badge-pill badge-info mb-1 float-right">Enhancement</div>
            <span class="font-weight-bold">#25874</span>
            <a href="javascript:void(0)">About template page load speed</a>
            <p class="my-1">Hi, John, can you work on increase page speed of template...</p>
            <small class="text-muted">Created by <span class="font-weight-bold font-13">Hasan
                Basri</span>
              &nbsp;&nbsp; -3 day ago</small>
          </div>
        </div>
      </div>
      <a href="javascript:void(0)" class="card-footer card-link text-center small ">View
        All</a>
    </div>
    <!-- Support tickets -->
  </div>
  <div class="col-md-6 col-lg-12 col-xl-6">
    <div class="card">
      <div class="card-header">
        <h4>Projects Payments</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Client Name</th>
                <th>Date</th>
                <th>Payment Method</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>John Doe </td>
                <td>11-08-2018</td>
                <td>NEFT</td>
                <td>$258</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Cara Stevens
                </td>
                <td>15-07-2018</td>
                <td>PayPal</td>
                <td>$125</td>
              </tr>
              <tr>
                <td>3</td>
                <td>
                  Airi Satou
                </td>
                <td>25-08-2018</td>
                <td>RTGS</td>
                <td>$287</td>
              </tr>
              <tr>
                <td>4</td>
                <td>
                  Angelica Ramos
                </td>
                <td>01-05-2018</td>
                <td>CASH</td>
                <td>$170</td>
              </tr>
              <tr>
                <td>5</td>
                <td>
                  Ashton Cox
                </td>
                <td>18-04-2018</td>
                <td>NEFT</td>
                <td>$970</td>
              </tr>
              <tr>
                <td>6</td>
                <td>
                  John Deo
                </td>
                <td>22-11-2018</td>
                <td>PayPal</td>
                <td>$854</td>
              </tr>
              <tr>
                <td>7</td>
                <td>
                  Hasan Basri
                </td>
                <td>07-09-2018</td>
                <td>Cash</td>
                <td>$128</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
     const currentYear = "<?php echo date('Y'); ?>";
</script>
<style>
    #myInput {
      background-image: url("{!! asset('assets/img/searchicon.png') !!}");
      background-position: 10px 7px;
      background-repeat: no-repeat;
      width: 100%;
      font-size: 15px;
      padding: 2px 10px 9px 35px;
      border: 1px solid #ddd;
      margin-bottom: -9px;
    }
    </style>

@endsection
@section('modal_content')
@endsection
@section('footer_content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{!! asset('assets/bundles/apexcharts/apexcharts.min.js') !!}"></script>
    <script src="{!! asset('assets/js/page/index.js') !!}"></script>
@endsection
