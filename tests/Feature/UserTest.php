<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test if user no exists
     *
     * @return void
     */
    public function test_userNoExists()
    {
        $data = [
            "name" => "Manolo",
            "password" => "Manolo12345."
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(404)->assertJsonStructure([
            'status',
            'data',
            'msg'
        ]);
    }

    /**
     * Test if user data not match
     * 
     * @return void
     */
    public function test_dataNotMatch()
    {
        $data = [
            "name" => "test",
            "password" => "Prueba12345."
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(401)->assertJsonStructure([
            'status',
            'data',
            'msg'
        ]);
    }

    /**
     * Test if user data match
     * 
     * @return void
     */
    public function test_dataMatch()
    {
        $data = [
            "name" => "test",
            "password" => "Test12345."
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(200)->assertJsonStructure([
            'status',
            'data' => [
                'token',
            ],
            'msg'
        ]);
    }
}
