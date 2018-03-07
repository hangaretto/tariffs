<?php

namespace Magnetar\Tariffs\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;
use Queue;

class MagnetarTariffsTest extends TestCase
{
    private $headers = [
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjE3YmJiNmJhZTg2NWQ1MjdhNTQ4NTA4YzY4ODIzMDUyNjkxNTllNjhkYTZmNjNkYjBkZjU0MTIzNjU5ZTAwMDhiMmRlZjM0ZThlOTBjMmJjIn0.eyJhdWQiOiIyIiwianRpIjoiMTdiYmI2YmFlODY1ZDUyN2E1NDg1MDhjNjg4MjMwNTI2OTE1OWU2OGRhNmY2M2RiMGRmNTQxMjM2NTllMDAwOGIyZGVmMzRlOGU5MGMyYmMiLCJpYXQiOjE1MjAyMjYyMzMsIm5iZiI6MTUyMDIyNjIzMywiZXhwIjoxNTUxNzYyMjMzLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.PJmaSZ3z6G5Jq6mwAgLQlLW4U1yy_6muqrI2ZL2WsaRtePRntIlRJQLN89sXvNHxjJ_hPwpMK-H4NHQnkqLkOis-uQopasmeETRz6WUIAUJswbNWRGOynJAF6UbLrDsVMXaUmXJIPx7E9Yywbso-KvCJZ-7FVpw6Kdke6BJQlWj9TJMg0SEjKTGAU5yAVR_IaH1ZoFa0mFKBlTg3EaGV0hEqSH22xMk_TOgoB-hhcKg4gjQIHZMzVeQyudSpgxxSvxF0qb9trn6Zo_8cyVMNvJhH0iRd3dONmcgrF8uNXnAzOsFPkk07_9VXzuSjQfPQjXU8s0i1I0_OFnsDA-BsbVHeR-igsq_q4dCTSuVAxGAq8xJgAtJvkcfzO16UWXZymEkf7m52mvzNZ3VfihcC8yuoT0DyOqrc2CIH2NzqoOwmJparfkPNHKB5T76CAqk2rVv4hgUIT7im9Yy3NnUxGmtJCMb5UdZke9zFCVnkAShMu3t5fys3vq7FcpHbcTNrCypwFrj_uddLMoofI-HhJdAZrvOwNv8PuJpyxZo7GXrTHyzz-2ozENNo3CWNIlX33dDpzuI-xmp7Pc7is5Rp3fJIfU_JBw0ghc1tJcM4mE2giFY-6KaA7cO_odA-1usm1L6NcRrrjX5yt4Y51MGtILsRfrJxI0mliLvhmi2ZJck',
    ];

    public function testObjectCRUD()
    {

        DB::beginTransaction();

        $post_data = [
            'name' => 'Test',
            'type_id' => 1,
            'periods' => '{"P0Y": {"active": true}, "P0Y1M": {"active": true}, "P0Y2M": {"active": true}}',
            'data' => '{"1": {"active": true}, "2": {"count": 50, "active": true, "base_price": 100, "refresh_period": "P1M"}, "3": {"price": {"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}}, "active": true}}'
        ];

        $response = $this->withHeaders($this->headers)->json('GET', 'api/v1/magnetar/tariffs/tariffs', []);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $response = $this->withHeaders($this->headers)->json('POST', 'api/v1/magnetar/tariffs/tariffs', $post_data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        if($response->status() == 200) {

            $tariff_json = json_decode($response->getContent(), true);

            $id = $tariff_json['data']['object']['id'];

            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/tariffs/' . $id, $post_data);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

            $response = $this->withHeaders($this->headers)->json('GET', 'api/v1/magnetar/tariffs/tariffs/' . $id, []);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

//            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/tariffs/' . $id . '/users/1', [ // todo
//                'period' => 'P0Y1M'
//            ]);
//            $response
//                ->assertStatus(200)
//                ->assertJson([
//                    'status' => 'success',
//                ]);

            $response = $this->withHeaders($this->headers)->json('DELETE', 'api/v1/magnetar/tariffs/tariffs/' . $id, []);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

        }

        DB::rollBack();

    }

    public function testGetObject_types()
    {
        DB::beginTransaction();

        $response = $this->withHeaders($this->headers)->json('GET', 'api/v1/magnetar/tariffs/object_types', []);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $response = $this->withHeaders($this->headers)->json('POST', 'api/v1/magnetar/tariffs/object_types', [
            'name' => 'Test'
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        if($response->status() == 200) {

            $tariff_json = json_decode($response->getContent(), true);

            $id = $tariff_json['data']['object_type']['id'];

            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/object_types/' . $id, [
                'name' => 'Test'
            ]);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

            $response = $this->withHeaders($this->headers)->json('GET', 'api/v1/magnetar/tariffs/object_types/' . $id, []);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

            $response = $this->withHeaders($this->headers)->json('DELETE', 'api/v1/magnetar/tariffs/object_types/' . $id, []);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

        }

        DB::rollBack();
    }

    public function testGetModules()
    {
        DB::beginTransaction();

        $post_data = [
            'name' => 'Test',
            'code' => 'test',
            'settings' => '{"active": true}',
            'price' => '{"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}, "P0Y3M": {"price": 12}}',
        ];

        $response = $this->withHeaders($this->headers)->json('GET', 'api/v1/magnetar/tariffs/modules', []);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $response = $this->withHeaders($this->headers)->json('POST', 'api/v1/magnetar/tariffs/modules', $post_data);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        if($response->status() == 200) {

            $tariff_json = json_decode($response->getContent(), true);

            $id = $tariff_json['data']['module']['id'];

            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/modules/' . $id, $post_data);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

            $response = $this->withHeaders($this->headers)->json('GET', 'api/v1/magnetar/tariffs/modules/' . $id, []);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/modules/' . $id . '/buy', [
                'period' => 'P0Y1M'
            ]);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

            $response = $this->withHeaders($this->headers)->json('DELETE', 'api/v1/magnetar/tariffs/modules/' . $id, []);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

        }

        DB::rollBack();
    }

}
