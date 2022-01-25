<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Collection;
use App\Models\Deck;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    /**
     * Card Register
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function registerCard(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();
        if (isset($data)) {
            $validator = Validator::make(
                json_decode($data, true),
                [
                    'name' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'collection' => 'required|int|exists:decks,collection_id'
                ]
            );

            $data = json_decode($data);

            try {
                if ($validator->fails()) {
                    $response['status'] = 0;
                    $response['msg'] = "Ha ocurrido un error: " . $validator->errors();

                    return response()->json($response, 400);
                } else {
                    // Create Card
                    $card = new Card();
                    $card->name = $data->name;
                    $card->description = $data->description;
                    $card->save();

                    // Update Collection Edition Data
                    $collection = Collection::find($data->collection);
                    $collection->edition_date = new DateTime('now');
                    $collection->save();

                    // Create New Association
                    $deck = new Deck();
                    $deck->card_id = $card->id;
                    $deck->collection_id = $data->collection;
                    $deck->save();

                    $response['status'] = 1;
                    $response['msg'] = "Carta Guardada Correctamente";

                    return response()->json($response, 200);
                }
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 500);
            }
        }
    }
}
