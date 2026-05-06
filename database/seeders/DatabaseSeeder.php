<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\SalesDetail;
use App\Models\SalesTransaction;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            User::updateOrCreate(
                ['username' => 'admin'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                ]
            );

            $categories = collect([
                'Fire Safety',
                'CCTV',
                'IT Products',
            ])->map(fn($name) => Category::updateOrCreate(
                ['category_name' => $name],
                ['category_name' => $name]
            ));

            $suppliers = collect([
                [
                    'supplier_name' => 'Safety First Co.',
                    'first_name' => 'Juan',
                    'last_name' => 'Dela Cruz',
                    'contact_info' => '09171234567',
                    'address' => 'Makati City, Metro Manila',
                    'is_archived' => false,
                ],
                [
                    'supplier_name' => 'Tech Supplies Inc.',
                    'first_name' => 'Maria',
                    'last_name' => 'Santos',
                    'contact_info' => '09281234567',
                    'address' => 'Quezon City, Metro Manila',
                    'is_archived' => false,
                ],
                [
                    'supplier_name' => 'Network Solutions',
                    'first_name' => 'Pedro',
                    'last_name' => 'Reyes',
                    'contact_info' => '09391234567',
                    'address' => 'Pasig City, Metro Manila',
                    'is_archived' => false,
                ],
            ])->map(fn($supplier) => Supplier::updateOrCreate(
                ['supplier_name' => $supplier['supplier_name']],
                $supplier
            ));

            $employees = collect([
                ['first_name' => 'Ace', 'last_name' => 'Canindo', 'role' => 'Admin', 'contact_info' => '09170000001'],
                ['first_name' => 'Hannah', 'last_name' => 'Ayco', 'role' => 'Sales Staff', 'contact_info' => '09170000002'],
                ['first_name' => 'Mark', 'last_name' => 'Villanueva', 'role' => 'Inventory Staff', 'contact_info' => '09170000003'],
            ])->map(fn($employee) => Employee::updateOrCreate(
                ['first_name' => $employee['first_name'], 'last_name' => $employee['last_name']],
                $employee
            ));

            collect([
                ['first_name' => 'Carlo', 'last_name' => 'Mendoza', 'contact_info' => '09175678901', 'address' => 'Bacolod City'],
                ['first_name' => 'Liza', 'last_name' => 'Fernandez', 'contact_info' => '09262345678', 'address' => 'Iloilo City'],
                ['first_name' => 'Patrick', 'last_name' => 'Gomez', 'contact_info' => '09192347856', 'address' => 'Cagayan de Oro City'],
            ])->each(fn($customer) => Customer::updateOrCreate(
                ['first_name' => $customer['first_name'], 'last_name' => $customer['last_name']],
                $customer + ['is_archived' => false]
            ));

            collect(['Cash', 'GCash', 'Bank Transfer'])->each(fn($method) => PaymentMethod::updateOrCreate(
                ['payment_method_name' => $method],
                ['payment_method_name' => $method]
            ));

            $products = collect([
                [
                    'supplier_id' => $suppliers[0]->id,
                    'category_id' => $categories[0]->id,
                    'model_number' => 'FE-10K-ABC',
                    'product_name' => 'Fire Extinguisher 10kg ABC',
                    'description' => 'Multi-purpose dry chemical fire extinguisher for commercial use',
                    'unit_price' => 3200,
                    'minimum_stock' => 3,
                ],
                [
                    'supplier_id' => $suppliers[1]->id,
                    'category_id' => $categories[1]->id,
                    'model_number' => 'CCTV-BLT-2MP',
                    'product_name' => 'CCTV Bullet Camera 2MP',
                    'description' => 'Outdoor bullet camera with night vision and waterproof casing',
                    'unit_price' => 2750,
                    'minimum_stock' => 4,
                ],
                [
                    'supplier_id' => $suppliers[2]->id,
                    'category_id' => $categories[2]->id,
                    'model_number' => 'SW-8P-GIG',
                    'product_name' => 'Network Switch 8-Port Gigabit',
                    'description' => '8-port gigabit ethernet switch for office and business networks',
                    'unit_price' => 1850,
                    'minimum_stock' => 2,
                ],
            ])->map(function ($item) {
                $product = Product::updateOrCreate(
                    ['product_name' => $item['product_name']],
                    collect($item)->except('minimum_stock')->all() + ['is_archived' => false]
                );

                Inventory::updateOrCreate(
                    ['product_id' => $product->id],
                    ['current_stock' => 0, 'minimum_stock' => $item['minimum_stock']]
                );

                return $product;
            });

            $stockIns = [
                [
                    'supplier_id' => $suppliers[0]->id,
                    'employee_id' => $employees[0]->id,
                    'date_received' => now()->subDays(3)->toDateString(),
                    'delivery_receipt_no' => 'DR-2026-0001',
                    'remarks' => 'Initial stock delivery',
                    'items' => [
                        ['product_id' => $products[0]->id, 'quantity' => 5, 'cost' => 2800, 'condition' => 'good'],
                    ],
                ],
                [
                    'supplier_id' => $suppliers[1]->id,
                    'employee_id' => $employees[2]->id,
                    'date_received' => now()->subDays(2)->toDateString(),
                    'delivery_receipt_no' => 'DR-2026-0002',
                    'remarks' => 'Camera shipment',
                    'items' => [
                        ['product_id' => $products[1]->id, 'quantity' => 8, 'cost' => 2400, 'condition' => 'good'],
                    ],
                ],
                [
                    'supplier_id' => $suppliers[2]->id,
                    'employee_id' => $employees[2]->id,
                    'date_received' => now()->subDay()->toDateString(),
                    'delivery_receipt_no' => 'DR-2026-0003',
                    'remarks' => 'Networking equipment delivery',
                    'items' => [
                        ['product_id' => $products[2]->id, 'quantity' => 6, 'cost' => 1500, 'condition' => 'good'],
                    ],
                ],
            ];

            foreach ($stockIns as $transaction) {
                $stockIn = StockIn::updateOrCreate(
                    ['delivery_receipt_no' => $transaction['delivery_receipt_no']],
                    collect($transaction)->except('items')->all()
                );

                foreach ($transaction['items'] as $item) {
                    StockInDetail::updateOrCreate(
                        [
                            'stock_in_id' => $stockIn->id,
                            'product_id' => $item['product_id'],
                        ],
                        [
                            'quantity_received' => $item['quantity'],
                            'cost_per_unit' => $item['cost'],
                            'total_amount' => $item['quantity'] * $item['cost'],
                            'condition_status' => $item['condition'],
                        ]
                    );

                    if ($item['condition'] === 'good') {
                        $inventory = Inventory::where('product_id', $item['product_id'])->first();
                        $inventory->update(['current_stock' => $item['quantity']]);
                    }
                }
            }

            $salesTransactions = [
                [
                    'reference' => 'SALE-2026-0001',
                    'customer_id' => Customer::where('first_name', 'Carlo')->where('last_name', 'Mendoza')->value('id'),
                    'employee_id' => $employees[1]->id,
                    'sales_date' => now()->subDay()->toDateString(),
                    'items' => [
                        ['product_id' => $products[0]->id, 'quantity' => 1, 'unit_price' => 3200],
                        ['product_id' => $products[2]->id, 'quantity' => 1, 'unit_price' => 1850],
                    ],
                    'payment' => [
                        'payment_date' => now()->subDay()->toDateString(),
                        'amount_paid' => 5050,
                        'payment_method_id' => PaymentMethod::where('payment_method_name', 'Cash')->value('id'),
                        'status' => 'paid',
                    ],
                ],
                [
                    'reference' => 'SALE-2026-0002',
                    'customer_id' => Customer::where('first_name', 'Liza')->where('last_name', 'Fernandez')->value('id'),
                    'employee_id' => $employees[1]->id,
                    'sales_date' => now()->toDateString(),
                    'items' => [
                        ['product_id' => $products[1]->id, 'quantity' => 2, 'unit_price' => 2750],
                    ],
                    'payment' => [
                        'payment_date' => now()->toDateString(),
                        'amount_paid' => 3000,
                        'payment_method_id' => PaymentMethod::where('payment_method_name', 'GCash')->value('id'),
                        'status' => 'partial',
                    ],
                ],
            ];

            foreach ($salesTransactions as $transaction) {
                $totalAmount = collect($transaction['items'])->sum(fn($item) => $item['quantity'] * $item['unit_price']);

                $sale = SalesTransaction::updateOrCreate(
                    [
                        'customer_id' => $transaction['customer_id'],
                        'sales_date' => $transaction['sales_date'],
                    ],
                    [
                        'employee_id' => $transaction['employee_id'],
                        'total_amount' => $totalAmount,
                        'status' => 'pending',
                    ]
                );

                foreach ($transaction['items'] as $item) {
                    SalesDetail::updateOrCreate(
                        [
                            'sales_transaction_id' => $sale->id,
                            'product_id' => $item['product_id'],
                        ],
                        [
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'subtotal' => $item['quantity'] * $item['unit_price'],
                        ]
                    );

                    $inventory = Inventory::where('product_id', $item['product_id'])->first();
                    $startingStock = match ($item['product_id']) {
                        $products[0]->id => 5,
                        $products[1]->id => 8,
                        $products[2]->id => 6,
                        default => $inventory->current_stock,
                    };

                    $soldQuantity = SalesDetail::where('product_id', $item['product_id'])->sum('quantity');
                    $inventory->update(['current_stock' => max(0, $startingStock - $soldQuantity)]);
                }

                Payment::updateOrCreate(
                    ['sales_transaction_id' => $sale->id],
                    $transaction['payment']
                );

                $sale->load('payment');
                $sale->refreshStatusFromPayment();
            }
        });
    }
}
