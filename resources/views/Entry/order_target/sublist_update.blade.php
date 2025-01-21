<div class="overflow-scroll">
    <div class="table-responsive">
        <table class="table table-sm table-hover" id="rights_tableExport" style="width:100%;">
            <thead style="position: sticky;top: 0; border: 1px solid #000000;">
                <tr>
                    <th rowspan="2" style="border: 1px solid #000000;">Sno</th>
                    <th rowspan="2" class="text-center" style="border: 1px solid #000000;">Dealer Name</th>
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
                            <th colspan="{{ $groupCounts[$group_creation1['id']] }}" style="border-bottom: 1px solid black;text-align:center;border: 1px solid #000000;">
                                {{ $group_creation1['group_name'] }}
                            </th>
                        @endif
                    @endforeach
                </tr>
                <tr style="border-bottom: 1px solid black">
                    @foreach ($group_creation as $group_creation1)
                        @foreach ($item_creation as $item_creation1)
                            @if($group_creation1['id'] == $item_creation1['group_id'])
                                <th style="border: 1px solid #000000;">{{ $item_creation1['short_code'] }}</th>
                            @endif
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $i1 = 1;
                    $row_val = 1;
                    $order_target_json = json_decode($order_target_sublist['order_target'], true);
                @endphp

                @foreach ($dealer_creation as $dealer_creation1)
                    <tr>
                        <td style="position: sticky; top: 0; border: 1px solid #000000;">{{ $i1++ }}</td>

                        <td style="border: 1px solid #000000;">
                            {{ $dealer_creation1['dealer_name'] }}
                            @php
                                $dealer_id = '0';
                                foreach ($order_target_json as $target) {
                                    if ($target['dealer_id'] == $dealer_creation1['id']) {
                                        $dealer_id = $target['dealer_id'];
                                        break;
                                    } else {
                                        $dealer_id = $dealer_creation1['id'];
                                        break;
                                    }
                                }
                            @endphp
                            <input type='hidden' id='dealer_id_{{$row_val}}' class='dealer_id_{{$row_val}}' value="{{ $dealer_id }}" />
                        </td>
                        @php
                            $col_val = 1;
                            $target_data = null;
                        @endphp

                        @foreach ($order_target_json as $target)
                            @if ($target['dealer_id'] == $dealer_creation1['id'])
                            @php
                                $target_data = $target;
                                break;
                            @endphp
                            @endif
                        @endforeach

                        @foreach ($group_creation as $group_creation1)
                            @foreach ($item_creation as $item_creation1)
                                @if($group_creation1['id'] == $item_creation1['group_id'])
                                    @php
                                        $target_quantity = '0';
                                        if ($target_data && isset($target_data['groupDetails'])) {
                                            foreach ($target_data['groupDetails'] as $group) {
                                                if ($group['group_id'] == $group_creation1['id']) {
                                                    foreach ($group['itemDetails'] as $item) {
                                                        if ($item['item_id'] == $item_creation1['id']) {
                                                            $target_quantity = $item['target_quantity'];
                                                            break 2;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <td style="border: 1px solid #000000;vertical-align: middle;">
                                        <input type='hidden' id='group_id_{{$row_val}}_{{$col_val}}' class='group_id_{{$row_val}}_{{$col_val}}' value="{{ $group_creation1['id'] }}" />
                                        <input type='hidden' id='item_id_{{$row_val}}_{{$col_val}}' class='item_id_{{$row_val}}_{{$col_val}}' value="{{ $item_creation1['id'] }}" />
                                        <input type='number' id='target_quantity_{{$row_val}}_{{$col_val}}' style="width: 80px" class='form-control target_quantity_{{$row_val}}_{{$col_val}}' value="{{ $target_quantity }}" />
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
