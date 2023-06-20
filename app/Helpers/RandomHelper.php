<?php


namespace App\Helpers;


use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\Transaction;
use App\Scopes\CustomerCurrentScope;
use App\Services\CustomerService;
use App\Services\UserService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RandomHelper
{
    /**
     * @param  int  $length
     * @return string
     */
    public static function numberString(int $length): string
    {
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= mt_rand(0, 9);
        }
        return $number;
    }

    /**
     * @return string
     */
    public static function phoneNumber(): string
    {
        $prefix = Arr::random(['090', '091', '092']);
        return $prefix.RandomHelper::numberString(7);
    }

    /**
     * @return string
     */
    public static function customerCode(): string
    {
        return 'KH_'.date('ym').sprintf(
                "%'.05d",
                (new CustomerService())->getCustomerNumberByYearAndMonth() + 1
            );
    }

    /**
     * @return string
     */
    public static function userCode(): string
    {
        return 'NV_'.date('ym').sprintf(
                "%'.05d",
                (new UserService())->getUserNumberByYearAndMonth()
            );
    }

    /**
     * @return string
     */
    public static function roleName(string $name): string
    {
        return Str::slug($name).'-'.time();
    }    

    /**
     * @param  int|null  $optional
     * @return string
     */
    public static function orderCode(?int $optional = 0): string
    {
        /** @var Customer $customer */
        $customer = Auth::user() ?? (new CustomerService())->getCustomerTest();
        return $customer->code.sprintf(
                "%'.04d",
                Order::withTrashed()->where('customer_id', $customer->id)->count() + $optional
            );
    }

    /**
     * @return string
     */
    public static function billCode(): string
    {
        return 'SPXVN-'. self::getNumberByLength(12);
    }

    /**
     * @param  int  $length
     * @return string
     */
    private static function getNumberByLength(int $length): string
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    /**
     * @param  int|null  $optional
     * @return string
     */
    public static function getTransactionCode(?int $optional = 1): string
    {
        $count = Transaction::query()->count();
        return "GD_".date('ymd').sprintf(
                "%'.04d",
                $count + $optional
            );
    }

    /**
     * @param  int|null  $optional
     * @return string
     */
    public static function getPackageCode(?int $optional = 1): string
    {
        $count = OrderPackage::query()->count();
        return "KI_".date('ymd').sprintf(
                "%'.04d",
                $count + $optional
            );
    }

    /**
     * @param  int|null  $optional
     * @return string
     */
    public static function getDeliveryCode(?int $optional = 1): string
    {
        $count = Delivery::query()->count();
        return sprintf(
            "GH_%'.04d",
            $count + $optional
        );
    }
}