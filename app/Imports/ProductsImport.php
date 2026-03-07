<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array<string, mixed> $row
     */
    public function model(array $row): ?Product
    {
        $code = trim((string) ($row['codigo'] ?? $row['code'] ?? ''));
        $name = trim((string) ($row['nombre'] ?? $row['name'] ?? ''));

        if ($code === '' || $name === '') {
            return null;
        }

        $categoryName = trim((string) ($row['categoria'] ?? ''));
        $supplierName = trim((string) ($row['proveedor'] ?? ''));

        $categoryId = null;
        if ($categoryName !== '') {
            $category = Category::firstOrCreate(
                ['name' => Str::limit($categoryName, 120, '')],
                ['description' => 'Creada desde importación', 'active' => true]
            );
            $categoryId = $category->id;
        }

        $supplierId = null;
        if ($supplierName !== '') {
            $supplier = Supplier::firstOrCreate(
                ['name' => Str::limit($supplierName, 150, '')],
                ['active' => true]
            );
            $supplierId = $supplier->id;
        }

        return Product::updateOrCreate(
            ['code' => Str::limit($code, 80, '')],
            [
                'barcode' => $this->nullableString($row['codigo_barras'] ?? $row['barcode'] ?? null, 80),
                'name' => Str::limit($name, 180, ''),
                'description' => $this->nullableString($row['descripcion'] ?? null, 1000),
                'price' => $this->decimalOrZero($row['precio'] ?? 0),
                'stock' => $this->unsignedInt($row['cantidad'] ?? $row['stock'] ?? 0),
                'min_stock' => $this->unsignedInt($row['stock_minimo'] ?? 0),
                'photo_path' => null,
                'category_id' => $categoryId,
                'supplier_id' => $supplierId,
                'active' => true,
            ]
        );
    }

    /**
     * @param mixed $value
     */
    private function nullableString(mixed $value, int $maxLength = 255): ?string
    {
        $text = trim((string) $value);

        return $text === '' ? null : Str::limit($text, $maxLength, '');
    }

    /**
     * @param mixed $value
     */
    private function decimalOrZero(mixed $value): float
    {
        $clean = str_replace(',', '.', trim((string) $value));
        $number = is_numeric($clean) ? (float) $clean : 0;

        return $number < 0 ? 0 : $number;
    }

    /**
     * @param mixed $value
     */
    private function unsignedInt(mixed $value): int
    {
        $number = (int) floor((float) (is_numeric($value) ? $value : 0));

        if ($number < 0) {
            return 0;
        }

        return min($number, 4294967295);
    }
}