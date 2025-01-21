<div class="row">
    <div class="col-md-12">
        <table class="table table-sm table-hover" id="rights_tableExport" style="width:100%;">
            <thead>
                <tr align="center" class="stl">
                    <th>Sno</th>
                    <th>Place</th>
                    <th>Dealer Name</th>
                    <th>Mobile No</th>
                    {{-- <th>Address</th> --}}
                </tr>
            </thead>
            <tbody>
                <?php
                $i1 = 1;
                $cnt_index = 0;

                foreach ($dealer_query as $dealer_query_1) { ?>
                    <tr align="center">
                        <td class="stl"><?php echo $i1; $i1++; ?></td>
                        <td class="stl"><?php echo $dealer_query_1['place']; ?></td>
                        <td class="stl"><?php echo $dealer_query_1['dealer_name']; ?></td>
                        <td class="stl"><?php echo $dealer_query_1['mobile_no']; ?></td>
                        {{-- <td class="stl"><?php echo $dealer_query_1['address']; ?></td> --}}
                    </tr>
                    <?php
                    $cnt_index++;
                } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
var rights_tableExport_rows=null;
$(function () {
    $(".user_type_options").select2();
    rights_tableExport_rows=$('#rights_tableExport').DataTable({"dom": 'frtip',"pageLength": 10});
});
</script>
