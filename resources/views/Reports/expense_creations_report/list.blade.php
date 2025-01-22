<div class="table-responsive">
    <table class="table table-hover" id="tableExport" style="width:100%;">
    <thead>
    <tr>
        <th>Sno</th>
        <th>Exp-Date</th>
        <th>Exp-Number</th>
        <th>Sal-Ref Name</th>
        <th>Market Manager Name</th>
        <th>Tr</th>
        <th>Fuel</th>
        <th>DA</th>
        <th>Courier</th>
        <th>Lodging</th>
        <th>Phone</th>
        <th>Others</th>
        <th>Total Amount</th>
        <th>Distance</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $i1=1;

    foreach($expense_creations_main as $expense_creations_main1)
    {
        $total_sublist=explode(";",$expense_creations_main1['total_sublist']);
        ?>
    <tr>
        <td><?php echo $i1;$i1++; ?></td>
        <td><?php echo $expense_creations_main1['expense_date']; ?></td>
        <td><?php echo $expense_creations_main1['expense_no']; ?></td>
        <td><?php echo $expense_creations_main1['sales_ref_name']; ?></td>
        <td><?php echo $expense_creations_main1['market_manager_name']; ?></td>
        <td><?php echo $expense_creations_main1['travel']; ?></td>
        <td><?php echo $expense_creations_main1['fuel']; ?></td>
        <td><?php echo $expense_creations_main1['da_1']; ?></td>
        <td><?php echo $expense_creations_main1['courier']; ?></td>
        <td><?php echo $expense_creations_main1['lodging']; ?></td>
        <td><?php echo $expense_creations_main1['phone']; ?></td>
        <td><?php echo $expense_creations_main1['others']; ?></td>
        <td><?php echo $expense_creations_main1['total']; ?></td>
        <td>
            <?php echo isset($expense_creations_main1['distance']) ? $expense_creations_main1['distance'] : 'No data'; ?>
        </td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
<script>
$(function () {
    $('#tableExport').DataTable({
        "dom": 'lBfrtip',
        "buttons": [
            {
                extend: 'excel',
                text: 'Excel',
                exportOptions: {
                    columns: [0, 1, 2,3,4,5,6,7,8,9,10,11,12,13]
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                //text: '<i class="far fa-file-pdf"></i>',
                exportOptions: {
                    columns: [0, 1, 2,3,4,5,6,7,8,9,10,11,12,13]
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: [0, 1, 2,3,4,5,6,7,8,9,10,11,12,13]
                }
            }
        ]
    });
});
</script>
