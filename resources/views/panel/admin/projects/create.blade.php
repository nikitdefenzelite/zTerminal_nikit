@extends('layouts.main')
@section('title', 'Project')
@section('content')
    @php
        /**
         * Project
         *
         * @category ZStarter
         *
         * @ref zCURD
         * @author Defenzelite <hq@defenzelite.com>
         * @license https://www.defenzelite.com Defenzelite Private Limited
         * @version <zStarter: 1.1.0>
         * @link https://www.defenzelite.com
         */
        $breadcrumb_arr = [
            ['name' => 'Project', 'url' => route('admin.projects.index'), 'class' => ''],
            ['name' => 'Add Project', 'url' => 'javascript:void(0);', 'class' => 'Active'],
        ];
    @endphp
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('panel/admin/plugins/mohithg-switchery/dist/switchery.min.css') }}">
        <style>
            .error {
                color: red;
            }
        </style>
    @endpush

    <div class="container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-grid bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Add') }} Project</h5>
                            <span>{{ __('Add a new record for') }} Project</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @include('panel.admin.include.breadcrumb')
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 mx-auto">
                <!-- start message area-->
                @include('panel.admin.include.message')
                <!-- end message area-->
                <div class="card ">
                    <div class="card-header">
                        <h3>{{ __('Create') }} Project</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.projects.store') }}" method="post" enctype="multipart/form-data"
                            class="ajaxForm" id="ProjectForm">
                            @csrf
                            <input type="hidden" name="request_with" value="create">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label for="name" class="control-label">Name<span
                                            class="text-danger">*</span></label>
                                    <input required class="form-control" name="name" type="text" id="name"
                                        value="{{ old('name', '') }}" placeholder="Enter Name">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">

                                <div class="form-group">
                                    <label for="project_register_id">Project Register <span
                                            class="text-danger">*</span></label>
                                    <input type="number" placeholder="Enter Project Register ID" name="project_register_id" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group {{ $errors->has('system_variable_payload') ? 'has-error' : '' }}">
                                    <label for="system_variable_payload" class="control-label"> System Variable Payload<span
                                            class="text-danger">*</span></label>
                                    <textarea required class="form-control" name="system_variable_payload" type="text" id="system_variable_payload"
                                        value="{{ old('system_variable_payload', '') }}" placeholder="Enter Name"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group {{ $errors->has('postman_payload') ? 'has-error' : '' }}">
                                    <label for="postman_payload" class="control-label">Postman Payload<span
                                            class="text-danger">*</span></label>
                                    <textarea required class="form-control" name="postman_payload" type="textar" id="postman_payload"
                                        value="{{ old('postman_payload', '') }}" placeholder="Enter Postman Payload"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 ml-auto">
                                <div class="form-group">
                                    <button type="submit"
                                        class="btn btn-primary floating-btn ajax-btn">{{ __('Create') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script>
            $('.ajaxForm').on('submit', function(e) {
                e.preventDefault();
                let route = $(this).attr('action');
                let method = $(this).attr('method');
                let data = new FormData(this);
                let redirectUrl = "{{ url('admin/projects') }}";
                let response = postData(method, route, 'json', data, null, null, 1, null, redirectUrl);
            })
        </script>
    @endpush
@endsection
