@extends('layouts.app')

@section('title', 'Form')

@push('style')
<!-- CSS Libraries -->
{{-- <link rel="stylesheet"
        href="assets/modules/datatables/datatables.min.css">
    <link rel="stylesheet"
        href="assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css"> --}}
<link rel="stylesheet" href="{{ asset('library/selectric/public/selectric.css') }}">
<link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css" />

@endpush

@section('main')
<div class="main-content" id="main-content" url="{{ route('api_renderProTes') }}">
    <section class="section">
        <div class="section-header">
            <h1>Data Kuota Program Studi</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="#">Seleksi Tes</a></div>
                <div class="breadcrumb-item">Data Kuota Program Studi</div>
            </div>
        </div>


        <div id="">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-pills">
                                <li class="nav-item">
                                    <a class="nav-link active"
                                        href="#">Data</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="#">Kuota</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{route('renderBindProdiTes')}}">Binding</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div class="align-items-left">
                                    <button class="btn btn-primary " id="modal-add"><i class="fas fa-plus"></i> Add</button>
                                </div>
                            </div>
                            <br>

                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close"
                                            data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        {{ session('success') }}
                                    </div>
                                </div>
                            @endif

                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                <div class="alert alert-danger alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close"
                                            data-dismiss="alert">
                                            <span>&times;</span>
                                        </button>
                                        {{ $error }}
                                    </div>
                                </div>
                                @endforeach
                            @endif

                            <br>
                            <div class="">
                                <table class="table-hover table-md table display nowrap" id="table-prodi-tes" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Id Prodi</th>
                                            <th scope="col">Prodi</th>
                                            <th scope="col">Kelompok Belajar</th>
                                            <th scope="col">Kuota</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer row">
                            
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="prodi-tes-edit" hidden>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="my-2">Binding Data Program Studi</h4>
                    </div>
                    <div id="edit-prodi-tes" url="{{ route('api_editProTes') }}">
                    @csrf
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Id Program Studi</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" id="id" name="id_prodi" class="form-control"/>
                                    <input type="text" id="id_obj" name="id_obj" class="form-control" hidden/>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Program Studi</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" id="prodi" name="prodi" class="form-control" required/>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Kelompok Bidang</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" id="bidang" name="bidang" class="form-control" required/>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Kuota</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" id="kuota" name="kuota" class="form-control" placeholder="Kuota" required/>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <input class="btn btn-success" type="button" onclick="submit()" value="Submit">
                                    <input class="btn btn-danger ml-2"type="button" value="Close" onclick="tutup()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form class="modal-part" id="modal-add-prodi" action="{{route('addProdiTes')}}" method="post">
        @csrf
        <p>Tambahkan Data <code>Program Studi</code> Terbaru</p>
        <div class="form-group">
            <label>Nomor Program Studi</label>
            <input type="number" class="form-control" placeholder="Id Prodi" name="id_prodi" required>
        </div>
        <div class="form-group">
            <label>Program Studi</label>
            <input type="text" class="form-control" placeholder="Program Studi" name="prodi" required>
        </div>
        <div class="form-group">
            <label>Kelompok Bidang</label>
            <input type="text" class="form-control" placeholder="Kelompok Bidang" name="kelompok_bidang" required>
        </div>
        <div class="form-group">
            <label>Kuota</label>
            <input type="number" class="form-control" placeholder="Kuota" name="kuota" required>
        </div>
        <div class="d-flex justify-content-end">
            <input type="submit" class="btn btn-primary btn-shadow" value="Tambah">
        </div>
    </form>

    @foreach($prodi as $prodii => $data)
    <form class="modal-part" id="modal-edit-prodi-{{ $prodii + $prodi->firstItem()}}" action="{{route('editProdiTes',['id' => $data['_id']])}}" method="post">
        @csrf
        <p>Tambahkan Data <code>Program Studi</code> Terbaru</p>
        <div class="form-group">
            <label>Nomor Program Studi</label>
            <input type="number" class="form-control" placeholder="Id Prodi" name="id_prodi" value="{{$data['id_prodi']}}" required>
        </div>
        <div class="form-group">
            <label>Program Studi</label>
            <input type="text" class="form-control" placeholder="Program Studi" name="prodi" value="{{$data['prodi']}}" required>
        </div>
        <div class="form-group">
            <label>Kelompok Bidang</label>
            <input type="text" class="form-control" placeholder="Kelompok Bidang" name="kelompok_bidang" value="{{$data['kelompok_bidang']}}" required>
        </div>
        <div class="form-group">
            <label>Kuota</label>
            <input type="number" class="form-control" placeholder="Kuota" name="kuota" value="{{$data['kuota']}}" required>
        </div>
        <div class="d-flex justify-content-end">
            <input type="submit" class="btn btn-primary btn-shadow" value="Edit">
        </div>
    </form>
    @endforeach

</div>
@endsection

@push('scripts')
<!-- JS Libraies -->
<script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
<script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>
<script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>
<script src="{{ asset('js/stisla.js') }}"></script>
<script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<!-- Page Specific JS File -->
<script src="../../js/table.js"></script>
<script src="../../js/style.js"></script>
<script src="../../js/prodi.js"></script>
<script src="../../js/prodi-tes.js"></script>
<script src="{{ asset('js/page/modules-sweetalert.js') }}"></script>

@endpush