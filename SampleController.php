<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TestModel;

class SampleController1 extends Controller
{
    public function search(Request $request)
    {
        $convert = TestModel::convert_string('encrypt',$request->keyword);
        $sample = TestModel::select('*',DB::raw('CONCAT(control_number, ", ", control_tags) AS full_control_tag'))
        ->where('sample_number', 'like', "%{$convert}%")
        ->orWhere('account_number', 'like', "%{$convert}%")
        ->withAndWhereHas('sample_status_logs',function($query) {
            $query->orderBy('sample_status_log_id','desc');
            $query->withAndWhereHas('createdBy',function($query){
                
            });
        })
        ->first();
        
        if($sample){
            return view('sample.show', ['sample' => $sample]);
        }else{
            return view('errors.data_not_found');
        }
    }


    public function dailyReportPDF($id) 
    {
        if(auth()->user()->can('access','export_daily_report')){
            $reports_daily = Report::where('report_id', $id)
                            ->with(array('createdBy'=>function($query){
                            $query->select('user_id','title','first_name','last_name');
                            }))
                            ->with(array('updatedBy'=>function($query){
                            $query->select('user_id','title','first_name','last_name');
                            }))
                            ->first();

                            
            $pdf = PDF::loadView('pdf.individual_report.export_pdf_file_daily',[
                'reports_daily'=>$reports_daily,
                'downloaded_at'=> date('Y-m-d H:i:s'),
                'downloaded_by'=> Auth::user()->profile['first_name'].' '.Auth::user()->profile['last_name'],
            ])->setPaper('A4', 'landscape');
            return $pdf->download(date('mdY',strtotime($reports_daily['date_from'])).'.pdf');
        } else {
            return view('errors.no_access');
        }
    }
}
