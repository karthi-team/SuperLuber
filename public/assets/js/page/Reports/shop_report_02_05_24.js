function list_div(from_date,to_date,dealer_id,area_id,shop_type_id,pur_time,group_id,item_id,pur_volumn)
{
//   alert(from_date);
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();
  $('#list_div').html("");
  var dealer_id = $("#dealer_name").val();
  var area_id = $("#area_name").val();
  var shop_type_id = $("#shop_type").val();
  var pur_time = $("#pur_time").val();
  var group_id = $("#group_id").val();
  var item_id = $("#item_id").val();
  var pur_volumn = $("#pur_volumn").val();

  var sendInfo={"action":"retrieve","from_date":from_date,"to_date":to_date,"dealer_id":dealer_id,"area_id":area_id,"shop_type_id":shop_type_id,"user_rights_edit_1":user_rights_edit_1,"user_rights_delete_1":user_rights_delete_1,
  "group_id":group_id,
  "item_id":item_id,
  "pur_time":pur_time,
  "pur_volumn":pur_volumn};
  $.ajax({
    type: "GET",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){
      $('#list_div').html(data);
      console.log("erro");
    }
  });
}
$(function () {
  list_div();
});


function get_item_name()
{
  var group_id = $("#group_id").val();

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

function get_area_name()
{
  var dealer_name = $("#dealer_name").val();
// alert(dealer_name);
  var sendInfo={"action":"getareaname","dealer_name":dealer_name};


      $.ajax({
        type: "GET",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        dataType: "json",
        success: function(data) {
          $('#area_name').empty();

          $('#area_name').append('<option  value="" readonly>---Select---</option>');
          for(let i1=0;i1 < data.length;i1++){
            $('#area_name').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['area_name'] + '</option>');
       }
        }
      });
    }

      function get_shop_type()
      {
      var area_name = $("#area_name").val();
    //  alert(area_name);
      var sendInfo={"action":"getshoptype","area_name":area_name};


          $.ajax({
            type: "GET",
            url: $("#CUR_ACTION").val(),
            data: sendInfo,
            dataType: "json",
            success: function(data) {
              $('#shop_type').empty();

              $('#shop_type').append('<option  value="" readonly>---Select---</option>');
              for(let i1=0;i1 < data.length;i1++){
                $('#shop_type').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['shops_type'] + '</option>');
          }
            }
          });

}

function fromdate()
          {
            var from_date = $("#from_date").val();

            var sendInfo={"action":"retrieve","from_date":from_date};


            $.ajax({
              type: "GET",
              url: $("#CUR_ACTION").val(),
              data: sendInfo,
              dataType: "json",
              success: function(data) {
                $('#from_date').empty();
  
                $('#from_date').val(data);
                
            }
            });

          }