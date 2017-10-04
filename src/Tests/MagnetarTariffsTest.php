<?php

namespace Magnetar\Tariffs\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;

class MagnetarTariffsTest extends TestCase
{
    private $headers = [
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjdhNTE0ZTNmNzE4NDFiZDcwNmRjN2RlNzgwYWI5MjBkMWI5YjNkODk0MTc5YWIyNDE5ZjRmZjEzZDQxMDUxNWE1NzhlZGEyOWUzZjgyNzEyIn0.eyJhdWQiOiIyIiwianRpIjoiN2E1MTRlM2Y3MTg0MWJkNzA2ZGM3ZGU3ODBhYjkyMGQxYjliM2Q4OTQxNzlhYjI0MTlmNGZmMTNkNDEwNTE1YTU3OGVkYTI5ZTNmODI3MTIiLCJpYXQiOjE1MDY2NzA3MTksIm5iZiI6MTUwNjY3MDcxOSwiZXhwIjoxNTM4MjA2NzE4LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.q7s2jP105U502-hV1gVnYT6tB7PCbHjb-s1rLbL98Rqm4Lf1VhDbKJRZgtgpLwuzCyXuyjVeXfuCJlzYjtlT3AL9Rq54GuoVjO0hena3OL-b39Lyi4_VdTrgaa0W9YAZaoNV5t75Q42whVQz2KFjKvg4hf9_VItzTnsKH8I8yWHSClxzje1ZXRjuFPBYTCGXxhiCmz_oD_4rS8pz8TMQW2L6319CIAohLvvNZe8XREViCVgB7secbNikEAMWQOkWZU1Hthi_afHwHX15zcpJcdKVumLgnqpmxBUN1uk7m3AnfQHfcNkZipcZIxroxpEzCDTMd1tK-cY1zE1dNEfRVmm-ugu9UP9tx0CxYDj-KvzON2Ac5J2IC4DPxC50Byd3MmpG_ROhmF7YOMrW09IJ5Zenr1QZ_SBO3oYT0dACCcK6bi8c9IknvjBrdNj5ig20-y2IO9nkN7BJHpvm6cfswYVT3jz0r-LXlVs2vJ8xTcUJOoOHGYWRlIQHDbBEgms6EWJByQeOGPdvDPLfQ58WyMLK7y7AxnbwKx_4fAl0Cn2ft1RdTdq0S6twMYH71b1U3Gh7JZ_bffO4rBmrqg2hqXB29F19BtOQgbq8cWu8lwterhcSZK1P22kPVarSYLo2oiIwKb7PC8rOMSxTcS_4UZG0S586AZSTLxm2-pSO3j4',
    ];

    public function testObjectCRUD()
    {

        DB::beginTransaction();

        $response = $this->withHeaders($this->headers)->json('GET', 'api/v1/magnetar/tariffs/tariffs', []);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $response = $this->withHeaders($this->headers)->json('POST', 'api/v1/magnetar/tariffs/tariffs', [
            'name' => 'Test',
            'type_id' => 1,
//            'currency_id' => 1,
            'price' => 100,
            'data' => '{"1":{"active":"true"},"2":{"count":50,"active":"true"},"3":{"period":10,"period_type":"day","active":"true"}}'
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        if($response->status() == 200) {

            $tariff_json = json_decode($response->getContent(), true);

            $id = $tariff_json['data']['object']['id'];

            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/tariffs/' . $id, [
                'name' => 'Test',
                'type_id' => 1,
//                'currency_id' => 1,
                'price' => 100,
                'data' => '{"1":{"active":"true"},"2":{"count":50,"active":"true"},"3":{"period":10,"period_type":"day","active":"true"}}'
            ]);
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

            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/tariffs/' . $id . '/buy', []);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                ]);

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

        $response = $this->withHeaders($this->headers)->json('GET', 'api/v1/magnetar/tariffs/modules', []);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $response = $this->withHeaders($this->headers)->json('POST', 'api/v1/magnetar/tariffs/modules', [
            'name' => 'Test',
            'settings' => '{"count": 1, "active": 1}',
            'price' => 100
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        if($response->status() == 200) {

            $tariff_json = json_decode($response->getContent(), true);

            $id = $tariff_json['data']['module']['id'];

            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/modules/' . $id, [
                'name' => 'Test',
                'settings' => '{"count": 1, "active": 1}',
                'price' => 100,
                'grade' => 1,
                'group' => 1
            ]);
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
