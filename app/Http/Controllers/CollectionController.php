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
            $collectionValidator = Validator::make(
                json_decode($data, true),
                [
                    'name' => 'required|string|max:255',
                    'symbol' => 'required|string|max:255',
                    'card_id' => 'nullable|int|exists:cards,id'
                ],
                [
                    'name.required' => 'Nombre de Colección Obligatorio',
                    'symbol.required' => 'Imagen de Colección Obligatoria',
                    'card_id.exists' => 'La Carta Seleccionada No Existe'
                ]
            );

            $cardValidator = Validator::make(
                json_decode($data, true),
                [
                    'cardName' => 'required|string|max:255',
                    'cardDescription' => 'required|string|max:255',
                ],
                [
                    'cardName.required' => 'Nombre de Carta Obligatorio',
                    'cardDescription.required' => 'Descripción de Carta Obligatoria'
                ]
            );

            $data = json_decode($data);

            try {
                if (!isset($data->card_id)) {
                    if ($cardValidator->fails() || $collectionValidator->fails()) {
                        $response['status'] = 0;
                        $response['data']['errors'] = $collectionValidator->errors();
                        $response['data']['errors'] = $cardValidator->errors();
                        $response['msg'] = "Ha ocurrido un error.";

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
                        $card->name = $data->cardName;
                        $card->description = $data->cardDescription;
                        $card->save();

                        // Deck
                        $deck = new Deck();
                        $deck->card_id = $card->id;
                        $deck->collection_id = $collection->id;
                        $deck->save();
                    }
                } else {
                    if ($collectionValidator->fails()) {
                        $response['status'] = 0;
                        $response['data']['errors'] = $collectionValidator->errors();
                        $response['msg'] = "Ha ocurrido un error.";

                        return response()->json($response, 400);
                    } else {
                        // Create Collection
                        $dateNow = new DateTime('now');
                        $collection = new Collection();

                        $collection->name = $data->name;
                        $collection->symbol = $data->symbol;
                        $collection->edition_date = $dateNow;
                        $collection->save();

                        // Deck
                        $deck = new Deck();
                        $deck->card_id = $data->card_id;
                        $deck->collection_id = $collection->id;
                        $deck->save();
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
