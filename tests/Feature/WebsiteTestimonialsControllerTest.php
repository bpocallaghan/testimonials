<?php

namespace Bpocallaghan\Testimonials\Tests;

use Bpocallaghan\Testimonials\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebsiteTestimonialsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_display_testimonials_on_website()
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

        $response = $this->get('/testimonials');

        $response->assertStatus(200);
        $response->assertViewIs('testimonials::testimonials');
        $response->assertViewHas('items');
        $response->assertSee('John Doe');
        $response->assertSee('Jane Doe');
    }

    /** @test */
    public function it_displays_testimonials_in_correct_order()
    {
        Testimonial::create([
            'customer' => 'Third Customer',
            'description' => 'Third description',
            'list_order' => 3,
        ]);

        Testimonial::create([
            'customer' => 'First Customer',
            'description' => 'First description',
            'list_order' => 1,
        ]);

        Testimonial::create([
            'customer' => 'Second Customer',
            'description' => 'Second description',
            'list_order' => 2,
        ]);

        $response = $this->get('/testimonials');

        $response->assertStatus(200);
        
        // Check that testimonials appear in correct order
        $content = $response->getContent();
        $firstPos = strpos($content, 'First Customer');
        $secondPos = strpos($content, 'Second Customer');
        $thirdPos = strpos($content, 'Third Customer');

        $this->assertLessThan($secondPos, $firstPos);
        $this->assertLessThan($thirdPos, $secondPos);
    }

    /** @test */
    public function it_handles_empty_testimonials_list()
    {
        $response = $this->get('/testimonials');

        $response->assertStatus(200);
        $response->assertViewIs('testimonials::testimonials');
        $response->assertViewHas('items');
    }

    /** @test */
    public function it_only_shows_non_deleted_testimonials()
    {
        $activeTestimonial = Testimonial::create([
            'customer' => 'Active Customer',
            'description' => 'Active description',
            'list_order' => 1,
        ]);

        $deletedTestimonial = Testimonial::create([
            'customer' => 'Deleted Customer',
            'description' => 'Deleted description',
            'list_order' => 2,
        ]);

        $deletedTestimonial->delete();

        $response = $this->get('/testimonials');

        $response->assertStatus(200);
        $response->assertSee('Active Customer');
        $response->assertDontSee('Deleted Customer');
    }

    /** @test */
    public function it_displays_testimonial_descriptions()
    {
        Testimonial::create([
            'customer' => 'John Doe',
            'description' => '<p>This is a <strong>great</strong> product!</p>',
            'list_order' => 1,
        ]);

        $response = $this->get('/testimonials');

        $response->assertStatus(200);
        $response->assertSee('This is a great product!');
    }

    /** @test */
    public function it_displays_testimonial_links()
    {
        Testimonial::create([
            'customer' => 'John Doe',
            'description' => 'Great product!',
            'link' => 'https://example.com',
            'list_order' => 1,
        ]);

        $response = $this->get('/testimonials');

        $response->assertStatus(200);
        $response->assertSee('https://example.com');
    }
}
