<?php

namespace Database\Seeders;

use App\Helpers\DatabaseHelper;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::query()->truncate();
        Category::query()->insert(
            DatabaseHelper::getData(
                [
                    'Quần áo',
                    'Giầy dép',
                    'Đồ điện tử / Điện máy',
                    'Nội thất',
                    'Linh kiện điện tử / điện máy',
                    'Phụ kiện thời trang',
                    'Thời trang',
                    'Phụ kiện Thời trang',
                    'Mỹ phẩm',
                    'Đồ chơi',
                    'Vải vóc',
                    'Tóc giả',
                    'Văn phòng phẩm'
                ],
                true
            )
        );
    }
}
