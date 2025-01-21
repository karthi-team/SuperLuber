
function list_div(from_date,to_date,manager_id,group_by,sales_ref_id,ref_by,dealer_id,group_id,item_id,sales_det) {
    $('#list_div').html("");
    var sendInfo = {
        "action": 'retrieve',
        "from_date": from_date,
        "to_date": to_date,
        "manager_id": manager_id,
        "group_by": group_by,
        "sales_ref_id": sales_ref_id,
        "ref_by": ref_by,
        "dealer_id": dealer_id,
        "group_id": group_id,
        "item_id": item_id,
        "sales_det": sales_det
    }
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {
            $('#list_div').html(data);
        }
    });
}

$(function () {
    list_div('', '', '', '', '', '');

    $("#group_by").attr("disabled","disabled");
    $("#sales_ref_id").attr("disabled","disabled");
    $("#ref_by").attr("disabled","disabled");
    $("#dealer_id").attr("disabled","disabled");
});

function change_manager_id(check1){
    $("#group_by").val("").trigger('change');
    $("#sales_ref_id").val("").trigger('change');
    $("#ref_by").val("").trigger('change');
    $("#dealer_id").val("").trigger('change');
    if(check1==''){
        $("#group_by").attr("disabled","disabled");
        $("#sales_ref_id").attr("disabled","disabled");
        $("#ref_by").attr("disabled","disabled");
        $("#dealer_id").attr("disabled","disabled");
    }else{
        $("#group_by").removeAttr("disabled");
        $("#sales_ref_id").removeAttr("disabled");
        $("#ref_by").attr("disabled","disabled");
        $("#dealer_id").attr("disabled","disabled");
    }
}
function change_group_by(check1){
    $("#sales_ref_id").val("").trigger('change');
    $("#ref_by").val("").trigger('change');
    $("#dealer_id").val("").trigger('change');
    if(check1!=''){
        $("#sales_ref_id").attr("disabled","disabled");
        $("#ref_by").attr("disabled","disabled");
        $("#dealer_id").attr("disabled","disabled");
    }else{
        $("#sales_ref_id").removeAttr("disabled");
        $("#ref_by").attr("disabled","disabled");
        $("#dealer_id").attr("disabled","disabled");
    }
}
function change_sales_ref_id(check1){
    $("#ref_by").val("").trigger('change');
    $("#dealer_id").val("").trigger('change');
    if(check1==''){
        $("#ref_by").attr("disabled","disabled");
        $("#dealer_id").attr("disabled","disabled");
    }else{
        $("#ref_by").removeAttr("disabled");
        $("#dealer_id").removeAttr("disabled");
    }
}
function change_ref_by(check1){
    $("#dealer_id").val("").trigger('change');
    if(check1!=''){
        $("#dealer_id").attr("disabled","disabled");
    }else{
        $("#dealer_id").removeAttr("disabled");
    }
}

function find_sales_ref()
{

  var manager_id = $("#manager_id").val();
  //alert(countryId)

  var sendInfo={"action":"getSalesRef","manager_id":manager_id};
    if (manager_id) {
      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
          $('#sales_ref_id').empty();
          $('#sales_ref_id').append('<option  value="" readonly>---Select---</option>');
          for(let i1=0;i1 < data.length;i1++){
            $('#sales_ref_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['sales_ref_name'] + '</option>');
       }
        }
      });
    } else {
      $('#sales_ref_id').empty();

    }

}

function find_dealer_name()
{

  var sales_ref_id = $("#sales_ref_id").val();

  //alert(countryId)

  var sendInfo={"action":"getDealerName","sales_ref_id":sales_ref_id};
    if ( sales_ref_id ) {

      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
          $('#dealer_id').empty();

          $('#dealer_id').append('<option  value="" readonly>---Select---</option>');
          for(let i1=0;i1 < data.length;i1++){
            $('#dealer_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['dealer_name'] + '</option>');
       }
        }
      });
    } else {
      $('#dealer_id').empty();

    }

}
function get_item_name()
{

  var group_id = $("#group_id").val();

  //alert(countryId)

  var sendInfo={"action":"getitemName","group_id":group_id};


      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
          $('#item_id').empty();

          $('#item_id').append('<option  value="" readonly>---Select---</option>');
          for(let i1=0;i1 < data.length;i1++){
            $('#item_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['short_code'] + '</option>');
       }
        }
      });

}
