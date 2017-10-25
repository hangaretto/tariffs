<?php

namespace Magnetar\Tariffs\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;
use Queue;

class MagnetarTariffsTest extends TestCase
{
    private $headers = [
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjVkOWU2NDY1Mjk3YmE4NjBhMjljMjhkNzliNzA2NmYzZDVlZDIxMmU3ODNkZTUzN2JjOWEwYmJhZWZlNGQ2YzgwMmYzNjczN2FiN2VkZWYyIn0.eyJhdWQiOiIyIiwianRpIjoiNWQ5ZTY0NjUyOTdiYTg2MGEyOWMyOGQ3OWI3MDY2ZjNkNWVkMjEyZTc4M2RlNTM3YmM5YTBiYmFlZmU0ZDZjODAyZjM2NzM3YWI3ZWRlZjIiLCJpYXQiOjE1MDc1NDE4MzEsIm5iZiI6MTUwNzU0MTgzMSwiZXhwIjoxNTM5MDc3ODMxLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.BrVyUGINH8SpbkTmTUcQXyuMSxMnsdlHYjvl-a1WBteVhWjIIU5lYHHvIZWGE84kh7jpZo3Ymk9lGUTQgUBxAXBluRLlao75qSbdcVo9FayEnKMwhrZtJPvivpd8gI_w5BHvuROmcfHyLGke44CXxcfBhtlI9KalH4keFMSYQQENc86cvaZBxbhRK-Wiq_L66HY8vcNLQ1zvF4_xPkwfLSKm6Lz7pBG6eLraXIj14qIAR0lsQw6bzP2QMWT8kmEbaklUMrmkQyUa775qqh5YodSKBduwQS-OZXjmwIsym2kcM8F169PwSQgR-2eQiZOiKfbYaF_ogxZjVDncn1r1NLMJsCdqj4kjm6VdhAdZdIE5Hbz7X0Rt0GMHTE2VQ8mCEehwGEQTfKF0cIiMCw8gdqzpPaD-ruTZLYbraP5UGn_pMyWuTZPt-kVnxqfGcY8sDL5TZhrCSK8cphJrp45XmY8-PML389OZmn0ODiS5rmWHthBfnzPsAna7nWZzRJn_oGFw5T6fqfNdxGUnS-qk9nX6V_0AQA_AuTN6kNXx1G4KfAZ0pZVznPtQc_RNNgwBZnsw1NRQmRKpkEGRK3LVpXdcFfZf3m3gc-k-HAex_mAEOrbSa1YuS4voKLdKzZjl1VoZKb4wM74gVjd-lHpA5rMRVqZVB0gLGPbsBoyaKVo',
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

        $post_data = [
            'name' => 'Test',
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
