<style>
    table,
    td,
    th {
        border: 1px solid #000000;
    }
</style>
<div class="overflow-scroll">
    <div class="table-responsive">

        <table class="table table-sm table-hover" id="rights_tableExport" style="width:100%;">
            <thead style="position: sticky;top: 0">
                <tr>
                    <th rowspan="2">Sno</th>
                    {{-- <th class="text-center">Sales Rep Name</th> --}}
                    <th rowspan="2" class="text-center">Dealer Name</th>
                    @php
                        $groupCounts = [];
                    @endphp
                    @foreach ($group_creation as $group_creation1)
                        @php
                            $groupCounts[$group_creation1['id']] = 0;
                        @endphp
                        @foreach ($item_creation as $item_creation1)
                            @if ($group_creation1['id'] == $item_creation1['group_id'])
                                @php
                                    $groupCounts[$group_creation1['id']]++;
                                @endphp
                            @endif
                        @endforeach
                    @endforeach

                    @foreach ($group_creation as $group_creation1)
                        @if(isset($groupCounts[$group_creation1['id']]) && $groupCounts[$group_creation1['id']] > 0)
                            <th colspan="{{ $groupCounts[$group_creation1['id']] }}" style="border-bottom: 1px solid black;text-align:center;">
                                {{ $group_creation1['group_name'] }}
                            </th>
                        @endif
                    @endforeach
                </tr>

                <tr style="border-bottom: 1px solid black">
                    @foreach ($group_creation as $group_creation1)
                        @foreach ($item_creation as $item_creation1)
                            @if($group_creation1['id'] == $item_creation1['group_id'])
                                <th>{{ $item_creation1['short_code'] }}</th>
                            @endif
                        @endforeach
                    @endforeach
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i1 = 1;
                    $row_val = 1;
                @endphp

                @foreach ($dealer_creation as $dealer_creation1)
                    <tr>
                        <td style="position: sticky; top: 0">{{ $i1++ }}</td>

                        <td>{{ $dealer_creation1['dealer_name'] }}</td>
                        <input type='hidden' id='dealer_id_{{$row_val}}' class='dealer_id_{{$row_val}}' value="{{$dealer_creation1['id']}}" />
                        @php
                            $col_val = 1;
                        @endphp
                        @foreach ($group_creation as $group_creation1)
                            @foreach ($item_creation as $item_creation1)
                                @if($group_creation1['id'] == $item_creation1['group_id'])
                                    <td>
                                        <input type='hidden' id='group_id_{{$row_val}}_{{$col_val}}' class='group_id_{{$row_val}}_{{$col_val}}' value="{{$group_creation1['id']}}" />

                                        <input type='hidden' id='item_id_{{$row_val}}_{{$col_val}}' class='item_id_{{$row_val}}_{{$col_val}}' value="{{ $item_creation1['id'] }}" />

                                        <input type='number' id='target_quantity_{{$row_val}}_{{$col_val}}' style="width: 80px" class='form-control target_quantity_{{$row_val}}_{{$col_val}}'/>
                                    </td>
                                    @php
                                        $col_val++;
                                    @endphp
                                @endif
                            @endforeach
                        @endforeach
                    </tr>
                    @php
                        $row_val++;
                    @endphp
                @endforeach
                <input type="hidden" id="row_value" value="{{$row_val}}">
                <input type="hidden" id="col_value" value="{{$col_val}}">
            </tbody>
        </table>
    </div>
</div>
