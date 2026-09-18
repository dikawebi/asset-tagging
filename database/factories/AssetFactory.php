<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        // Format ID Aset unik otomatis, contoh: AST-2026-847392
        $assetId = 'AST-' . date('Y') . '-' . $this->faker->unique()->numberBetween(100000, 999999);

        return [
            'asset_id' => $assetId,
            'name' => $this->faker->randomElement([
                'ThinkPad X1 Carbon', 'MacBook Pro M3', 'Dell Latitude 5420',
                'HP LaserJet Pro M404dn', 'Epson L3210 All-in-One',
                'LG UltraFine 24 Inch', 'ASUS ProArt Display 27 Inch',
                'Cisco Catalyst Switch 24-Port', 'MikroTik Cloud Core Router'
            ]),
            // Mengambil ID acak dari data master yang sudah ada nanti
            'category_id' => Category::inRandomOrder()->first()?->id ?? 1,
            'location_id' => Location::inRandomOrder()->first()?->id ?? 1,
            'department_id' => Department::inRandomOrder()->first()?->id ?? 1,

            'processor' => $this->faker->randomElement([
                'Intel Core i5-1335U', 'Intel Core i7-1355U', 'AMD Ryzen 5 7530U', 'Apple M3',
            ]),
            'memory' => $this->faker->randomElement(['8GB DDR4', '16GB DDR4', '16GB DDR5', '32GB DDR5']),
            'storage' => $this->faker->randomElement(['256GB SSD', '512GB SSD', '1TB SSD', '1TB HDD']),

            'pr_number' => 'PR-' . $this->faker->numberBetween(2026001, 2026999),
            'po_number' => 'PO-' . $this->faker->numberBetween(2026001, 2026999),
            'user_name' => $this->faker->name(),
            'status' => $this->faker->randomElement(['In use', 'Idle', 'Broke']),

            // Mengisi array kosong untuk gambar produk sementara
            'images' => null,
        ];
    }
}
