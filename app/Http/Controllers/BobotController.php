<?php

namespace App\Http\Controllers;

use App\Models\CandidateMand;
use App\Models\CandidatePres;
use App\Models\CandidateTes;
use App\Models\Criteria;
use Exception;
use Hamcrest\Type\IsInteger;
use Hamcrest\Type\IsNumeric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BobotController extends Controller
{
    public function render()
    {
        return view('halaman.pembobotan', [
            'type_menu' => 'bobot',
        ]);
    }

    public function getTahun()
    {
        try {
            $year = Criteria::select('tahun')->where('table', 'candidates_mand')
                ->groupBy('tahun')->orderBy('tahun', 'desc')->get()->toArray();
            for ($x = 0; $x < count($year); $x++) {
                $year[$x] = $year[$x]['tahun'];
            }
            return response()->json([
                'tahun' => $year,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function getPend()
    {
        try {
            $this->validate(request(), [
                'tahun' => 'required|numeric',
            ]);
            $tahun = request('tahun');
            return response()->json([
                'pendidikan' => [
                    'D2', 'D3', 'S1', 'S2'
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function render_api()
    {
        try {
            $this->validate(request(), [
                'tahun' => 'required|numeric',
                'pendidikan' => 'required',
            ]);

            $pend   = request('pendidikan');
            $tahun  = request('tahun');

            $criteria = Criteria::select('bobot', 'kolom')->where('kode_criteria', $tahun . '_candidates_mand')->first()->toArray();
            $criterias = $criteria['bobot'];

            if ($criterias) {
                for ($index = 0; $index < count($criterias); $index++) {
                    $criterias[$index]['id'] = $index;
                };

                $order = array('prioritas', 'pembobotan', 'tambahan');

                usort($criterias, function ($a, $b) use ($order) {
                    $pos_a = array_search($a['tipe'], $order);
                    $pos_b = array_search($b['tipe'], $order);
                    return $pos_a - $pos_b;
                });
            } else {
                $criterias = [];
            };

            $kolom = $criteria['kolom'];

            return response()->json([
                'criteria'  => $criterias,
                'kolom'     => $kolom,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function getNilai()
    {
        try {
            $this->validate(request(), [
                'tahun' => 'required|numeric',
                'pendidikan' => 'required',
                'kolom' => 'required',
            ]);

            $pend = (request('pendidikan')) ? request('pendidikan') : 'S1';
            $tahun = (request('tahun')) ? request('tahun') : strval(date("Y"));
            $kolom = request('kolom');

            $candidate = CandidateMand::select(strval($kolom))
                ->where('periode', intval($tahun))
                ->groupBy(strval($kolom))->orderBy(strval($kolom), 'desc')
                ->get();


            if ($candidate[0][strval($kolom)] == null) {
                return response()->json([
                    'error' => 'Kolom tidak ditemukan',
                ]);
            }
            for ($x = 0; $x < count($candidate); $x++) {
                $candidate[$x] = $candidate[$x][strval($kolom)];
            }

            return response()->json([
                'nilai'  => $candidate,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function api_insert()
    {
        try {
            $this->validate(request(), [
                'tahun'         => 'required|numeric',
                'pendidikan'    => 'required',
                'pembobotan'    => 'required',
                'data'          => 'required'
            ]);

            $pend   = request('pendidikan');
            $tahun  = request('tahun');
            $data = request('data');

            if (Criteria::query()->where('kode_criteria', $tahun . '_candidates_mand')->exists()) {
                $criteria = Criteria::query()->where('kode_criteria', $tahun . '_candidates_mand')->first();
                switch (request('pembobotan')) {
                    case 'prioritas':
                        $array = [
                            'kolom'     => $data[0],
                            'nilai'     => $data[1],
                            'tipe'      => 'prioritas',
                        ];
                        break;

                    case 'pembobotan':
                        $array = [
                            'kolom'     => $data[0],
                            'nilai'     => $data[1],
                            'bobot'     => $data[2],
                            'tipe'      => 'pembobotan',
                        ];
                        break;

                    case 'tambahan':
                        $array = [
                            'kolom'     => $data[0],
                            'tipe'      => 'tambahan',
                        ];
                        break;

                    default:
                        return response()->json([
                            'error' => "pembobotan: attribute must whether 'prioritas', 'bobot', 'tambahan'",
                        ]);
                        break;
                }
                $bobot = (array) $criteria->bobot;

                if (is_numeric(array_search($array, $bobot))) {
                    return response()->json(['error' => "Data Telah Ditambahkan",]);
                } else {
                    array_push($bobot, $array);
                    $criteria->bobot = $bobot;
                }

                $criteria->save();
                return response()->json([
                    'status' => "Data " . ucfirst(request('pembobotan')) . " Berhasil Ditambahkan",
                ]);
            } else {
                return response()->json([
                    'error' => "Data tidak ditemukan, Pastikan anda telah memasukkan data dengan benar",
                ]);
            }
        } catch (Exception $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function api_delete()
    {
        try {
            $this->validate(request(), [
                'tahun'         => 'required|numeric',
                'pendidikan'    => 'required',
                'id'            => 'required|numeric'
            ]);

            $pend   = request('pendidikan');
            $tahun  = request('tahun');
            $id     = request('id');

            $criteria = Criteria::select('bobot')->where('kode_criteria', $tahun . '_candidates_mand')->first();
            $bobot = $criteria->bobot;

            unset($bobot[$id]);
            $bobot = array_values($bobot);
            $criteria->bobot = $bobot;
            $criteria->save();

            return response()->json([
                'status' => "Data Berhasil Dihapuskan",
            ]);
        } catch (Exception $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function api_edit()
    {
        try {
            $this->validate(request(), [
                'tahun'         => 'required|numeric',
                'pendidikan'    => 'required',
                'pembobotan'    => 'required',
                'id'            => 'required|numeric',
                'data'          => 'required',
            ]);

            $pend   = request('pendidikan');
            $tahun  = request('tahun');
            $tahap  = request('tahap');
            $id     = request('id');
            $data   = request('data');

            $criteria = Criteria::select('bobot')->where('kode_criteria', $tahun . '_candidates_mand')->first();
            $bobot = $criteria->bobot;

            //Masukin data ke template
            switch (request('pembobotan')) {
                case 'prioritas':
                    $array = [
                        'kolom'     => $data[0],
                        'nilai'     => $data[1],
                        'tipe'      => 'prioritas',
                    ];
                    break;

                case 'pembobotan':
                    $array = [
                        'kolom'     => $data[0],
                        'nilai'     => $data[1],
                        'bobot'     => $data[2],
                        'tipe'      => 'pembobotan',
                    ];
                    break;

                case 'tambahan':
                    $array = [
                        'kolom'     => $data[0],
                        'tipe'      => 'tambahan',
                    ];
                    break;

                default:
                    return response()->json([
                        'error' => "pembobotan: attribute must whether 'prioritas', 'bobot', 'tambahan'",
                    ]);
                    break;
            }

            //Cek bobot[id]
            if (array_key_exists($id, $bobot) == false) {
                return response()->json([
                    'error' => "id: attribute is not registered",
                ]);
            }

            $bobot[$id] = $array;
            $criteria->bobot = $bobot;
            $criteria->save();

            return response()->json([
                'status' => "Data Berhasil Diedit",
            ]);
        } catch (Exception $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }
}
