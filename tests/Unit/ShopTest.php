<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\UpdateShopInfoToCache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShopTest extends TestCase
{
    public $shop_id;
    public $id;

    public function setUp()
    {
        parent::setUp();
        $this->shop_id = 1717041709;
        $this->id = 186;
    }

    public function testApiOrderHome()
    {
        $response = $this->get('api/order');
        $response->assertStatus(200)
            ->assertExactJson(['errno' => 403, 'error' => 'unauthorized action.']);
    }

    public function testGet()
    {
        $response = $this->post('api/shop.get/' . $this->shop_id);
        $response->assertJsonFragment(['errno' => 0, 'error' => 'success']);
    }

    public function testNotifyResponse()
    {
        $response = $this->post('api/notify/' . $this->id);
        $response->assertJsonFragment(['errno' => 0, 'error' => 'success']);
    }

    public function testNotifyPushed()
    {
        $id = $this->id;
        Queue::fake();
        dispatch((new UpdateShopInfoToCache($this->id))->onQueue('update'));
        Queue::assertPushed(UpdateShopInfoToCache::class, function ($job) use ($id) {
            return $job->shop_id === $id;
        });
        Queue::assertPushedOn('update', UpdateShopInfoToCache::class);
    }
}
