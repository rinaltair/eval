<?php

namespace App\Http\Controllers;

use App\Models\CandidateMand;
use App\Models\Criteria;
use Exception;
use Illuminate\Http\Request;

class FilterMandiriController extends Controller
{
    public function render()
    {
        // dd(request()->all());
        
        $search = request('search');
        $collumn = request('kolom');

        $ncollumn = request('banyakCollumn');

        $filter = [];
        for ($i=0; $i < request('banyakCollumn'); $i++) {
            $filter[$i][0]=strtolower(request('kolom-'.strval($i)));
            $filter[$i][1]=strtolower(request('operator-'.strval($i)));
            $filter[$i][2]=strtolower(request('nilai-'.strval($i)));
        }

        // dd([$search, $collumn, $ncollumn]);
        $candidates = CandidateMand::query()->where('status','post-import')
            ->when( $ncollumn, function($query) use ($filter) {
                return $query->where(function($query) use ($filter) {
                    for ($a=0; $a < count($filter); $a++) { 
                        $query->where($filter[$a][0], $filter[$a][1] , intval($filter[$a][2]));
                    }
                });
            })
            ->when( $search && $collumn, function($query) use ($collumn,$search) {
                return $query->where(function($query) use ($collumn,$search) {
                    if (is_numeric($search)) {
                        $query->where($collumn, intval($search));
                    } else {
                        $query->where($collumn, 'like', '%'.$search . '%');
                    }
                });
            })
            ->paginate(10);
                
        $criteria = Criteria::query()->where('table', 'candidates_mand')->get();

        return view('halaman.filter-mandiri',[
            'type_menu' => 'mandiri',
            'candidates' => $candidates,
            'criteria' => $criteria,
            'searchbar' => [$collumn, $search],
            'filter' => $filter,
        ]);    
    }

    public function save(Request $request) 
    {
        try {
            $filter = [];
            $operator = [];
            for ($i=0; $i < request('banyakCollumn'); $i++) {
                $filter[$i]['kolom']=strtolower(request('kolom-'.strval($i)));

                match (request('operator-'.strval($i))) {
                    '=' => $filter[$i]['operator'] = 'et',
                    '>' => $filter[$i]['operator'] = 'gt',
                    '<' => $filter[$i]['operator'] = 'lt',
                    '>=' => $filter[$i]['operator'] = 'gtet',
                    '<=' => $filter[$i]['operator'] = 'ltet',
                    '<>' => $filter[$i]['operator'] = 'net',
                };

                $filter[$i]['nilai']=strtolower(request('nilai-'.strval($i)));
                $operator[$i]=strtolower(request('operator-'.strval($i)));
            }
            $criteria = array(
                'tahun' => now()->year,
                'kolom' => $filter,
                'table' => 'filter_candidates_mand',
                'kode_criteria' => strval(now()->year).'_filter_candidates_mand',
            );
            
            $candidates = CandidateMand::query()->where('status','post-import')
            ->when( request('banyakCollumn'), function($query) use ($filter, $operator) {
                return $query->where(function($query) use ($filter, $operator) {
                    for ($a=0; $a < count($operator); $a++) { 
                        $query->where($filter[$a]['kolom'], $operator[$a] , intval($filter[$a]['nilai']));
                    }
                });
            })
            ->update(['status' => 'filtered']);


            if (Criteria::query()->where('kode_criteria',strval(now()->year).'_filter_candidates_mand')->exists()) {
                Criteria::query()->where('kode_criteria',strval(now()->year).'_filter_candidates_mand')->update($criteria);
            } else {
                Criteria::insert($criteria);
            }

            CandidateMand::query()->where('status','post-import')->delete();

            return response()->json([
                'status'=>'done',
            ]);
        } catch (Exception $th) {
            return response()->json([
                // 'url'=>route(),
                'status'=>'error: '.$th,
            ]);        
        }

    }

    public function getFilter()
    {
        $criteria = Criteria::select('kolom')->where('table', 'filter_candidates_mand')->where('tahun', intval(request('tahun')))->first();
        $kolom = [];
        foreach($criteria->kolom as $ckolom){
            match ($ckolom['operator']) {
                'et' => $ckolom['operator'] = '=',
                'gt' => $ckolom['operator'] = '>',
                'lt' => $ckolom['operator'] = '<',
                'gtet' => $ckolom['operator'] = '>=',
                'ltet' => $ckolom['operator'] = '<=',
                'net' => $ckolom['operator'] = '<>',
            };
            array_push($kolom,$ckolom);
        }
        return response()->json([
            'kolom'=>$kolom,
        ]);
    }
}