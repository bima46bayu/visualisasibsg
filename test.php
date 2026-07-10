<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/sales/dashboard?pivot_month=2', 'GET');
$controller = new App\Http\Controllers\Api\SalesApiController(new App\Services\DashboardService());
$response = $controller->dashboard($request);
echo json_encode($response->getData());
