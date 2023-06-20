<?php


namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Str;

class FilterScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $startWith = 'scope';
        // Kiểm trang filter cho model nào.
        $prefix = explode('\\', get_class($model));
        $prefix = strtolower(array_pop($prefix));
        $prefix = $model->getPrefixRoute() ?? $prefix;
        if (strpos(request()->url(), 'api/'.$prefix) === false) {
            return;
        }
        // Lấy scope trong model
        $methods = array_filter(get_class_methods($model), fn($method) => Str::startsWith($method, $startWith));
        foreach ($methods as $method) {
            $scope = lcfirst(Str::replace($startWith, '', $method));
            $isValid = Str::startsWith($method, $startWith);
            if (($isValid && (!isset($_GET[Str::snake($scope)]) || (isset(
                                $_GET[Str::snake(
                                    $scope
                                )]
                            ) && empty($_GET[Str::snake($scope)]) && $_GET[Str::snake(
                                $scope
                            )] !== '0'))) || !$isValid || $method === 'scopeSearch') {
                continue;
            }
            $builder->{$scope}();
        }
    }
}