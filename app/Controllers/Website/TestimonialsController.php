<?php

namespace Bpocallaghan\Testimonials\Controllers\Website;

use App\Http\Requests;
use Bpocallaghan\Testimonials\Models\Testimonial;
use App\Http\Controllers\Website\WebsiteController;

class TestimonialsController extends WebsiteController
{
    /**
     * Show the testimonials page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Testimonial::orderBy('list_order')->get();

        return $this->view('testimonials::testimonials', compact('items'));
    }
}
