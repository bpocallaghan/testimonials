<?php

namespace Bpocallaghan\Testimonials\Tests;

use Bpocallaghan\Testimonials\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestimonialModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_testimonial()
    {
        $testimonial = Testimonial::create([
            'customer' => 'John Doe',
            'description' => 'This is a great product!',
            'link' => 'https://example.com',
        ]);

        $this->assertInstanceOf(Testimonial::class, $testimonial);
        $this->assertEquals('John Doe', $testimonial->customer);
        $this->assertEquals('This is a great product!', $testimonial->description);
        $this->assertEquals('https://example.com', $testimonial->link);
    }

    /** @test */
    public function it_has_validation_rules()
    {
        $rules = Testimonial::$rules;

        $this->assertArrayHasKey('customer', $rules);
        $this->assertArrayHasKey('description', $rules);
        $this->assertStringContainsString('required', $rules['customer']);
        $this->assertStringContainsString('required', $rules['description']);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $testimonial = Testimonial::create([
            'customer' => 'Jane Doe',
            'description' => 'Amazing service!',
        ]);

        $testimonial->delete();

        $this->assertSoftDeleted('testimonials', ['id' => $testimonial->id]);
    }

    /** @test */
    public function it_can_restore_soft_deleted_testimonial()
    {
        $testimonial = Testimonial::create([
            'customer' => 'Bob Smith',
            'description' => 'Excellent quality!',
        ]);

        $testimonial->delete();
        $testimonial->restore();

        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'customer' => 'Bob Smith',
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_set_list_order()
    {
        $testimonial = Testimonial::create([
            'customer' => 'Alice Johnson',
            'description' => 'Outstanding support!',
            'list_order' => 5,
        ]);

        $this->assertEquals(5, $testimonial->list_order);
    }

    /** @test */
    public function it_guards_id_field()
    {
        $testimonial = Testimonial::create([
            'id' => 999,
            'customer' => 'Test Customer',
            'description' => 'Test description',
        ]);

        $this->assertNotEquals(999, $testimonial->id);
    }

    /** @test */
    public function it_uses_correct_table_name()
    {
        $testimonial = new Testimonial();
        $this->assertEquals('testimonials', $testimonial->getTable());
    }
}
