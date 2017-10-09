<?php

namespace Magnetar\Tariffs\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;

class MagnetarTariffsTest extends TestCase
{
    private $headers = [
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjQ5MGE3NDMyMjYzY2ViZGY2ZGVhNDFkYTI0NWIzMWI2Mzc5M2FlM2FjMjRkZWQ3N2IyNDU3ZGIxOTMwYzc0MjVlZGM4YzczNjAwNzVhOGRlIn0.eyJhdWQiOiIyIiwianRpIjoiNDkwYTc0MzIyNjNjZWJkZjZkZWE0MWRhMjQ1YjMxYjYzNzkzYWUzYWMyNGRlZDc3YjI0NTdkYjE5MzBjNzQyNWVkYzhjNzM2MDA3NWE4ZGUiLCJpYXQiOjE1MDcyNjc4MzgsIm5iZiI6MTUwNzI2NzgzOCwiZXhwIjoxNTM4ODAzODM4LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.lZ3wQc4810vqFEc7dMxRmvyk0jvB6a1ppFEAiQdbQe3jm1k3CNp_i7_m81MryvNMbuP689K7kvbfuqoZrzHF_fRo_G1GZmptCXoFMSiKWmCu-uN2JKH7D2w4BiABTHlxQaubYwZVmMbhOH9gvxaEdHvluIAd88u5vw3F8AIukdKVx93T9EYcilw6coQBWeqHolFBJrORwhQ89N2BRFKX5b6p2EOFnVwzUK45Q8YE-VpD3ADddPNlmQH2Vag3PEK0dr-gktSw197O-fQ_zpddnEWtm6DRuX2rhyAA7bsAP8k1W3ZKNdjwhfao2Z_E2NB0d-FXgXS3_iHYU2SdMfPb7tHRcaWd8WUMxjGDUtCfgj-4b5dp_2D1fFdqBJyLUiPpmob90QYBbYljLCYKtbX1HQg-aKekg7ZYHGKtg-Hy8bkxjqr2YPwErwX1B0VXM-KUCtIMNiZKj0t3viRzZ8Fq04_zjkoffnLtv-0lZndy28HrVjpQ-5B3IAPoZOtPodIa7gLJzjLP1obDzBclyEduQZo99iyMc2dkd1g-rbhb1NLYcB3N2RfiSxu0zdyIJ6mN1gl6OCkfFvMbv_-cXEOlimv7BAVncYlt3OxJopDDHEjosgvDsXqEDv5yVsy8rrL0m3mcLwLt5neu7dk2vB8phnxHxJCX-h-nk1LSwNy-FsM',
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
            'periods' => '{"P0Y": {"active": true}, "P0Y1M": {"active": true}, "P0Y2M": {"active": true}}',
            'data' => '{"1": {"active": true}, "2": {"count": 50, "active": true, "base_price": 100, "refresh_period": "P1M"}, "3": {"price": {"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}}, "active": true}}'
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
                'periods' => '{"P0Y": {"active": true}, "P0Y1M": {"active": true}, "P0Y2M": {"active": true}}',
                'data' => '{"1": {"active": true}, "2": {"count": 50, "active": true, "base_price": 100, "refresh_period": "P1M"}, "3": {"price": {"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}}, "active": true}}'
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

            $response = $this->withHeaders($this->headers)->json('PUT', 'api/v1/magnetar/tariffs/tariffs/' . $id . '/buy', [
                'period' => 'P0Y1M'
            ]);
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
            'settings' => '{"active": true}',
            'price' => '{"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}, "P0Y3M": {"price": 12}}',
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
                'settings' => '{"active": true}',
                'price' => '{"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}, "P0Y3M": {"price": 12}}',
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
