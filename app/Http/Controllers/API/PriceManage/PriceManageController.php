<?php

namespace App\Http\Controllers\API\PriceManage;

use App\Http\Controllers\Controller;
use App\Models\PriceManage;
use App\Models\SystemSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PriceManageController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $data = PriceManage::first();
        if ($data) {
            return $this->ok('Data Retrieve Successfully!', $data, 200);
        }
        return $this->error("Data not found", 404);
    }

}
