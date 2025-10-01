<?php

namespace Bpocallaghan\Testimonials\Tests;

use Bpocallaghan\Testimonials\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_order_page()
    {
        Testimonial::create([
            'customer' => 'John Doe',
            'description' => 'Great product!',
            'list_order' => 1,
        ]);

        Testimonial::create([
            'customer' => 'Jane Doe',
            'description' => 'Amazing service!',
            'list_order' => 2,
        ]);

        $response = $this->get('/admin/testimonials/order');

        $response->assertStatus(200);
        $response->assertViewIs('testimonials::order');
        $response->assertViewHas('itemsHtml');
    }

    /** @test */
    public function it_generates_correct_html_for_testimonials()
    {
        $testimonial1 = Testimonial::create([
            'customer' => 'John Doe',
            'description' => 'Great product!',
            'list_order' => 1,
        ]);

        $testimonial2 = Testimonial::create([
            'customer' => 'Jane Doe',
            'description' => 'Amazing service!',
            'list_order' => 2,
        ]);

        $response = $this->get('/admin/testimonials/order');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Jane Doe');
        $response->assertSee('data-id="' . $testimonial1->id . '"');
        $response->assertSee('data-id="' . $testimonial2->id . '"');
    }

    /** @test */
    public function it_can_update_testimonial_order()
    {
        $testimonial1 = Testimonial::create([
            'customer' => 'John Doe',
            'description' => 'Great product!',
            'list_order' => 1,
        ]);

        $testimonial2 = Testimonial::create([
            'customer' => 'Jane Doe',
            'description' => 'Amazing service!',
            'list_order' => 2,
        ]);

        $newOrder = [
            ['id' => $testimonial2->id],
            ['id' => $testimonial1->id],
        ];

        $response = $this->post('/admin/testimonials/order', [
            'list' => json_encode($newOrder)
        ]);

        $response->assertStatus(200);
        $response->assertJson(['result' => 'success']);

        // Check that the order was updated
        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial1->id,
            'list_order' => 2,
        ]);

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial2->id,
            'list_order' => 1,
        ]);
    }

    /** @test */
    public function it_handles_empty_testimonials_list()
    {
        $response = $this->get('/admin/testimonials/order');

        $response->assertStatus(200);
        $response->assertViewHas('itemsHtml', '');
    }

    /** @test */
    public function it_orders_testimonials_by_list_order()
    {
        Testimonial::create([
            'customer' => 'Third',
            'description' => 'Third description',
            'list_order' => 3,
        ]);

        Testimonial::create([
            'customer' => 'First',
            'description' => 'First description',
            'list_order' => 1,
        ]);

        Testimonial::create([
            'customer' => 'Second',
            'description' => 'Second description',
            'list_order' => 2,
        ]);

        $response = $this->get('/admin/testimonials/order');

        $response->assertStatus(200);
        
        // Check that testimonials appear in correct order
        $content = $response->getContent();
        $firstPos = strpos($content, 'First');
        $secondPos = strpos($content, 'Second');
        $thirdPos = strpos($content, 'Third');

        $this->assertLessThan($secondPos, $firstPos);
        $this->assertLessThan($thirdPos, $secondPos);
    }

    /** @test */
    public function it_handles_invalid_json_in_update_order()
    {
        $response = $this->post('/admin/testimonials/order', [
            'list' => 'invalid json'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['result' => 'success']);
    }

    /** @test */
    public function it_handles_empty_list_in_update_order()
    {
        $response = $this->post('/admin/testimonials/order', [
            'list' => json_encode([])
        ]);

        $response->assertStatus(200);
        $response->assertJson(['result' => 'success']);
    }
}
