<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CardTest extends TestCase
{
    /**
     * Test if user is not authenticated
     *
     * @return void
     */
    public function test_noAuth()
    {
        $data = [
            "name" => "Carta Test",
            "description" => "Esto es una carta de test",
            "collection" => 1
        ];

        $api_token = '';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $api_token,
            'Accept' => 'application/json'
        ])->postJson('/api/registerCard', $data);

        $response->assertStatus(401)->assertJsonStructure([
            'status',
            'msg'
        ]);
    }

    /**
     * Test if no data is entered
     *
     * @return void
     */
    public function test_noData()
    {
        $data = [
            "name" => "",
            "description" => "",
            "collection" => null
        ];

        $api_token = '27|B83ErcVc0zbCisVgsM8WYGN8mo8xLqcDEX0UO2xw';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $api_token,
            'Accept' => 'application/json'
        ])->postJson('/api/registerCard', $data);

        $response->assertStatus(400)->assertJsonStructure([
            'status',
            'data' => [
                'errors' => [
                    'name',
                    'description',
                    'collection'
                ]
            ],
            'msg'
        ]);
    }

    /**
     * Test if collection id is incorrect
     * 
     * @return void
     */
    public function test_badCollectionId()
    {
        $data = [
            "name" => "Carta Test",
            "description" => "Esto es una carta de test",
            "collection" => 4
        ];

        $api_token = '27|B83ErcVc0zbCisVgsM8WYGN8mo8xLqcDEX0UO2xw';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $api_token,
            'Accept' => 'application/json'
        ])->postJson('/api/registerCard', $data);

        $response->assertStatus(400)->assertJsonStructure([
            'status',
            'data' => [
                'errors' => [
                    'collection'
                ]
            ],
            'msg'
        ]);
    }

    /**
     * Test if all card data is correct
     * 
     * @return void
     */
    public function test_allDataOk()
    {
        $data = [
            "name" => "Carta Test",
            "description" => "Esto es una carta de test",
            "collection" => 1
        ];

        $api_token = '27|B83ErcVc0zbCisVgsM8WYGN8mo8xLqcDEX0UO2xw';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $api_token,
            'Accept' => 'application/json'
        ])->postJson('/api/registerCard', $data);

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'data',
            'msg'
        ]);
    }
}
