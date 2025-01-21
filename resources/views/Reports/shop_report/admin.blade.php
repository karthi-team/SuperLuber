@extends('layouts/main_page')
@section('page_title', 'Shop Reports')
@section('header_content')
    <link rel="stylesheet" type="text/css" href="../assets/css/page/button.css">
@endsection
@section('main_content')
    <style>
        .modal-xxl {
            max-width: 1600px;
            margin: 1 auto;
            padding-left: 17px;
        }
    </style>

    <input type="hidden" id="CUR_ACTION" value="{{ route('Shop_Report_action') }}" />
    <input type="hidden" id="_token" value="{{ csrf_token() }}" />
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><span class="label info small">Shop Reports</span></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="from_date">From Date</label>
                                <input type="date" class="form-control" id="from_date" value="<?php echo date('Y-m-d'); ?>" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="to_date">To Date</label>
                                <input type="date" class="form-control" id="to_date" value="<?php echo date('Y-m-t'); ?>" />
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="dealer_id">Manager Name</label>
                                <select class="form-control select2" onchange="get_sales_ref()" id="manager_id"
                                    width="100%">
                                    <option value="">Select</option>
                                    @foreach($manager_creation as $manager_creation1)
                                    <option value="{{ $manager_creation1['id'] }}">{{ $manager_creation1['manager_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="dealer_id">Sales Ref Name</label>
                                <select class="form-control select2" id="sales_ref_id" width="100%"
                                    onchange="get_dealer()">
                                    <option value="">Select</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="manager_name">Dealer Name</label>
                                <select class="form-control select2" id="dealer_name" width="100%"
                                    onchange='get_area_name()'>
                                    <option value="">Select</option>
                                    <?php foreach($dealer_list as $dealer_list1){ ?>
                                    <option value="<?php echo $dealer_list1['id']; ?>"><?php echo $dealer_list1['dealer_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sales_name">Market Name</label>
                                <select class="form-control select2" id="area_name" width="100%"
                                    onchange='get_shop_type()'>
                                    <option value="">Select</option>
                                    <?php foreach($market_creation as $market_creation1){ ?>
                                    <option value="<?php echo $market_creation1['id']; ?>"><?php echo $market_creation1['area_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="dealer_name">Shop Type</label>
                                <select class="form-control select2" id="shop_type" width="100%">
                                    <option value="">Select</option>
                                    <?php foreach($shop_type as $shop_type1){ ?>
                                    <option value="<?php echo $shop_type1['id']; ?>"><?php echo $shop_type1['shops_type']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="tally_no">Purchase Times</label>
                                <input type="text" class="form-control" id="pur_time" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ledger_dr">Group Name </label>
                                <select class="form-control select2" id="group_id" width="100%"
                                    onchange='get_item_name()'>
                                    <option value="">Select</option>
                                    <?php foreach($group_creation as $group_creation){ ?>
                                    <option value="<?php echo $group_creation['id']; ?>"><?php echo $group_creation['group_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="unorder">Item Name</label>
                                <select class="form-control select2" id="item_id" width="100%">
                                    <option value="">Select</option>
                                    <?php foreach($item_creation as $item_creation){ ?>
                                    <option value="<?php echo $item_creation['id']; ?>"><?php echo $item_creation['short_code']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="to_date">Purchase Volume</label>
                                <input type="text" class="form-control" id="pur_volumn" />
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button class="btn btn-info"
                                    onclick="list_div(from_date.value,to_date.value,manager_id.value,sales_ref_id.value,dealer_name.value,area_name.value,shop_type.value,pur_time.value,group_id.value,item_id.value,pur_volumn.value)">GO</button>
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
    <div class="modal fade" data-keyboard="false" data-backdrop="static" id="bd-example-modal-lg1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Modal title1</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="model_main_content">
                    ...
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_content')
    <script>
        $(function() {
            if (user_rights_add_1 != '1') {
                $("#user_rights_add_div").remove();
            }
            if ((user_rights_add_1 != '1') && (user_rights_edit_1 != '1')) {
                $("#bd-example-modal-lg1").remove();
            }
        });
    </script>
    <script src="../assets/js/page/Reports/shop_report.js"></script>
@endsection
