<?php

namespace Tests\Feature;

use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationEndpointsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test konumların listelenmesi.
     *
     * @return void
     */
    public function testIndex()
    {
        // Test için istanbul ve ankara lokasyonu oluşturuyoruz
        Location::factory()->create(['name' => 'Istanbul', 'latitude' => 40.99801733, 'longitude' => 28.88620542, 'color' => '#ff0000']);
        Location::factory()->create(['name' => 'Ankara', 'latitude' => 39.93228144, 'longitude' => 32.85728580, 'color' => '#0000ff']);

        // Listelenen konumların doğruluğunu test ediyoruz
        $response = $this->getJson('/api/v1/locations');
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ])
            ->assertJsonFragment(['name' => 'Istanbul'])
            ->assertJsonFragment(['name' => 'Ankara']);
    }

    /**
     * Test konum ekleme işlemi.
     *
     * @return void
     */
    public function testStore()
    {
        // Eskişehir lokasyonunu test olarak tanımlıyoruz
        $data = [
            'name' => 'Eskişehir',
            'color' => '#FF5733',
            'latitude' => 39.76849852,
            'longitude' => 30.52136129,
        ];

        $response = $this->postJson('/api/v1/locations', $data);

        // Yanıtın başarılı olup olmadığını kontrol ediyoruz
        $response->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => 'Location created successfully',
            ]);
    }

    /**
     * Test mesafeye göre sıralama.
     *
     * @return void
     */
    public function testGetRouteList()
    {
        // Geçerli koordinatlar
        $data = [
            'latitude' => 39.75927873,
            'longitude' => 30.49737287,
        ];

        $response = $this->postJson('/api/v1/locations/route-list', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'name',
                        'latitude',
                        'longitude',
                        'distance',
                    ]
                ],
            ]);
    }

    /**
     * Test konum detaylarını görüntüleme.
     *
     * @return void
     */
    public function testShow()
    {
        // Bir konum oluşturuyoruz
        $location = Location::factory()->create(['name' => 'Sample Location']);

        $response = $this->getJson("/api/v1/locations/{$location->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Sample Location']);
    }

    /**
     * Test konum güncelleme işlemi.
     *
     * @return void
     */
    public function testUpdate()
    {
        // Bir konum oluşturuyoruz
        $location = Location::factory()->create(['name' => 'Old Location']);

        // Güncelleme verileri
        $data = [
            'name' => 'Updated Location',
            'color' => '#000000',
            'latitude' => 41.712776,
            'longitude' => -73.005974,
        ];

        $response = $this->putJson("/api/v1/locations/{$location->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Location']);
    }

    /**
     * Test konum silme işlemi.
     *
     * @return void
     */
    public function testDestroy()
    {
        // Bir konum oluşturuyoruz
        $location = Location::factory()->create(['name' => 'Location to Delete']);

        $response = $this->deleteJson("/api/v1/locations/{$location->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => 'Location deleted successfully',
            ]);
    }
}
