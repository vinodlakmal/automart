<?php

namespace Tests\Feature;

use App\Models\Ad;
use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdFlowTest extends TestCase
{
    use RefreshDatabase;

    private function scaffold(): array
    {
        $district = District::create(['name' => 'Colombo', 'slug' => 'colombo']);
        $city = City::create(['district_id' => $district->id, 'name' => 'Dehiwala', 'slug' => 'dehiwala']);
        $parent = Category::create(['name' => 'Vehicles', 'slug' => 'vehicles', 'type' => 'vehicles']);
        $child = Category::create(['name' => 'Cars', 'slug' => 'cars', 'parent_id' => $parent->id, 'type' => 'vehicles']);

        return compact('district', 'city', 'parent', 'child');
    }

    public function test_guest_is_redirected_from_create(): void
    {
        $this->get(route('ads.create'))->assertRedirect();
    }

    public function test_user_can_post_an_ad(): void
    {
        ['district' => $d, 'city' => $c, 'child' => $cat] = $this->scaffold();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('ads.store'), [
            'category_id'   => $cat->id,
            'district_id'   => $d->id,
            'city_id'       => $c->id,
            'title'         => 'Toyota Aqua 2015 for sale',
            'description'   => 'Excellent condition, full option, lady driven.',
            'condition'     => 'used',
            'price'         => 4500000,
            'is_negotiable' => '1',
            'contact_name'  => 'Vinod',
            'contact_phone' => '0712345678',
            'attributes'    => ['brand' => 'Toyota', 'year' => '2015'],
        ]);

        $ad = Ad::first();
        $this->assertNotNull($ad);
        $response->assertRedirect(route('ads.show', $ad));
        $this->assertDatabaseHas('ads', ['title' => 'Toyota Aqua 2015 for sale', 'user_id' => $user->id]);
        $this->assertDatabaseHas('ad_attributes', ['ad_id' => $ad->id, 'attribute_key' => 'brand', 'attribute_value' => 'Toyota']);
    }

    public function test_non_owner_cannot_delete(): void
    {
        ['district' => $d, 'city' => $c, 'child' => $cat] = $this->scaffold();
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $ad = Ad::create([
            'user_id' => $owner->id, 'category_id' => $cat->id, 'district_id' => $d->id,
            'city_id' => $c->id, 'title' => 'Sample ad here', 'description' => 'A valid long description text.',
            'contact_name' => 'X', 'contact_phone' => '0711111111', 'status' => 'active',
        ]);

        $this->actingAs($other)->delete(route('ads.destroy', $ad))->assertForbidden();
        $this->assertNotSoftDeleted($ad);
    }
}
