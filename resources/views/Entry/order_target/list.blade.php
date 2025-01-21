
<div class="table-responsive">
    <table class="table table-hover" id="tableExport" style="width:100%;">
        <thead>
            <tr class="stl">
                <th>Sno</th>
                <th>Entry Date</th>
                <th class="text-center">Target Number</th>
                <th class="text-center">Sales Executive Name</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
    $i1=1;
    foreach($order_target as $order_target1)
    { ?>
            <tr>
                <td class="stl"><?php echo $i1;
                $i1++; ?></td>
                <td><?php echo $order_target1['entry_date']; ?></td>

               <td align="center" class="stl">
                <?php echo $order_target1['target_number']; ?>
                </td>

                <td align="center" class="stl"><?php echo $order_target1['sales_executive_name']; ?></td>

                <td align="center" class="stl">
                    <a class="btn btn-icon icon-left btn-info hover-gradient" data-toggle="tooltip" style="cursor: pointer"
                        title="Edit" onclick="open_model('Update Order Target','<?php echo $order_target1['id']; ?>')"><i
                            class="far fa-edit"></i>Edit</a>
                    <a class="btn btn-icon icon-left btn-danger" data-toggle="tooltip" style="cursor: pointer" title="Delete"
                        onclick="delete_row('<?php echo $order_target1['id']; ?>')"><i class="fas fa-trash"></i>Delete</a>
                </td>
                </tr>
                <?php } ?>
                </tbody>
                </table>
                </div>
<script>
    $(function() {
        $('#tableExport').DataTable({
            "dom": 'lBfrtip',
            "buttons": [
                'excel', 'pdf', 'print'
            ]
        });
    });
</script>
