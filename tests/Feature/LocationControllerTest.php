<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Location;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function it_deletes_location_if_exists()
    {
        $location = Location::factory()->create();

        $response = $this->deleteJson(route('locations.delete', ['id' => $location->id]));

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Location deleted successfully']);
    }

    /** @test */
    public function it_returns_error_if_location_not_found()
    {
        $response = $this->deleteJson(route('locations.delete', ['id' => 999]));

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Location not found']);
    }

    public function test_can_get_all_locations()
    {
        $data = [
            'location' => 'location1',
            'state' => 1,
            'zone' => 'zone1',
        ];

        Location::create($data);

        $response = $this->get(route('locations.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'zone',
                    'state'
                ]
            ]
        ]);

        $response->assertJsonCount(1, 'data');
    }

    public function test_get_all_locations_returns_no_data()
    {
        $response = $this->get(route('locations.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data'
        ]);

        $response->assertJson([
            'success' => true,
            'data' => []
        ]);
    }

     /** @test */
    public function test_show_location_exists()
    {

        $data = [
            'location' => 'location1',
            'state' => 1,
            'zone' => 'zone1',
        ];

        $location = Location::create($data);

        $response = $this->getJson(route('locations.show', ['locationId' => $location->id]));

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $location->id,
                'state' => $location->state,
                'zone' => $location->zone,
                'location' => $location->location
            ]
        ]);
    }

     /** @test */
    public function test_show_location_not_found()
    {
        $nonExistingLocationId = mt_rand(1000000000, 9999999999);


        $response = $this->getJson(route('locations.show', ['locationId' => $nonExistingLocationId]));

        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'Location not found'
        ]);
    }
}
