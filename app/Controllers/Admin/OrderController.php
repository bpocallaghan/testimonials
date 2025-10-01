<?php

namespace Bpocallaghan\Testimonials\Controllers\Admin;

use App\Http\Requests;
use Illuminate\Http\Request;
use Bpocallaghan\Testimonials\Models\Testimonial;
use Bpocallaghan\Titan\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Response;

class OrderController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $html = $this->getOrderHtml();

        return $this->view('testimonials::order')->with('itemsHtml', $html);
    }

    /**
     * Update the order
     * @param Request $request
     * @return array
     */
    public function updateOrder(Request $request)
    {
        $navigation = json_decode($request->get('list'), true);

        foreach ($navigation as $key => $nav) {
            $row = $this->updateListOrder($nav['id'], ($key + 1));
        }

        return ['result' => 'success'];
    }

    /**
     * Generate the nestable html
     *
     * @param null $parent
     *
     * @return string
     */
    private function getOrderHtml($parent = null)
    {
        $html = '<ol class="dd-list">';

        $items = Testimonial::orderBy('list_order')->get();
        foreach ($items as $key => $item) {
            $html .= '<li class="dd-item" data-id="' . $item->id . '">';
            $html .= '<div class="dd-handle">';
            $html .= $item->customer . ' ' . ' <span style="float:right"> ' . substr(strip_tags($item->description), 0, 40) . '... </span></div>';
            $html .= '</li>';
        }

        $html .= '</ol>';

        return (count($items) >= 1 ? $html : '');
    }

    /**
     * Update Navigation Item, with new list order and parent id (list and parent can change)
     *
     * @param     $id
     * @param     $listOrder
     * @param int $parentId
     *
     * @return mixed
     */
    private function updateListOrder($id, $listOrder, $parentId = 0)
    {
        $row = Testimonial::find($id);
        $row->list_order = $listOrder;
        $row->save();

        return $row;
    }

    /**
     * Export testimonials data in various formats
     *
     * @param Request $request
     * @return Response
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        // Get testimonials data
        $testimonials = Testimonial::orderBy('list_order')->get();
        
        switch (strtolower($format)) {
            case 'json':
                return $this->exportJson($testimonials);
            case 'csv':
            default:
                return $this->exportCsv($testimonials);
        }
    }

    /**
     * Export data as CSV
     *
     * @param $testimonials
     * @return Response
     */
    private function exportCsv($testimonials)
    {
        $filename = 'testimonials_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($testimonials) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Customer',
                'Description',
                'Link',
                'List Order',
                'Created At',
                'Updated At'
            ]);
            
            // CSV data
            foreach ($testimonials as $testimonial) {
                fputcsv($file, [
                    $testimonial->id,
                    $testimonial->customer,
                    strip_tags($testimonial->description),
                    $testimonial->link,
                    $testimonial->list_order,
                    $testimonial->created_at,
                    $testimonial->updated_at
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data as JSON
     *
     * @param $testimonials
     * @return Response
     */
    private function exportJson($testimonials)
    {
        $filename = 'testimonials_export_' . date('Y-m-d_H-i-s') . '.json';
        
        $data = $testimonials->map(function($testimonial) {
            return [
                'id' => $testimonial->id,
                'customer' => $testimonial->customer,
                'description' => $testimonial->description,
                'link' => $testimonial->link,
                'list_order' => $testimonial->list_order,
                'created_at' => $testimonial->created_at,
                'updated_at' => $testimonial->updated_at
            ];
        });
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }
}