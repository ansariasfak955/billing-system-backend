<?php
  
namespace App\Http\Controllers;
  
use App\Models\TermsConditions;
use Illuminate\Http\Request;
  
class TermsConditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $termscondition = TermsConditions::latest()->paginate(5);
      
        return view('backend.pages.termsconditions.index',compact('termscondition'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
  
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.pages.termsconditions.create');
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
      
        TermsConditions::create($request->all());
       
        return redirect()->route('termsconditions.index')
                        ->with('success','Terms and conditions page created successfully.');
    }
  
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TermsConditions  $product
     * @return \Illuminate\Http\Response
     */
    public function show(TermsConditions $termscondition)
    {
        return view('backend.pages.termsconditions.show',compact('termscondition'));
    }
  
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TermsConditions  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(TermsConditions $termscondition)
    {
        return view('backend.pages.termsconditions.edit',compact('termscondition'));
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TermsConditions  $termscondition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TermsConditions $termscondition)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        
        $termscondition->title = $request->title;
        $termscondition->description = $request->description;
        $termscondition->save();
        
      
        return redirect()->route('termsconditions.index')
                        ->with('success','termsconditions updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TermsConditions  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(TermsConditions $termscondition)
    {
        $termscondition->delete();
       
        return redirect()->route('termsconditions.index')
                        ->with('success','termsconditions deleted successfully');
    }
}