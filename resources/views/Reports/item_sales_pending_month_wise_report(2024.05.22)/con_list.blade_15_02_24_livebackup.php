<button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0" aria-controls="tableExport" onclick="exportToCSV()">Excel</button>
<button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="tableExport" onclick="generatePDF()">PDF</button>
<button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="tableExport" onclick="printTable()">Print</button>
<style>
    .select2-container .select2-selection--single .select2-selection__rendered {
    padding-right: 128px;
}
.green-background {
    background-color: #ADD8E6;
}

</style>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- DataTables JavaScript -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="assets/bundles/pretty-checkbox/pretty-checkbox.min.css">
<br><br>
<div class="table-responsive">
    <table class="table table-hover" id="tableExport" style="width:100%;" border="1">
        <thead>
            <tr style="border-bottom: 1px solid black">
                <th style="border-bottom: 1px solid black">S.NO</th>
                <th style="border-bottom: 1px solid black">Sales Ref Name</th>
                @foreach ($uniqueGroupNames as $groupName)
                    <th style="background-color: #4dffe6ad;">{{ $groupName->group_name }}<br>SALES</th>
                    <th style="background-color: #e98ed185;">Pending</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @php
                $i1 = 1;
            @endphp

            @foreach ($dealerQuery as $sales_order_1)
                <tr>
                    <td>{{ $i1++ }}</td>
                    <td>{{ $sales_order_1->sales_ref_name }}</td>

                    @foreach ($uniqueGroupNames as $group)
                        @php
                            $matchingPending = $pendings
                                ->where('sales_exec_socm', $sales_order_1->sales_ref_id)
                                ->where('group_creation_id', $group->id)
                                ->first();

                            $balanceQuantity = $matchingPending ? $matchingPending->total_balance_quantity_sodsc : 0;
                            $orderQuantity = $matchingPending ? $matchingPending->total_order_quantity_socm : 0;

                            $balanceQuantity = ($balanceQuantity == $orderQuantity) ? 0 : ($balanceQuantity - $orderQuantity);
                        @endphp

                        <td>{{ abs($balanceQuantity) }}</td>
                        <td>{{ abs($balanceQuantity + $orderQuantity) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <td hidden></td>
            <td colspan="2" style="background-color: #f0f0f0;"><center>Total:</center></td>

            @foreach ($uniqueGroupNames as $group)
                @php
                    $totalPending = 0;
                    $totalOrder = 0;
                @endphp
                @foreach ($dealerQuery as $sales_order_1)
                    @php
                        $matchingPending = $pendings
                            ->where('sales_exec_socm', $sales_order_1->sales_ref_id)
                            ->where('group_creation_id', $group->id)
                            ->first();

                        $balanceQuantity = $matchingPending ? $matchingPending->total_balance_quantity_sodsc : 0;
                        $orderQuantity = $matchingPending ? $matchingPending->total_order_quantity_socm : 0;

                        $balanceQuantity = ($balanceQuantity == $orderQuantity) ? 0 : ($balanceQuantity - $orderQuantity);

                        $totalPending += $balanceQuantity;
                        $totalOrder += $balanceQuantity + $orderQuantity;
                    @endphp
                @endforeach
                <td style="background-color: #4dffe6ad;">{{ abs($totalPending) }}</td>
                <td style="background-color: #e98ed185;">{{ abs($totalOrder) }}</td>
            @endforeach
        </tfoot>
    </table>
</div>








<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tableExport').DataTable({
            paging: true,
            searching: true
            // Add other DataTables options as needed
        });
    });
</script>
