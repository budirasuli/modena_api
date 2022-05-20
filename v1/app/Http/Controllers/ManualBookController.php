<?php

namespace App\Http\Controllers;

use App\Models\ProductMaster;
use Illuminate\Http\Request;
use DB;
use Storage;

class ManualBookController extends Controller
{
    public function index($id, Request $request)
    {
        $title = $request->title;

        $manual_books = ProductMaster::select(
                'product_master.*',
                'product_segment.slug as segment_slug',
                'product_category.slug as category_slug',
                'product_sub_category.slug as sub_category_slug',
                'media.id as media_id',
                'media.path',
                'media.file_name',
                'media.content_type'
            )
            ->leftJoin('product_segment', function($join) {
                $join->on('product_segment.id_product_segment_id', '=', 'product_master.id_product_segment_id');
                $join->on('product_segment.country_code', '=', 'product_master.country_code');
                $join->on('product_segment.language_code', '=', 'product_master.language_code');
            })
            ->leftJoin('product_series', function($join) {
                $join->on('product_series.id_product_series_id', '=', 'product_master.id_product_series_id');
                $join->on('product_series.country_code', '=', 'product_master.country_code');
                $join->on('product_series.language_code', '=', 'product_master.language_code');
            })
            ->leftJoin('product_category', function($join) {
                $join->on('product_category.id_product_category_id', '=', 'product_master.id_product_category_id');
                $join->on('product_category.country_code', '=', 'product_master.country_code');
                $join->on('product_category.language_code', '=', 'product_master.language_code');
            })
            ->leftJoin('product_sub_category', function($join) {
                $join->on('product_sub_category.id_product_sub_category_id', '=', 'product_master.id_product_sub_category_id');
                $join->on('product_sub_category.country_code', '=', 'product_master.country_code');
                $join->on('product_sub_category.language_code', '=', 'product_master.language_code');
            })
            ->leftJoin('media', function($join) {
                $join->on('media.mediable_id', '=', 'product_master.id_product_master_id');
            })
            ->where('media.content_type',"LIKE",'manual_id')
            ->whereNull('product_master.deleted_at')
            ->where('product_master.country_code', "=", 'id')
            ->whereNotNull('media.mediable_id')
            ->where(function ($manual_books) use ($title) {
                $manual_books->whereRaw('product_master.master_name LIKE "%'.$title.'%"');
                $manual_books->orWhereRaw("REPLACE(product_master.type,' ','') LIKE '%".preg_replace('/\s+/', '', $title)."%'");
            })
            ->groupBy('id_product_master_id')
            ->limit(10)
            ->get();

            dd($manual_books);
    }
}
