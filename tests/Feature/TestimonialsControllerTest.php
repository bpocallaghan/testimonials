<?php

namespace Bpocallaghan\Testimonials\Tests;

use Bpocallaghan\Testimonials\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

class TestimonialsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_testimonials_index()
    {
        Testimonial::create([
            'customer' => 'John Doe',
            'description' => 'Great product!',
        ]);

        $response = $this->get('/admin/testimonials');

        $response->assertStatus(200);
        $response->assertViewIs('testimonials::index');
        $response->assertViewHas('items');
    }

    /** @test */
    public function it_can_show_create_form()
    {
        $response = $this->get('/admin/testimonials/create');

        $response->assertStatus(200);
        $response->assertViewIs('testimonials::add_edit');
    }

    /** @test */
    public function it_can_store_a_new_testimonial()
    {
        $data = [
            'customer' => 'Jane Doe',
            'description' => 'Amazing service!',
            'link' => 'https://example.com',
        ];

        $response = $this->post('/admin/testimonials', $data);

        $this->assertDatabaseHas('testimonials', [
            'customer' => 'Jane Doe',
            'description' => 'Amazing service!',
            'link' => 'https://example.com',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        $response = $this->post('/admin/testimonials', []);

        $response->assertSessionHasErrors(['customer', 'description']);
    }

    /** @test */
    public function it_validates_customer_minimum_length()
    {
        $data = [
            'customer' => 'Jo', // Less than 3 characters
            'description' => 'Valid description',
        ];

        $response = $this->post('/admin/testimonials', $data);

        $response->assertSessionHasErrors(['customer']);
    }

    /** @test */
    public function it_can_show_a_testimonial()
    {
        $testimonial = Testimonial::create([
            'customer' => 'Bob Smith',
            'description' => 'Excellent quality!',
        ]);

        $response = $this->get("/admin/testimonials/{$testimonial->id}");

        $response->assertStatus(200);
        $response->assertViewIs('testimonials::show');
        $response->assertViewHas('item', $testimonial);
    }

    /** @test */
    public function it_can_show_edit_form()
    {
        $testimonial = Testimonial::create([
            'customer' => 'Alice Johnson',
            'description' => 'Outstanding support!',
        ]);

        $response = $this->get("/admin/testimonials/{$testimonial->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('testimonials::add_edit');
        $response->assertViewHas('item', $testimonial);
    }

    /** @test */
    public function it_can_update_a_testimonial()
    {
        $testimonial = Testimonial::create([
            'customer' => 'Original Customer',
            'description' => 'Original description',
        ]);

        $updateData = [
            'customer' => 'Updated Customer',
            'description' => 'Updated description',
            'link' => 'https://updated.com',
        ];

        $response = $this->put("/admin/testimonials/{$testimonial->id}", $updateData);

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'customer' => 'Updated Customer',
            'description' => 'Updated description',
            'link' => 'https://updated.com',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_updating()
    {
        $testimonial = Testimonial::create([
            'customer' => 'Test Customer',
            'description' => 'Test description',
        ]);

        $response = $this->put("/admin/testimonials/{$testimonial->id}", []);

        $response->assertSessionHasErrors(['customer', 'description']);
    }

    /** @test */
    public function it_can_delete_a_testimonial()
    {
        $testimonial = Testimonial::create([
            'customer' => 'Delete Me',
            'description' => 'This will be deleted',
        ]);

        $response = $this->delete("/admin/testimonials/{$testimonial->id}");

        $this->assertSoftDeleted('testimonials', ['id' => $testimonial->id]);
    }
}
