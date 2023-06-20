<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ValidationHelper
{
    public static function validation(array $params, array $rule, ?array $attributes = [], ?array $message = []): array
    {
        $errors = Validator::make(
            $params,
            $rule,
            $message,
            $attributes
        )->errors();
        $messages = $errors->messages();
        if (!$messages) {
            return [];
        }
        $data = [];
        foreach ($messages as $key => $error) {
            $data[$key] = array_shift($error);
        }
        unset($messages, $errors);
        return $data;
    }

    public static function prepareUpdateAction(array &$data, string $id)
    {
        foreach ($data as $key => $item) {
            if(is_array($item)) continue;
            $data[$key] = self::addIdToUpdateAction(self::replaceRequired($item), $id);
        }
    }

    private static function replaceRequired(string $item): string
    {
        if (Str::startsWith($item, $required = 'required')) {
            $item = str_replace($required, '', $item);
        }
        return $item;
    }

    private static function addIdToUpdateAction(string $item, string $id): string
    {
        $strUnique = 'unique';
        $process = array_filter(explode('|', $item));
        if (strpos($item, $strUnique) !== false) {
            $index = 0;
            foreach ($process as $key => $value) {
                if (strpos($value, $strUnique) === false) {
                    continue;
                }
                $index = $key;
                break;
            }
            $process[$index] .= ',id,'.$id;
            $item = implode('|', $process);
        }
        return $item;
    }
}