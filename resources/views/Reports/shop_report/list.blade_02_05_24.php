
<div class="table-responsive"><br>
    <table class="table table-hover" id="tableExport" style="width:100%;">
        <div class="table-responsive">
            <thead>
                <tr>
                    <th style="width: 5%;">Sno</th>
                    <th style="width: 8%;">Shop Name</th>
                    @if($dealer_id)
                    <th style="width: 8%;">Place</th>
                    @endif
                    <th style="width: 10%;">Address</th>
                    <th style="width: 10%;">Shop Type</th>
                    <th style="width: 10%;">No of Products Purchase</th>
                    <th style="width: 8%;">Purchase Ltrs</th>
                    <th style="width: 8%;">Last Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                    $total_product_count = 0;
                    $total_product_ltr = 0;
                   
                @endphp
                @foreach($list as $lists)
                    <tr>
                        {{-- <td>{{ $i }}</td>
                        <td>{{$lists->shop_name}}</td>
                        <td>{{ucfirst($lists->address)}}</td>
                        <td>{{$lists->shops_type}}</td>
                        @foreach ($arr_val as $arr_item)
                        @foreach ($arr_item['getvalue'] as $getvalueItem)
                            @if($getvalueItem->shop_creation_id == $lists->id)
                                <td>{{ $arr_item['getvalues_cnt'] }}</td>
                            @endif
                            @if($getvalueItem->shop_creation_id == $lists->id)
                                <td>{{ $arr_item['getvalues'] }}</td>
                            @endif
                            @if($getvalueItem->shop_creation_id == $lists->id)
                                <td>{{ $arr_item['dateval'] }}</td>
                            @endif
                            @break(2)
                        @endforeach
                        
                        @endforeach --}}

                        <td>{{ $i }}</td>
                        <td>{{$lists->shop_name}}</td>

                        @if($dealer_id)
                        <td> {{$lists->place}} </td>
                        @endif
                        

                        <td>
                            @unless($lists->address == '')
                            {{ucfirst($lists->address)}}
                            @else
                            -
                            @endunless
                        </td>
                        <td>{{$lists->shops_type}}</td>
                        <td>
                            @unless($lists->order_quantity == 0)
                                {{ $lists->order_quantity }}
                            @else
                                -
                            @endunless
                        </td>
                        <td>
                            @unless($lists->pieces_quantity == '')
                            {{ucfirst($lists->pieces_quantity)}}
                            @else
                            -
                            @endunless
                        </td>
                        <td>
                            @unless($lists->order_date == '')
                            {{$lists->order_date}}
                            @else
                            -
                            @endunless
                        </td>

                    </tr>
                    @php
                        $i++;
                        $total_product_count+=$lists->order_quantity;
                        $total_product_ltr+=$lists->pieces_quantity;
                    @endphp
                @endforeach 
            </tbody>
            <tr>
                <td ></td>
                <td ></td>
                <td ></td>
                @if($dealer_id)
                <td ></td>
                @endif
                <td style="background-color:rgb(117, 157, 231); color:white">TOTAL : </td>
                <td style="background-color:rgb(201, 207, 107);">{{ $total_product_count }}</td>
                <td style="background-color:rgb(117, 157, 231);">{{ $total_product_ltr }}</td>
                <td ></td>
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
