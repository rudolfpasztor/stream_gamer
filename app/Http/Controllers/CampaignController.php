<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Campaign;
use App\Http\Resources\CampaignResource as CampaignResource;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get point list
        $campaigns = Campaign::orderby('created_at', 'desc')->paginate(15);
        //return collection as resource
        return CampaignResource::collection($campaigns);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $campaign = $request->isMethod('put') ? Campaign::findOrFail($request->id) : new Campaign;

        if($request->input('status')) {
            $campaign->status = $request->input('status');
        } else {
            $campaign->status = "active";
        }
        $campaign->id = $request->input('id');
        $campaign->name = $request->input('name');
        $campaign->currency_name = $request->input('currency_name');

        if($campaign->save()) {
            return new CampaignResource($campaign);
        }
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id)
    {
        $campaign = Campaign::findOrFail($id);

        return new CampaignResource($campaign);
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(Campaign $campaign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, Campaign $campaign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy( $id )
    {
        $campaign = Campaign::findOrFail($id);
        if($campaign->delete()) {
            return new CampaignResource($campaign);
        }
    }
}
