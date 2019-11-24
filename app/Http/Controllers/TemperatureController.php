<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Temperature;
use Carbon\Carbon;

class TemperatureController extends Controller
{
  private $model;

  public function __construct(Temperature $model)
  {
    $this->model = $model;
  }

  public function get(Request $request)
  {
    $range = $request->only(['from', 'to']);
    $query = Temperature::query();
    if (!empty($range['from'])) {
      $query->where('created_at', '>', Carbon::parse($range['from']));
    }

    if (!empty($range['to'])) {
      $query->where('created_at', '<', Carbon::parse($range['to']));
    }
    return response()->json($query->get());
  }
}
