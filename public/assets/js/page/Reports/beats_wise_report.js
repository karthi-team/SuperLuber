function list_div(from_date,to_date,dealer_id,beats_id,shop_id,shop_type)
{
  $('#list_div').html("");
  var sendInfo={"action":"retrieve","from_date":from_date,"to_date":to_date,"dealer_id":dealer_id,"beats_id":beats_id,"shop_id":shop_id,"shop_type":shop_type};
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){
      $('#list_div').html(data);

    }
  });
}
$(function () {
  list_div('','','','','');
});

function find_beats()
{
  var dealer_id = $("#dealer_id").val();
  var sendInfo={"action":"find_beats","dealer_id":dealer_id};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
            $('#beats_id').empty();
            $('#beats_id').append('<option value="" readonly>Select</option>');
            for (let i1 = 0; i1 < data.id.length; i1++) {
                $('#beats_id').append('<option value="' + data.id[i1] + '">' + data.area_name[i1] + '</option>');
            }
        }
    });
}

function find_shop_type()
{
  var beats_id = $("#beats_id").val();
  var sendInfo={"action":"find_shop_type","beats_id":beats_id};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
            $('#shop_type').empty();
            $('#shop_type').append('<option value="" readonly>-Select-</option>');
            for (let i2 = 0; i2 < data.length; i2++) {
                $('#shop_type').append('<option value="' + data[i2]['id'] + '">' + data[i2]['shops_type'] + '</option>');
            }
        }
    });
}

function find_shop_name()
{
  var beats_id = $("#beats_id").val();
  var shop_type = $("#shop_type").val();
  var sendInfo={"action":"find_shop_name","beats_id":beats_id,"shop_type":shop_type};
    $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
            $('#shop_id').empty();
            $('#shop_id').append('<option value="" readonly>Select</option>');
            for (let i3 = 0; i3 < data.length; i3++) {
                $('#shop_id').append('<option value="' + data[i3]['id'] + '">' + data[i3]['shop_name'] + '</option>');
            }
        }
    });
}
