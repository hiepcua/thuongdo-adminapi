<?php

use App\Constants\LocateConstant;
use App\Exceptions\CustomValidationException;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Services\ConfigService;
use App\Services\OrganizationService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

if (!function_exists('pluckByKeyValue')) {
    function pluckByKeyValue(array $array): array
    {
        $data = [];
        foreach ($array as $key => $item) {
            $data[] = ['value' => $key, 'name' => $item];
        }
        return $data;
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser(): ?Authenticatable
    {
        return Auth::user();
    }
}

if (!function_exists('getCurrentUserId')) {
    function getCurrentUserId(): string
    {
        return getCurrentUser()->id;
    }
}

if (!function_exists('getOrganization')) {
    function getOrganization(): string
    {
        return request()->input('organization_id') ?? (new OrganizationService())->getOrganizationDefault();
    }
}

if (!function_exists('getModelNameByClass')) {
    function getModelNameByClass($class): string
    {
        $strArr = explode('\\', $class);
        return array_pop($strArr);
    }
}

if (!function_exists('getExchangeRate')) {
    function getExchangeRate($orderId = null): float
    {
        return (float)(optional(Order::query()->find($orderId))->exchange_rate ?? (new ConfigService(
            ))->getExchangeRate());
    }
}

if (!function_exists('getExchangeRateByPackage')) {
    function getExchangeRateByPackage($id = null): float
    {
        return (float)(optional(OrderPackage::query()->find($id))->exchange_rate ?? (new ConfigService(
            ))->getExchangeRate());
    }
}

if (!function_exists('getProvinceX')) {
    function getProvinceX(?string $provinceId = null): string
    {
        return $provinceId ?? LocateConstant::PROVINCE_ID_X;
    }
}

if (!function_exists('roundXPrecision')) {
    function roundXPrecision(float $number, $precision = 2): float
    {
        return round($number, $precision);
    }
}

if (!function_exists('roundHalfUp')) {
    function roundHalfUp(float $number): float
    {
        return $number <= 0.5 && $number > 0 ? 0.5 : $number;
    }
}

if (!function_exists('enoughMoneyToPay')) {
    /**
     * @param  bool  $isCheck
     * @throws Throwable
     */
    function enoughMoneyToPay(bool $isCheck)
    {
        throw_if(
            $isCheck,
            CustomValidationException::class,
            [trans('transaction.enough_money_to_pay')]
        );
    }
}

if (!function_exists('getUuid')) {
    /**
     * @return string
     */
    function getUuid(): string
    {
        return Uuid::uuid6()->toString();
    }
}

if (!function_exists('convert_number_to_words')) {
    function convert_number_to_words($number)
    {
        $hyphen = ' ';
        $conjunction = ' ';
        $separator = ' ';
        $negative = 'âm ';
        $decimal = ' phẩy ';
        $one = 'mốt';
        $ten = 'lẻ';
        $dictionary = array(
            0 => 'Không',
            1 => 'Một',
            2 => 'Hai',
            3 => 'Ba',
            4 => 'Bốn',
            5 => 'Năm',
            6 => 'Sáu',
            7 => 'Bảy',
            8 => 'Tám',
            9 => 'Chín',
            10 => 'Mười',
            11 => 'Mười một',
            12 => 'Mười hai',
            13 => 'Mười ba',
            14 => 'Mười bốn',
            15 => 'Mười lăm',
            16 => 'Mười sáu',
            17 => 'Mười bảy',
            18 => 'Mười tám',
            19 => 'Mười chín',
            20 => 'Hai mươi',
            30 => 'Ba mươi',
            40 => 'Bốn mươi',
            50 => 'Năm mươi',
            60 => 'Sáu mươi',
            70 => 'Bảy mươi',
            80 => 'Tám mươi',
            90 => 'Chín mươi',
            100 => 'trăm',
            1000 => 'ngàn',
            1000000 => 'triệu',
            1000000000 => 'tỷ',
            1000000000000 => 'nghìn tỷ',
            1000000000000000 => 'ngàn triệu triệu',
            1000000000000000000 => 'tỷ tỷ'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if ($number < 0) {
            return $negative.convert_number_to_words(abs($number));
        }

        $fraction = null;

        if (strpos($number, '.') !== false) {
            [$number, $fraction] = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= strtolower($hyphen.($units == 1 ? $one : $dictionary[$units]));
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];
                if ($remainder) {
                    $string .= strtolower(
                        $conjunction.($remainder < 10 ? $ten.$hyphen : null).convert_number_to_words($remainder)
                    );
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number - ($numBaseUnits * $baseUnit);
                $string = convert_number_to_words($numBaseUnits).' '.$dictionary[$baseUnit];
                if ($remainder) {
                    $string .= strtolower($remainder < 100 ? $conjunction : $separator);
                    $string .= strtolower(convert_number_to_words($remainder));
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}





