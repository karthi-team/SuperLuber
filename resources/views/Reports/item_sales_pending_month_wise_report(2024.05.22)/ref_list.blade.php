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
                @if($sales_rep->isNotEmpty())
                    @php
                        $salesRepName = $sales_rep[0]->sales_ref_name;
                    @endphp
                    <th colspan="4" style="color: black;"><strong>{{ $salesRepName }}</strong></th>
                @else
                    <th colspan="4" style="color: black;">Sales Ref ID: No Sales Rep Found</th>
                @endif
            </tr>
            <tr style="border-bottom: 1px solid black">
                <th style="border-bottom: 1px solid black">S.NO</th>
                <th style="border-bottom: 1px solid black">Dealer Name</th>
                @foreach ($uniqueGroupNames as $groupName)
                    <th colspan="2" style="background-color: #fff8a3ad; border-bottom: 1px solid black;"><center>{{ $groupName->group_name }}</center></th>
                @endforeach
            </tr>
            <tr style="border-bottom: 1px solid black">
                <th></th>
                <th></th>
                @foreach ($uniqueGroupNames as $groupName)
                    <th style="background-color: #4dffe6ad;">Dispatched</th>
                    <th style="background-color: #e98ed185;">Pending</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @php
                $i1 = 1;
                $columnSums = array_fill(0, count($uniqueGroupNames) * 2, 0);
            @endphp

            @foreach ($dealerQuery as $sales_order_1)
                <tr>
                    <td>{{ $i1++ }}</td>
                    <td>{{ $sales_order_1->dealer_name }}</td>

                    @php
                        $colIndex = 0;
                    @endphp
                    @foreach ($uniqueGroupNames as $group)
                        @php
                            $matchingPending = $pendings
                                ->where('dealer_socm', $sales_order_1->dealer_id)
                                ->where('group_creation_id', $group->id)
                                ->first();

                            $balanceQuantity = $matchingPending ? $matchingPending->total_balance_quantity_sodsc : 0;
                            $orderQuantity = $matchingPending ? $matchingPending->total_order_quantity_socm : 0;

                            $balanceQuantity = ($balanceQuantity == $orderQuantity) ? 0 : ($balanceQuantity - $orderQuantity);

                            $columnSums[$colIndex] += abs($balanceQuantity);
                            $columnSums[$colIndex + 1] += abs($balanceQuantity + $orderQuantity);
                        @endphp

                        <td>{{ abs($balanceQuantity) }}</td>
                        <td>{{ abs($balanceQuantity + $orderQuantity) }}</td>

                        @php
                            $colIndex += 2;
                        @endphp
                    @endforeach
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td></td>
                <td colspan="1" style="background-color: #f0f0f0;"><center>Total:</center></td>
                @foreach ($columnSums as $sum)
                    <td>{{ $sum }}</td>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>






<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<script>
    $(document).ready(function () {
        $('#tableExport').DataTable({
            paging: false
        });
    });
</script>

