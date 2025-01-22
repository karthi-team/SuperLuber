<div class="table-responsive"><br>
    <table class="table table-hover" id="tableExport" style="width:100%;">
        <div class="table-responsive">
            <thead>
                <tr>
                    <th style="width: 5%;">Sno</th>
                    <th style="width: 8%;">Shop Name</th>
                    @if ($dealer_id)
                        <th style="width: 8%;">Place</th>
                    @endif
                    <th style="width: 10%;">Address</th>
                    <th style="width: 10%;">Shop Type</th>
                    <th style="width: 10%;">No of Visits</th>
                    <th style="width: 10%;">Productive Purchase</th>
                    <th style="width: 10%;">Productive Not Purchase</th>
                    <th style="width: 8%;">Purchase Ltrs</th>
                    <th style="width: 8%;">Last Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_No_of_Visits = 0;
                    $total_Productive_Purchase = 0;
                    $total_Productive_Not_Purchase = 0;
                    $total_Purchase_Ltrs = 0;
                @endphp
                @foreach ($list as $i => $lists)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $lists['Shop_Name'] }}</td>
                        @if ($dealer_id)
                            <td>{{ $lists['Place'] }}</td>
                        @endif
                        <td>{{ $lists['Address'] }}</td>
                        <td>{{ $lists['Shop_Type'] }}</td>
                        <td>{{ $lists['No_of_Visits'] > 0 ? $lists['No_of_Visits'] : '-' }}</td>
                        <td>{{ $lists['Productive_Purchase'] > 0 ? $lists['Productive_Purchase'] : '-' }}</td>
                        <td>{{ $lists['Productive_Not_Purchase'] > 0 ? $lists['Productive_Not_Purchase'] : '-' }}</td>
                        <td>{{ $lists['Purchase_Ltrs'] > 0 ? $lists['Purchase_Ltrs'] : '-' }}</td>
                        <td>{{ $lists['Last_Purchase_Date'] != '' ? $lists['Last_Purchase_Date'] : '-' }}</td>
                        @php
                            $total_No_of_Visits += $lists['No_of_Visits'];
                            $total_Productive_Purchase += $lists['Productive_Purchase'];
                            $total_Productive_Not_Purchase += $lists['Productive_Not_Purchase'];
                            $total_Purchase_Ltrs += $lists['Purchase_Ltrs'];
                        @endphp
                    </tr>
                @endforeach
            </tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                @if ($dealer_id)
                    <td></td>
                @endif
                <td style="background-color:rgb(117, 157, 231); color:white">TOTAL : </td>
                <td style="background-color:rgb(201, 207, 107);">{{ $total_No_of_Visits }}</td>
                <td style="background-color:rgb(117, 157, 231); color:white">{{ $total_Productive_Purchase }} </td>
                <td style="background-color:rgb(201, 207, 107);">{{ $total_Productive_Not_Purchase }}</td>
                <td style="background-color:rgb(117, 157, 231);">{{ $total_Purchase_Ltrs }}</td>
                <td></td>
            </tr>
        </div>
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
