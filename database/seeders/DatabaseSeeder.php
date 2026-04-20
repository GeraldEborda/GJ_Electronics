<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\SalesTransaction;
use App\Models\SalesDetail;
use App\Models\Payment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Categories
        $fireSafety  = Category::create(['category_name' => 'Fire Safety']);
        $cctv        = Category::create(['category_name' => 'CCTV']);
        $itProducts  = Category::create(['category_name' => 'IT Products']);

        // Suppliers
        $safetyFirst = Supplier::create([
            'supplier_name'  => 'Safety First Co.',
            'contact_person' => 'Juan Dela Cruz',
            'contact_info'   => '09171234567',
            'address'        => 'Makati City, Metro Manila',
        ]);
        $techSupplies = Supplier::create([
            'supplier_name'  => 'Tech Supplies Inc.',
            'contact_person' => 'Maria Santos',
            'contact_info'   => '09281234567',
            'address'        => 'Quezon City, Metro Manila',
        ]);
        $networkSolutions = Supplier::create([
            'supplier_name'  => 'Network Solutions',
            'contact_person' => 'Pedro Reyes',
            'contact_info'   => '09391234567',
            'address'        => 'Pasig City, Metro Manila',
        ]);

        // Employees
        $aceCanindo   = Employee::create(['first_name' => 'Ace',    'last_name' => 'Canindo',  'role' => 'Sales Staff',  'contact_info' => '09111111111']);
        $hannahAyco   = Employee::create(['first_name' => 'Hannah', 'last_name' => 'Ayco',     'role' => 'Sales Staff',  'contact_info' => '09222222222']);

        // Customers
        $abcCorp   = Customer::create(['first_name' => 'ABC',  'last_name' => 'Corporation',      'contact_info' => '09301111111', 'address' => 'BGC, Taguig']);
        $xyzBldg   = Customer::create(['first_name' => 'XYZ',  'last_name' => 'Building Management','contact_info' => '09302222222', 'address' => 'Ortigas, Pasig']);
        $defSec    = Customer::create(['first_name' => 'DEF',  'last_name' => 'Security Services', 'contact_info' => '09303333333', 'address' => 'Cebu City']);

        // Products
        $fireExt = Product::create([
            'supplier_id'  => $safetyFirst->id,
            'category_id'  => $fireSafety->id,
            'model_number' => 'FE-5K-ABC',
            'product_name' => 'Fire Extinguisher 5kg ABC',
            'description'  => 'Multi-purpose dry chemical fire extinguisher',
            'unit_price'   => 1850,
        ]);
        $cctvCam = Product::create([
            'supplier_id'  => $techSupplies->id,
            'category_id'  => $cctv->id,
            'model_number' => 'CAM-HD1080',
            'product_name' => 'CCTV Camera HD 1080p',
            'description'  => 'High definition surveillance camera with night vision',
            'unit_price'   => 3500,
        ]);
        $smokeDetector = Product::create([
            'supplier_id'  => $safetyFirst->id,
            'category_id'  => $fireSafety->id,
            'model_number' => 'SD-100',
            'product_name' => 'Smoke Detector',
            'description'  => 'Photoelectric smoke detector with battery backup',
            'unit_price'   => 450,
        ]);
        $networkCable = Product::create([
            'supplier_id'  => $networkSolutions->id,
            'category_id'  => $itProducts->id,
            'model_number' => 'CAT6-305',
            'product_name' => 'Network Cable Cat6 305m',
            'description'  => 'High-speed Cat6 ethernet cable, 305m box',
            'unit_price'   => 4800,
        ]);
        $dvr = Product::create([
            'supplier_id'  => $techSupplies->id,
            'category_id'  => $cctv->id,
            'model_number' => 'DVR-16CH',
            'product_name' => 'DVR 16-Channel',
            'description'  => '16-channel digital video recorder for CCTV systems',
            'unit_price'   => 12500,
        ]);

        // Inventories
        Inventory::create(['product_id' => $fireExt->id,      'current_stock' => 22, 'minimum_stock' => 10]);
        Inventory::create(['product_id' => $cctvCam->id,      'current_stock' => 8,  'minimum_stock' => 10]);
        Inventory::create(['product_id' => $smokeDetector->id,'current_stock' => 35, 'minimum_stock' => 15]);
        Inventory::create(['product_id' => $networkCable->id, 'current_stock' => 12, 'minimum_stock' => 5]);
        Inventory::create(['product_id' => $dvr->id,          'current_stock' => 4,  'minimum_stock' => 5]);

        // Stock In - SI-001
        $si1 = StockIn::create([
            'supplier_id'         => $safetyFirst->id,
            'employee_id'         => $aceCanindo->id,
            'date_received'       => '2026-04-15',
            'delivery_receipt_no' => 'DR-2026-0041',
            'remarks'             => 'All items in good condition',
        ]);
        StockInDetail::create(['stock_in_id' => $si1->id, 'product_id' => $fireExt->id,      'quantity_received' => 10, 'cost_per_unit' => 1600, 'total_amount' => 16000, 'condition_status' => 'good']);
        StockInDetail::create(['stock_in_id' => $si1->id, 'product_id' => $smokeDetector->id,'quantity_received' => 20, 'cost_per_unit' => 350,  'total_amount' => 7000,  'condition_status' => 'good']);

        // Stock In - SI-002
        $si2 = StockIn::create([
            'supplier_id'         => $techSupplies->id,
            'employee_id'         => $hannahAyco->id,
            'date_received'       => '2026-04-13',
            'delivery_receipt_no' => 'DR-2026-0038',
            'remarks'             => 'CCTV batch delivery',
        ]);
        StockInDetail::create(['stock_in_id' => $si2->id, 'product_id' => $cctvCam->id,    'quantity_received' => 10, 'cost_per_unit' => 2800, 'total_amount' => 28000, 'condition_status' => 'good']);
        StockInDetail::create(['stock_in_id' => $si2->id, 'product_id' => $dvr->id,        'quantity_received' => 3,  'cost_per_unit' => 9800, 'total_amount' => 29400, 'condition_status' => 'good']);
        StockInDetail::create(['stock_in_id' => $si2->id, 'product_id' => $networkCable->id,'quantity_received' => 5, 'cost_per_unit' => 4000, 'total_amount' => 20000, 'condition_status' => 'good']);

        // Sales
        // SALE-001
        $sale1 = SalesTransaction::create([
            'customer_id' => $abcCorp->id,
            'employee_id' => $hannahAyco->id,
            'sales_date'  => '2026-04-16 10:00:00',
            'total_amount'=> 3700,
            'status'      => 'completed',
        ]);
        SalesDetail::create(['sales_transaction_id' => $sale1->id, 'product_id' => $fireExt->id, 'quantity' => 2, 'unit_price' => 1850, 'subtotal' => 3700]);
        Payment::create(['sales_transaction_id' => $sale1->id, 'amount_paid' => 3700, 'payment_method' => 'cash', 'status' => 'paid']);

        // SALE-002
        $sale2 = SalesTransaction::create([
            'customer_id' => $xyzBldg->id,
            'employee_id' => $aceCanindo->id,
            'sales_date'  => '2026-04-15 14:30:00',
            'total_amount'=> 22500,
            'status'      => 'completed',
        ]);
        SalesDetail::create(['sales_transaction_id' => $sale2->id, 'product_id' => $cctvCam->id, 'quantity' => 5, 'unit_price' => 3500, 'subtotal' => 17500]);
        SalesDetail::create(['sales_transaction_id' => $sale2->id, 'product_id' => $smokeDetector->id, 'quantity' => 11, 'unit_price' => 450, 'subtotal' => 4950]);
        Payment::create(['sales_transaction_id' => $sale2->id, 'amount_paid' => 22500, 'payment_method' => 'bank_transfer', 'status' => 'paid']);

        // SALE-003
        $sale3 = SalesTransaction::create([
            'customer_id' => $defSec->id,
            'employee_id' => $hannahAyco->id,
            'sales_date'  => '2026-04-14 09:00:00',
            'total_amount'=> 19050,
            'status'      => 'pending',
        ]);
        SalesDetail::create(['sales_transaction_id' => $sale3->id, 'product_id' => $dvr->id,     'quantity' => 1,  'unit_price' => 12500, 'subtotal' => 12500]);
        SalesDetail::create(['sales_transaction_id' => $sale3->id, 'product_id' => $fireExt->id, 'quantity' => 3,  'unit_price' => 1850,  'subtotal' => 5550]);
        SalesDetail::create(['sales_transaction_id' => $sale3->id, 'product_id' => $smokeDetector->id, 'quantity' => 2, 'unit_price' => 450, 'subtotal' => 900]);
        Payment::create(['sales_transaction_id' => $sale3->id, 'amount_paid' => 10000, 'payment_method' => 'cash', 'status' => 'partial']);
    }
}