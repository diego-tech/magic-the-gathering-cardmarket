<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Collection;
use App\Models\Deck;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    /**
     * Collection Register
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response, http_status_code)
     */
    public function registerCollection(Request $request)
    {
        $response = ["status" => 0, "data" => [], "msg" => ""];

        $data = $request->getContent();

        if (isset($data)) {
            $validator = Validator::make(
                json_decode($data, true),
                [
                    'name' => 'required|string|max:255',
                    'symbol' => 'required|string|max:255',
                ]
            );

            $data = json_decode($data);

            try {
                if ($validator->fails()) {
                    $response['status'] = 0;
                    $response['msg'] = "Ha ocurrido un error: " . $validator->errors();

                    return response()->json($response, 400);
                } else {
                    // Create Collection
                    $dateNow = new DateTime('now');
                    $collection = new Collection();

                    $collection->name = $data->name;
                    $collection->symbol = $data->symbol;
                    $collection->edition_date = $dateNow;
                    $collection->save();

                    // Generate Default Collection Card
                    $card = new Card();
                    $card->name = $data->name;
                    $card->description = "";
                    $card->save();

                    // Deck
                    $deck = new Deck();
                    $deck->card_id = $card->id;
                    $deck->collection_id = $collection->id;
                    $deck->save();
                }
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 500);
            }
        }
    }
}
