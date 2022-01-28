<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    /**
     * Cards Sales
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function saleCard(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();
        $user = $request->user();

        if (isset($data)) {
            $validator = Validator::make(
                json_decode($data, true),
                [
                    'card_id' => 'required|int|exists:cards,id',
                    'number_of_cards' => 'required|int',
                    'price' => 'required|numeric'
                ],
                [
                    'card_id.required' => 'Seleccione una Carta',
                    'card_id.exists' => 'La Carta Seleccionada No Existe',
                    'number_of_cards' => 'Introduzca un Número de Cartas',
                    'price' => 'Introduzca un Precio'
                ]
            );

            $data = json_decode($data);

            try {
                if ($validator->fails()) {
                    $response['status'] = 0;
                    $response['data'] = $validator->errors();
                    $response['msg'] = "Ha ocurrido un error.";

                    return response()->json($response, 400);
                } else {
                    // Create Sale
                    $sale = new Sale();
                    $sale->user_id = $user->id;
                    $sale->card_id = $data->card_id;
                    $sale->number_of_cards = $data->number_of_cards;
                    $sale->price = $data->price;
                    $sale->save();

                    $response['status'] = 1;
                    $response['msg'] = "Carta Puesta correctamente a la Venta";

                    return response()->json($response, 200);
                }
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 500);
            }
        }
    }

    /**
     * Card Finder
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function searchEngine(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();
        $data = json_decode($data);

        try {
            if (isset($data->search)) {
                // Valor Búsqueda
                $search = $data->search;

                $query = DB::table('cards')
                    ->select('name', 'id')
                    ->where('name', 'like', '%' . $search . '%')
                    ->get();

                $response['status'] = 1;
                $response['data'] = $query;
                $response['msg'] = "Búsqueda";

                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['msg'] = "Introduzca Datos";

                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 500);
        }
    }

    /**
     * Management of Purchases
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function purchaseManagement(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();
        $data = json_decode($data);

        try {
            if (isset($data->search)) {
                // Valor Búsqueda
                $search = $data->search;

                $query = DB::table('sales')
                    ->join('cards', 'card_id', 'cards.id')
                    ->join('users', 'user_id', 'users.id')
                    ->select(
                        'cards.name as CardName',
                        'number_of_cards',
                        'price',
                        'users.name as UserName'
                    )
                    ->where('cards.name', 'like', '%' . $search . '%')
                    ->orderBy('price', 'asc')
                    ->get();

                $response['status'] = 0;
                $response['data'] = $query;
                $response['msg'] = "Búsqueda";

                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['msg'] = "Introduzca Datos";

                return response()->json($response, 400);
            }
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 500);
        }
    }
}
