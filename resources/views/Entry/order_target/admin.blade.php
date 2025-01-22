<link rel="stylesheet" type="text/css" href="../assets/css/page/button.css">
@extends('layouts/main_page')
@section('page_title','Order Target')
@section('header_content')
@endsection
@section('main_content')
<style>
  .modal-xxl{
    max-width: 1600px;
    margin: 1 auto;
    padding-left: 17px;
  }
  </style>
<input type="hidden" id="CUR_ACTION" value="{{ route('Order_Target_Action') }}" />
<input type="hidden" id="_token1" value="{{ csrf_token() }}" />
<input type="hidden" id="CUR_ACTION1" value="{{ route('Order_Target_Action1') }}" />
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h4><span class="label info small">Order Target</span></h4>
        <div class="card-header-action">
          <button type="submit" class="button" onclick="open_model('Order Target','')">Create</button>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="from_date_1">From Date</label>
              <input type="date" class="form-control" id="from_date_1" value="<?php echo date("Y-m-01"); ?>" />
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="to_date_1">To Date</label>
              <input type="date" class="form-control" id="to_date_1" value="<?php echo date("Y-m-t"); ?>" />
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="sales_executive_id_1">Sales Executive Name</label>
              <select id="sales_executive_id_1" class="form-control">
                <option value="">Select</option>
                @foreach ($sales_ref_creation as $sales_ref_creation_1)
                    <option value="{{$sales_ref_creation_1->id}}">{{$sales_ref_creation_1->sales_ref_name}}</option>
                @endforeach
            </select>
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              <label>&nbsp;</label><br>
              <button class="btn btn-info" onclick="list_div(from_date_1.value,to_date_1.value,sales_executive_id_1.value)">GO</button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body" id="list_div">
      </div>
    </div>
  </div>
</div>
@endsection
@section('modal_content')
<div class="modal fade" data-keyboard="false" data-backdrop="static" id="bd-example-modal-lg1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xxl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myLargeModalLabel">Modal title1</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="model_main_content">
        ...
      </div>
    </div>
  </div>
</div>
@endsection
@section('footer_content')
<script src="{!! asset('assets/js/page/Entry/order_target.js') !!}"></script>
@endsection
