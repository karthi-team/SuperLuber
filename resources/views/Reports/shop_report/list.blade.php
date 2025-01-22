<div class="table-responsive"><br>
    <table class="table table-hover" id="tableExport" style="width:100%;">
        <thead>
            <tr>
                <th style="width: 5%;">Sno</th>
                <th style="width: 10%;">Shop Name</th>
                <th style="width: 10%;">Place</th>
                <th style="width: 10%;">Market</th>
                <th style="width: 15%;">Address</th>
                <th style="width: 8%;">Shop Type</th>
                <th style="width: 8%;">No of Visits</th>
                <th style="width: 8%;">Productive Purchase</th>
                <th style="width: 8%;">Not Productive</th>
                <th style="width: 8%;">Purchase Ltrs</th>
                <th style="width: 10%;">Last Purchase Date</th>
            </tr>
        </thead>
        <?php if(count($list)){ ?>
        <tbody>
            <?php
            $total_No_of_Visits = 0;
            $total_Productive_Purchase = 0;
            $total_Productive_Not_Purchase = 0;
            $total_Purchase_Ltrs = 0;
            $cnt = 1;
            foreach ($list as $lists) {
                echo "<tr>
                    <td>$cnt</td>
                    <td><div style='width:150px;'>$lists[Shop_Name]</div></td>
                    <td><div style='width:150px;'>$lists[Place]</div></td>
                    <td><div style='width:150px;'>$lists[Market]</div></td>
                    <td><div style='width:150px;'>$lists[Address]</div></td>
                    <td><div style='width:150px;'>$lists[Shop_Type]</div></td>
                    <td>".($lists['No_of_Visits'] > 0 ? $lists['No_of_Visits'] : '-')."</td>
                    <td>".($lists['Productive_Purchase'] > 0 ? $lists['Productive_Purchase'] : '-')."</td>
                    <td>".($lists['Productive_Not_Purchase'] > 0 ? $lists['Productive_Not_Purchase'] : '-')."</td>
                    <td>".($lists['Purchase_Ltrs'] > 0 ? $lists['Purchase_Ltrs'] : '-')."</td>
                    <td>".($lists['Last_Purchase_Date'] != '' ? $lists['Last_Purchase_Date'] : '-')."</td>
                </tr>";
                $total_No_of_Visits += $lists['No_of_Visits'];
                $total_Productive_Purchase += $lists['Productive_Purchase'];
                $total_Productive_Not_Purchase += $lists['Productive_Not_Purchase'];
                $total_Purchase_Ltrs += $lists['Purchase_Ltrs'];
                $cnt++;
            } ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="background-color:rgb(117, 157, 231); color:white">TOTAL : </td>
                <td style="background-color:rgb(201, 207, 107);">{{ $total_No_of_Visits }}</td>
                <td style="background-color:rgb(117, 157, 231); color:white">{{ $total_Productive_Purchase }} </td>
                <td style="background-color:rgb(201, 207, 107);">{{ $total_Productive_Not_Purchase }}</td>
                <td style="background-color:rgb(117, 157, 231);">{{ $total_Purchase_Ltrs }}</td>
                <td></td>
            </tr>
        </tfoot>
        <?php } ?>
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
