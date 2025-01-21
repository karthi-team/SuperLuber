<link rel="stylesheet" type="text/css" href="{!! asset('assets/css/page/button.css') !!}">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
@extends('./layouts/main_page')
@section('page_title','Market View')
@section('header_content')
@endsection
@section('main_content')
<style>
#map{
    width: 1400px;
    max-width: 100%;
    height: 700px;
}
.leaflet-routing-container {
    display: none !important;
}
.leaflet-routing-line {
    pointer-events: none;
}
.select2-container--default .select2-selection--single .select2-selection__rendered{
    /* width: 245px; */
    min-height: 42px;
    line-height: 42px;
    /* padding-left: 20px; */
    padding-right: 20px;
}
.marker-pin {
  width: 30px;
  height: 30px;
  border-radius: 50% 50% 50% 0;
  background: #c30b82;
  position: absolute;
  transform: rotate(-45deg);
  left: 50%;
  top: 50%;
  margin: -15px 0 0 -15px;
}

.marker-pin::after {
    content: '';
    width: 24px;
    height: 24px;
    margin: 3px 0 0 3px;
    background: #fff;
    position: absolute;
    border-radius: 50%;
 }

 .marker-pin-sales-executive {
  width: 30px;
  height: 30px;
  border-radius: 50% 50% 50% 0;
  background: #0000ff;
  position: absolute;
  transform: rotate(-45deg);
  left: 50%;
  top: 50%;
  margin: -15px 0 0 -15px;
}

.marker-pin-sales-executive::after {
    content: '';
    width: 24px;
    height: 24px;
    margin: 3px 0 0 3px;
    background: #fff;
    position: absolute;
    border-radius: 50%;
 }
 .custom-div-icon i {
   position: absolute;
   width: 22px;
   font-size: 22px;
   left: 0;
   right: 0;
   margin: 10px auto;
   text-align: center;
}

.custom-div-icon i.awesome {
   margin: 12px auto;
   font-size: 17px;
}
</style>
<input type="hidden" id="CUR_ACTION" value="{{ route('Market_View_Action') }}" />
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h4><span class="label info small">Market View</span></h4>
      </div>
      <div class="card-body" id="list_div">
        <div class="row">
            {{-- <div class="col-md-3">
              <div class="form-group">
                <label for="filter_date">Date</label>
                <input type="date" class="form-control" id="filter_date" value="<?php // echo date("Y-m-d"); ?>" onchange="get_sales_executive();"/>
              </div>
            </div> --}}
            <div class="col-md-3">
                <div class="form-group">
                <label for="manager_id">Manager Name</label>
                    <select class="form-control select2" id="manager_id" style="width:100%;" onchange="get_sales_ref();">
                      <option value="">Select</option>
                      @foreach ($manager_creation as $manager_creation1)
                          <option value="{{ $manager_creation1->id }}">{{ $manager_creation1->manager_name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              <div class="col-md-3">
                <div class="form-group">
                <label for="sales_rep_id">Sales Executive</label>
                    <select class="form-control select2" id="sales_rep_id" style="width:100%;" onchange="get_dealer_name();">
                      <option value="">Select</option>
                    </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                <label for="dealer_id">Dealer Name</label>
                   <select class="form-control select2" id="dealer_id" style="width:100%;" onchange="get_market_name();">
                      <option value="">Select</option>
                   </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                <label for="market_id">Market Name</label>
                   <select class="form-control select2" id="market_id" style="width:100%;" onchange="get_shop_type(); get_shop_name();">
                      <option value="">Select</option>
                   </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                <label for="shop_type_id">Shop Type</label>
                   <select class="form-control select2" id="shop_type_id" style="width:100%;" onchange="get_shop_name();">
                      <option value="">Select</option>
                   </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                <label for="shop_id">Shop Name</label>
                   <select class="form-control select2" id="shop_id" style="width:100%;">
                      <option value="">Select</option>
                   </select>
                </div>
              </div>
            <div class="col-md-1">
              <div class="form-group">
                <label>&nbsp;</label><br>
                <button class="btn btn-info" onclick="filter_market_view(manager_id.value, sales_rep_id.value, dealer_id.value, market_id.value, shop_type_id.value, shop_id.value)">GO</button>
              </div>
            </div>
          </div>
        <!-- this div will hold your map -->
        <div id="map"></div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('footer_content')
<script>
$(function () {
    $("#filter_sales_executive_id").select2();
});
</script>
<script src="{!! asset('assets/js/page/Map/market_view.js') !!}"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
@endsection
