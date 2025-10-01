<?php

namespace Bpocallaghan\Testimonials;

use Bpocallaghan\Testimonials\Models\Testimonial;
use Illuminate\Support\Facades\View;

class TestimonialsManager
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Render testimonials with optional parameters
     *
     * @param string $template
     * @param int $limit
     * @param string $orderBy
     * @return string
     */
    public function render($template = 'default', $limit = null, $orderBy = 'list_order')
    {
        $testimonials = Testimonial::published()->orderBy($orderBy);
        
        if ($limit) {
            $testimonials = $testimonials->take($limit);
        }
        
        $testimonials = $testimonials->get();

        return View::make("testimonials::components.{$template}", compact('testimonials'))->render();
    }

    /**
     * Render a single testimonial
     *
     * @param int $id
     * @param string $template
     * @return string
     */
    public function renderSingle($id, $template = 'single')
    {
        $testimonial = Testimonial::published()->findOrFail($id);

        return View::make("testimonials::components.{$template}", compact('testimonial'))->render();
    }

    /**
     * Get testimonials count
     *
     * @return int
     */
    public function count()
    {
        return Testimonial::published()->count();
    }

    /**
     * Get random testimonials
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function random($limit = 3)
    {
        return Testimonial::published()->inRandomOrder()->take($limit)->get();
    }

    /**
     * Get latest testimonials
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function latest($limit = 5)
    {
        return Testimonial::published()->latest()->take($limit)->get();
    }

    /**
     * Get testimonials by customer name
     *
     * @param string $customer
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function byCustomer($customer)
    {
        return Testimonial::published()->where('customer', 'like', "%{$customer}%")->get();
    }

    /**
     * Get testimonials statistics
     *
     * @return array
     */
    public function stats()
    {
        return [
            'total' => Testimonial::published()->count(),
            'this_month' => Testimonial::published()->whereMonth('created_at', now()->month)->count(),
            'this_year' => Testimonial::published()->whereYear('created_at', now()->year)->count(),
            'average_length' => Testimonial::published()->avg('description'),
        ];
    }
}
