@extends('layouts.app')

@section('title', 'Default Layout')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Data Prestasi</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Layout</a></div>
                    <div class="breadcrumb-item">Default Layout</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Preview</h2>
                <p class="section-lead">Preview data prestasi mahasiswa</p>
                <div class="card">
                    <div class="card-header">
                        <h4>Tabel Preview Data Prestasi Mahasiswa</h4>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table-hover table display nowrap" id="table" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Id Prodi</th>
                                        <th scope="col">Jurusan</th>
                                        <th scope="col">Id Politeknik</th>
                                        <th scope="col">Politeknik</th>
                                        <th scope="col">Id Kelompok Bidang</th>
                                        <th scope="col">Kelompok Bidang</th>
                                        <th scope="col">Quota</th>
                                        <th scope="col">Tertampung</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach($prodi as $quotaa) --}}
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    {{-- @endforeach --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="m-4 p-4">
                            {{-- {!! $prodi->links() !!} --}}
                        </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script> --}}
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/jquery-ui-dist/jquery-ui.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="../../js/table.js"></script>
@endpush