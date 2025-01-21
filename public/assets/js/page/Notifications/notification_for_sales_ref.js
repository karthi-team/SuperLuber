function list_div()
{
  var _token1=$("#_token1").val();
    $('#list_div').html("");
    var sendInfo={"action":"retrieve","_token":_token1,"user_rights_edit_1":user_rights_edit_1,"user_rights_delete_1":user_rights_delete_1};
  $.ajax({
    type: "POST",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){

      $('#list_div').html(data);
      $(function () {
        $('#tableExport').DataTable({
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Excel',
                    exportOptions: {
                        columns: [0, 1, 2,3 ]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    //text: '<i class="far fa-file-pdf"></i>',
                    exportOptions: {
                        columns: [0, 1, 2,3 ]
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    exportOptions: {
                        columns: [0, 1, 2,3 ]
                    }
                }
            ]
        });
    });
    }
  });
}
$(function () {
  list_div();
});
function open_model(title,id)
{
$('#bd-example-modal-lg1 #model_main_content').html("...");
var sendInfo={};
var _token1=$("#_token1").val();
if(id==""){sendInfo={"action":"create_form","_token":_token1};}
else{sendInfo={"action":"update_form","_token":_token1,"id":id};}
  $.ajax({
    type: "POST",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    success: function(data){
      $('#bd-example-modal-lg1').modal('show');
      $('#bd-example-modal-lg1 #myLargeModalLabel').html(title);
      $('#bd-example-modal-lg1 #model_main_content').html(data);
    },
    error: function() {
      alert('error handing here');
    }
  });
}
function insert_update_row(id, group_id, item_id, description, datetime,before_login_or_after_login) {

    var checkbox = document.getElementById('checkbox').checked ? 1 : 0;

    var sales_ref_name_1 = [];
    var sales_ref_name = document.getElementsByClassName('sales_ref_name');
    for (let i1 = 0; i1 < sales_ref_name.length; i1++) {
        sales_ref_name_1.push(sales_ref_name[i1].value);
    }
    var notification_status_1 = [];
    var notification_status = document.getElementsByClassName('notification_status');
    for (let i1 = 0; i1 < notification_status.length; i1++) {
        notification_status_1.push(notification_status[i1].value);
    }

    var _token1=$("#_token1").val();
    var formData = new FormData();
    var action = id === "" ? "insert" : "update";
    formData.append("_token", _token1);
    formData.append('action', action);
    formData.append('group_id', group_id);
    formData.append('item_id', item_id);
    formData.append('description', description);
    formData.append('datetime', datetime);
    formData.append('checkbox', checkbox);
    formData.append('sales_ref_name', sales_ref_name_1.toString());
    formData.append('notification_status', notification_status_1.toString());
    formData.append('before_login_or_after_login', before_login_or_after_login);
    var imageFiles = $("#image_name")[0].files;
    for (var i = 0; i < imageFiles.length; i++) {
        formData.append("image_name[]", imageFiles[i]);
    }
    if (action === "update") {
        formData.append('id', id);
    }

    $.ajax({
        type: "POST",
        url: $("#CUR_ACTION").val(),
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#bd-example-modal-lg1').modal('hide');
            if (response.error) {
                swal(response.error, { icon: 'error' });
            } else {
                var message = response.message || 'Record ' + (action === "insert" ? 'inserted' : 'updated') + ' successfully';
                swal(message, { icon: 'success' });
                list_div();
            }
        },
        error: function() {
            alert('Error handling here');
        }
    });
}

let selectedFiles = [];

function previewImage(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('image_previews');
    previewContainer.innerHTML = ''; // Clear any existing previews
    selectedFiles = Array.from(files); // Store selected files

    selectedFiles.forEach((file, index) => {
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imageUrl = event.target.result;

                // Create a container for each image preview
                const imageWrapper = document.createElement('div');
                imageWrapper.className = 'image_wrapper';
                imageWrapper.style.position = 'relative';
                imageWrapper.style.display = 'inline-block';
                imageWrapper.style.margin = '10px';

                // Create the image element
                const imagePreview = document.createElement('img');
                imagePreview.src = imageUrl;
                imagePreview.style.width = '75px';
                imagePreview.style.height = '75px';

                // Create the remove button
                const removeButton = document.createElement('button');
                removeButton.textContent = 'Remove';
                removeButton.style.position = 'absolute';
                removeButton.style.top = '5px';
                removeButton.style.right = '5px';
                removeButton.style.backgroundColor = 'red';
                removeButton.style.color = 'white';
                removeButton.style.border = 'none';
                removeButton.style.cursor = 'pointer';

                // Attach the remove functionality
                removeButton.onclick = function() {
                    previewContainer.removeChild(imageWrapper);
                    selectedFiles.splice(index, 1);
                    updateInputFiles();
                    previewImage({ target: { files: selectedFiles } });
                };

                // Append the image and remove button to the wrapper
                imageWrapper.appendChild(imagePreview);
                imageWrapper.appendChild(removeButton);

                // Append the wrapper to the preview container
                previewContainer.appendChild(imageWrapper);
            };
            reader.readAsDataURL(file);
        }
    });

    updateInputFiles(); // Update the input files
}

function updateInputFiles() {
    const input = document.getElementById('image_name');
    const dataTransfer = new DataTransfer();

    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    input.files = dataTransfer.files;
}

function find_item_id()
{
  var _token1=$("#_token1").val();
  var group_id = $("#group_id").val();
  var sendInfo={"action":"getitemname","_token":_token1,"group_id":group_id};
  $.ajax({
    type: "POST",
    url: $("#CUR_ACTION").val(),
    data: sendInfo,
    dataType: "json",
    success: function(data) {
      $('#item_id').empty();
      $('#item_id').append('<option  value="">Select</option>');
      for(let i1=0;i1 < data.length;i1++){
        $('#item_id').append('<option  value="' + data[i1]['id'] + '">' + data[i1]['item_name'] + '</option>');
      }
    },
    error: function () {
        alert("Error fetching Group Name");
    },
  });
}






function delete_row(id)
{
  var _token1=$("#_token1").val();

  swal({
    title: 'Are you sure?',
    text: 'To delete State Creation',
    icon: 'warning',
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      var sendInfo={"action":"delete","_token":_token1,"id":id};
      $.ajax({
        type: "POST",
        url: $("#CUR_ACTION").val(),
        data: sendInfo,
        success: function(data){
          swal('Deleted Successfully', {icon: 'success',});
          list_div();
        },
        error: function() {
          alert('error handing here');
        }
      });
    }
  });
}
