@extends('layouts.main')
@section('title', @$label)
@section('content')
    @php
        $breadcrumb_arr = [['name' => $label, 'url' => 'javascript:void(0);', 'class' => 'active']];
    @endphp

    <style>
        .ticket-card {
            margin-bottom: 20px;
        }

        .bg-color {
            background-color: #fff;
        }
    </style>

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-grid bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ getGreetingBasedOnTime() }}</h5>
                        </div>
                    </div>
                    <span>
                        Namaste <span class="text-dark fw-700">{{ auth()->user()->full_name }}</span>
                    </span>
                </div>
                <div class="col-lg-4">
                    @include('panel.admin.include.breadcrumb')
                </div>
            </div>
        </div>

        <div class="row clearfix ">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="row">
                    <a class="col-lg-3 col-md-6 col-sm-12" href="#">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h3 class="text-primary">{{$projectCount}}</h3>
                                        <p class="card-subtitle text-muted fw-500">Projects</p>
                                    </div>
                                    <div class="icon">
                                        <i class=" ik ik-layers"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <!-- <a class="col-lg-3 col-md-6 col-sm-12" href="#">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h3 class="text-primary">54</h3>
                                        <p class="card-subtitle text-muted fw-500">Verifications</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-user-shield fa-sm"></i>
                                    </div>
                                </div>
                                <div class="progress mt-3 mb-1 progress-6">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 63%;"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="text-muted f12">
                                    <span>Progress</span>
                                    <span class="float-right">63%</span>
                                </div>
                            </div>
                        </div>
                    </a> -->
                    <a class="col-lg-3 col-md-6 col-sm-12" href="#">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h3 class="text-primary">{{$cyRunnerLogCount}}</h3>
                                        <p class="card-subtitle text-muted fw-500">CyRunner Logs</p>
                                    </div>
                                    <div class="icon">
                                        <i class=" ik ik-pie-chart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a class="col-lg-3 col-md-6 col-sm-12" href="#">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="state">
                                        <h3 class="text-primary">{{$apiRunnerLogCount}}</h3>
                                        <p class="card-subtitle text-muted fw-500">Api Runner Logs</p>
                                    </div>
                                    <div class="icon">
                                        <i class=" ik ik-hard-drive"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>


    @push('script')
        
    @endpush
@endsection
