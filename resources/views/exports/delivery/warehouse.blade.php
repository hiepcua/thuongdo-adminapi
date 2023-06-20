<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<table style="font-family: Roboto, sans-serif">
    <tr>
        <td colspan="14"></td>
    </tr>
    <tr>
        <td colspan="9"></td>
        <td colspan="5" style="font-weight: bold; text-align: right;font-size: 10px">{{$organization->name}}</td>
    </tr>
    <tr>
        <td colspan="9"></td>
        <td colspan="5" style="text-align: right;font-size: 10px">Địa chỉ: <span>&nbsp;</span> {{$organization->address}}</td>
    </tr>
    <tr>
        <td colspan="9"></td>
        <td colspan="5" style="text-align: right;font-size: 10px">MST: <span>&nbsp;</span> {{$organization->tax_code}} <span>&nbsp;</span> - <span>&nbsp;</span>
            SĐT: <span>&nbsp;</span> {{$organization->phone_number}}</td>
    </tr>
    <tr>
        <td colspan="14"
            style="text-align: center;text-transform: uppercase;font-size: 12px;font-weight: bold;height: 25px;vertical-align: middle">
            PHIẾU XUẤT KHO KIÊM BIÊN BẢN BÀN GIAO
        </td>
    </tr>
    <tr>
        <td colspan="14"
            style="text-align: center;font-size: 8px;vertical-align: middle">
            Ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
        </td>
    </tr>
    <tr>
        <td colspan="14" align="right"> Số: {{$no}}</td>
    </tr>
    <tr>
        <td colspan="14">
            <b>Họ tên người nhận hàng: </b> <span>&nbsp;</span> {{ optional($receiver)->receiver }}
        </td>

    </tr>
    <tr>
        <td colspan="14">
            <b>Điện thoại: </b> <span>&nbsp;</span> {{ optional($receiver)->phone_number }}
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <b>Đơn vị: </b> <span>&nbsp;</span> Khách lẻ
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <b>Lý do xuất kho: </b> <span>&nbsp;</span> Xuất hàng trả khách
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <b>Xuất tại kho: </b> <span>&nbsp;</span> {{ optional($packages->first()->warehouse)->name }}
        </td>
    </tr>
    <tr>
        <td colspan="14"></td>
    </tr>
    <tr>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            STT
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Mã vận đơn
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Số lương / ĐVT
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Đơn Giá
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Thành Tiền
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Kiểm đếm
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Bảo hiểm
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Đóng gỗ
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Chống sốc
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Lưu kho
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            VC nước ngoài
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Chiết khấu
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Tổng tiền
        </th>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Ghi chú
        </th>
    </tr>
    @php
        $totalAmount = 0;
    @endphp
    @foreach($packages as $key => $package)
        @php
            $isVolume = $package->volume > 0;
            $unit = $isVolume ? $package->volume : $package->weight;
            $totalAmount += $package->amount;
        @endphp
        <tr>
            <td width="5" style="text-align: center;border:0.5px solid #333;height:25px">{{ ++$key }}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ $package->bill_code }}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ $unit . ($isVolume ? 'm3' : 'kg') }}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ $unit ? \App\Helpers\ConvertHelper::numericToVND(\App\Helpers\AccountingHelper::getCosts($package->international_shipping_cost / $unit)) : 0 }}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->international_shipping_cost) }}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->inspection_cost)}}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->insurance_cost)}}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->woodworking_cost)}}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->shock_proof_cost)}}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->storage_cost)}}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->china_shipping_cost)}}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->discount_cost)}}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($package->amount)}}</td>
            <td width="15"
                style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ $package->note }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="10"
            style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;"></td>
        <td colspan="2"
            style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;font-weight: bold;font-size: 11px">
            Tổng tiền vận chuyển
        </td>
        <td colspan="1"
            style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;">{{ \App\Helpers\ConvertHelper::numericToVND($totalAmount) }}</td>
        <td colspan="1"
            style="border:0.5px solid #333;height:25px;vertical-align: middle;text-align:center;"></td>
    </tr>
    <tr>
        <td colspan="14"></td>
    </tr>
    <tr>
        <td colspan="14"></td>
    </tr>
    <tr>
        <td colspan="14" style="font-weight: bold">Thông tin đơn hàng</td>
    </tr>
    <tr>
        <td colspan="14"></td>
    </tr>

    <tr>
        <th style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            STT
        </th>
        <th colspan="2"
            style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Mã đơn hàng
        </th>
        <th colspan="5"
            style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">

        </th>
        <th colspan="2" style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Tổng tiền
        </th>
        <th colspan="2" style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Đã thanh toán
        </th>
        <th colspan="2" style=" text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            Còn thiếu
        </th>
    </tr>
    @php
        $total = 0;
    
    @endphp
    @foreach($orders as $key => $order)
        @php
            $total+= $result = $order->isLatest ? ($order->total_amount - $order->deposit_cost) : 0;
        @endphp
        <tr>
            <th style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
                {{ ++$key }}
            </th>
            <th colspan="2" style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
                {{ $order->code }}
            </th>
            <th colspan="5"
                style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
            </th>
            <th colspan="2" style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
                {{ \App\Helpers\ConvertHelper::numericToVND($order->total_amount) }}
            </th>
            <th colspan="2" style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
                {{ \App\Helpers\ConvertHelper::numericToVND($order->deposit_cost) }}
            </th>
            <th colspan="2" style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">
                {{ \App\Helpers\ConvertHelper::numericToVND($result) }}
            </th>
        </tr>
    @endforeach
    @php
        $balance = $total;
        $totalPurchase = $balance + $totalAmount;
    @endphp
    <tr>
        <td rowspan="3" colspan="8" style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;"></td>
        <td colspan="4" style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;text-align: right">Tổng
            tiền
        </td>
        <td colspan="2"
            style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">{{ \App\Helpers\ConvertHelper::numericToVND($balance) }}</td>
    </tr>
    <tr>
        <td colspan="4" style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;text-align: right">Tổng
            tiền cần thanh toán
        </td>
        <td colspan="2"
            style="text-align:center; border:0.5px solid #333;height:25px;vertical-align: middle;">{{ \App\Helpers\ConvertHelper::numericToVND($totalPurchase) }}</td>
    </tr>
    <tr>
        <td colspan="6" style="border:0.5px solid #333;height:25px;vertical-align: middle;font-size: 8px; text-align: center">Bằng
            chữ: {{ convert_number_to_words(\App\Helpers\AccountingHelper::getCosts($totalPurchase)) }} đồng
        </td>
    </tr>
    <tr>
        <td colspan="14"></td>
    </tr>
    <tr>
        <td colspan="14">
            <b>Hiện trạng hàng hóa: </b> <span>&nbsp;</span> Nguyên kiện
        </td>
    </tr>
    <tr>
        <td colspan="14">
            <b>Hình thức vận chuyển: </b> <span>&nbsp;</span> Tại kho
        </td>
    </tr>
    <tr>
        <td colspan="14"></td>
    </tr>
    <tr>
        <td colspan="14"></td>
    </tr>
    <tr>
        <td colspan="1"></td>
        <td colspan="2" style="text-align: center;font-weight: bold">Người lập phiếu</td>

        <td colspan="1"></td>
        <td colspan="2" style="text-align: center;font-weight: bold">Người nhận hàng</td>

        <td colspan="1"></td>
        <td colspan="2" style="text-align: center;font-weight: bold">Thủ kho</td>

        <td colspan="1"></td>
        <td colspan="2" style="text-align: center;font-weight: bold">Giám đốc</td>
    </tr>
</table>
</html>