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

        $response->assertStatus(401);
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

        $api_token = '10|lkCjeus4eUXaaTn648kbl0uAeP7iZlkmJ0axWsvE';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $api_token,
            'Accept' => 'application/json'
        ])->postJson('/api/registerCard', $data);

        $response->assertStatus(400)->assertJson([
            'status' => 0,
            'msg' => 'Ha ocurrido un error.'
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

        $api_token = '10|lkCjeus4eUXaaTn648kbl0uAeP7iZlkmJ0axWsvE';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $api_token,
            'Accept' => 'application/json'
        ])->postJson('/api/registerCard', $data);

        $response->assertStatus(400)->assertJson([
            'status' => 0,
            'msg' => 'Ha ocurrido un error.'
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

        $api_token = '10|lkCjeus4eUXaaTn648kbl0uAeP7iZlkmJ0axWsvE';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $api_token,
            'Accept' => 'application/json'
        ])->postJson('/api/registerCard', $data);

        $response->assertStatus(200)->assertJson([
            'status' => 1,
            'msg' => 'Carta Guardada Correctamente'
        ]);
    }
}
