<?php

namespace App\Http\Controllers;

use App\Imports\ProdiImport;
use App\Models\Criteria;
use App\Models\Prodi;
use App\Models\ProdiTes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProdiTesController extends Controller
{
    public function import (Request $request) 
    {
        if ($request->file('excel') == null ||
        $request->input('periode') == '' ||
        $request->input('banyakCollumn') == 0) {
            Session::flash('error','Pastikan anda telah mengisi semua input');
            return redirect()->back();
        }

        try {
            $array = (new ProdiImport())->toArray($request->file('excel'));

            $namedkey = array();
            for ($i=0; $i < $request->input('banyakCollumn'); $i++) {
                $namedkey[$i]=strtolower($request->input('collumn-'.strval($i)));
            }
    
            $periode = $request->input('periode');
    
            $criteria = array(
                'tahun' => $periode,
                'criteria' => $namedkey,
                'table' => 'prodi_tes',
                'kode_criteria' => strval($periode).'_prodi_tes',
            );
    
            for ($i=0; $i < count($array[0]); $i++) {
                // $fil = array();
                for ($i=0; $i < count($array[0]); $i++) {
                    // $fil = array();
                    for ($ab=0; $ab < count($namedkey); $ab++) { 
                        $fil[$namedkey[$ab]] = trim($array[0][$i][$namedkey[$ab]]);
                    };
                    $fil['periode'] = $periode;
                    $fil['status'] = 0;
                    $filtered[] = $fil;
                }
        
                
                if (Criteria::query()->where('kode_criteria',strval($periode).'_prodi_tes')->exists()) {
                    Criteria::query()->where('kode_criteria',strval($periode).'_prodi_tes')->update($criteria);
                } else {
                    Criteria::insert($criteria);
                }

                ProdiTes::query()->where('status',0)->delete();
                ProdiTes::insert($filtered);
                
                Session::flash('sukses','Data Berhasil ditambahkan');
                return redirect()->back();
            }
            
        }catch (Exception $error) {
            Session::flash('error', $error);
            return redirect()->back();
        }
    }

    public function render(Request $request)
    {
        $search = $request->input('search');
        $collumn = $request->input('kolom');
        $prodi = ProdiTes::query()->where('status',0)
            ->when( $search && $collumn, function($query) use ($collumn,$search) {
                return $query->where(function($query) use ($collumn,$search) {
                    $query->where($collumn, 'like', '%'.$search . '%');
                });
            })
            ->paginate(10);

        $criteria = Criteria::query()->where('table', 'prodi_tes')->get();
        
        if($request->all() && empty($prodi->first())){
            Session::flash('error1','Data Prodi Tidak Tersedia');
        }

        return view('halaman.prodi-tes',[
            'type_menu' => 'tes',
            'prodi' => $prodi,
            'criteria' => $criteria,
            'searchbar' => [$collumn, $search],
        ]);
    }

    public function cancel(){
        ProdiTes::query()->where('status',0)->delete();
        return redirect('/prodi-tes');
    }

    public function save(){
        ProdiTes::query()->where('status',1)->delete();
        ProdiTes::query()->where('status',0)->update(['status' => 1]);
        return redirect('/preview-tes');
    }
}
