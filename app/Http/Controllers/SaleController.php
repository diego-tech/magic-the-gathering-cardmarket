<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function saleCard(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();

        if (isset($data)) {
            $validator = Validator::make(
                json_decode($data, true),
                [
                    'card_id' => 'required|int|exists:cards,id',
                    'number_of_cards' => 'required|int',
                    'price' => 'required|double'
                ]
            );

            $data = json_decode($data);

            try {
                if ($validator->fails()) {
                    $response['status'] = 0;
                    $response['msg'] = "Ha ocurrido un error: " . $validator->errors();

                    return response()->json($response, 400);
                } else {
                    // Create Sale
                    $sale = new Sale();
                    $sale->card_id = $data->card_id;
                    $sale->number_of_cards = $data->number_of_cards;
                    $sale->price = $data->price;
                    $sale->save();

                    // RETORNAR
                }
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 500);
            }
        }
    }

    public function searchEngine(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();
        $data = json_decode($data);

        if (isset($data)) {
            $query = DB::table('cards')
                ->select('name', 'id')
                ->where('name', 'like', '%' . $data->search . '%')
                ->get();

            $response['data'] = $query;
        }

        return response()->json($response);
    }

    public function getSales(Request $request) {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        
    }
}
