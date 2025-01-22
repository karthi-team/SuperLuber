<style>
   
    </style>
<div class="table-responsive"><br>
    <table class="table table-hover myTablesValue alg" id="tableExport" style="width:100%;">
        <div class="table-responsive">
            <thead>
                <tr>
                    <th style="width: 5%;">Sno</th>
                    <th style="width: 8%;">Shop Name</th>
                    <th style="width: 10%;">Address</th>
                    <th style="width: 10%;">Shop Type</th>
                    <th style="width: 10%;">No of Products Purchase</th>
                    <th style="width: 8%;">Purchase Ltrs</th>
                    <th style="width: 8%;">Last Purchase Date</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
           <tfoot>
            
           </tfoot>
        </div>      
    </table>
</div>

@if(isset($list))

<div class="table-responsive"><br>
    <table class="table table-hover myTablesValue alg" id="tableExport" style="width:100%;">
        <div class="table-responsive">
            <tbody>
                @php
                $i = 1;
                $total_product_count = 0;
                $total_product_ltr = 0;
               
            @endphp
            @foreach($list as $lists)
                <tr>
                    <td>{{ $i }}</td>
                    <td width="170px">{{$lists->shop_name}}</td>
                    <td width="170px">
                        @unless($lists->address == '')
                        {{ucfirst($lists->address)}}
                        @else
                        -
                        @endunless
                    </td>
                    <td width="200px">{{$lists->shops_type}}</td>
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
                <td style="background-color:rgb(117, 157, 231); color:white">TOTAL : </td>
                <td style="background-color:rgb(201, 207, 107);">{{ $total_product_count }}</td>
                <td style="background-color:rgb(117, 157, 231);">{{ $total_product_ltr }}</td>
                <td ></td>
            </tr>
        </div>      
    </table>
</div>

@endif
