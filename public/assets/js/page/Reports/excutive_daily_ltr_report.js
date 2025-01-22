function Excutive_sales(from_date,excutive_id,area_id,manager_na,group_id)
{
    var from_date_1 = $("#from_date").val();
    // alert(from_date_1)
  $('#list_div').html("");
  var sendInfo={"action":"view","from_date":from_date_1,"excutive_id":excutive_id,"area_id":area_id,'manager_na':manager_na,'group_id':group_id};
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){
      $('#list_div').html(data);

    }
  });
};
$(function () {
    Excutive_sales("", "", "", "", "", "");
});
function sales_mang(){

    var manager_na = $("#manager_na").val();
    var _token=$("#_token").val();
    $("#excutive_id").empty();
    $("#excutive_id").append('<option value="">Select dealer Name</option>');
    var sendInfo = {
        _token:_token,
        action: "getsalesexec",
        manager_na: manager_na,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {

            $('#excutive_id').empty();
            $('#excutive_id').append('<option value="">Select dealer Name</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#excutive_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['sales_ref_name'] + '</option>');
            }

        },
        error: function () {
            alert('Error fetching here');
        }
    });
}
function getdearlername() {

    var sales_exec = $("#excutive_id").val();
    var _token=$("#_token").val();
    $("#area_id").empty();
    $("#area_id").append('<option value="">Select dealer Name</option>');
    var sendInfo = {
        _token:_token,
        action: "getdearlername",
        sales_exec: sales_exec,
    };
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function (data) {

            $('#area_id').empty();
            $('#area_id').append('<option value="">Select dealer Name</option>');
            for (let i1 = 0; i1 < data.length; i1++) {
                $('#area_id').append('<option value="' + data[i1]['id'] + '">' + data[i1]['dealer_name'] + '</option>');
            }

        },
        error: function () {
            alert('Error fetching here');
        }
    });
}
