<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeckController extends Controller
{
    /**
     * Add Cards To Collections
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function addCardsToCollections(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();

        if (isset($data)) {
            $validator = Validator::make(
                json_decode($data, true),
                [
                    'card_id' => 'required|int|exists:cards,id',
                    'collection_id' => 'required|int|exists:collections,id'
                ]
            );

            $data = json_decode($data);

            try {
                if ($validator->fails()) {
                    $response['status'] = 0;
                    $response['msg'] = "Ha ocurrido un error: " . $validator->errors();

                    return response()->json($response, 400);
                } else {
                    $cardQuery = DB::table('decks')
                        ->where('card_id', $data->card_id)
                        ->where('collection_id', $data->collection_id)
                        ->value('id');
                        
                    if ($cardQuery) {
                        $response['status'] = 0;
                        $response['msg'] = "Carta ya Asociada";

                        return response()->json($response, 200);
                    } else {
                        // Create New Association
                        $deck = new Deck();
                        $deck->card_id = $data->card_id;
                        $deck->collection_id = $data->collection_id;
                        $deck->save();

                        $response['status'] = 1;
                        $response['msg'] = "Carta Asociada Correctamente";

                        return response()->json($response, 200);
                    }
                }
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 500);
            }
        }
    }
}
