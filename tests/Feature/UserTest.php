<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test if user no exists
     *
     * @return void
     */
    public function test_userNoExists() {
        $data = [
          "name" => "Manolo",
          "password" => "Manolo12345."
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(404)->assertJson([
            'status' => 0,
            'msg' => "Usuario No Registrado"
        ]);
    }

    public function test_dataNotMatch() {
        $data = [
            "name" => "test",
            "password" => "Prueba12345."
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(401)->assertJson([
            'status' => 0,
            'msg' => 'Nombre o Contraseña incorrectos'
        ]);
    }

    public function test_dataMatch() {
        $data = [
            "name" => "test",
            "password" => "Test12345."
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(200)->assertJson([
            'status' => 1,
            'msg' => 'Sesión Iniciada Correctamente'
        ]);
    }
}
