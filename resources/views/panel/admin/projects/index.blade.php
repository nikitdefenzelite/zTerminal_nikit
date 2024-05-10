@extends('layouts.main')
@section('title', 'Projects')
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
        ['name'=>'Projects', 'url'=> "javascript:void(0);", 'class' => 'active']
        ]
        @endphp
        <!-- push external head elements to head -->
        @push('head')
        @endpush
        <div class="container-fluid">
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <div class="page-header-title">
                            <i class="ik ik-grid bg-blue"></i>
                            <div class="d-inline">
                                <h5>Projects</h5>
                                <span>{{ __('admin/ui.list_of') }} Projects</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        @include("panel.admin.include.breadcrumb")
                    </div>
                </div>
            </div>
            <!-- start message area-->
            <div class="ajax-message text-center"></div>
            <!-- end message area-->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h3>Projects</h3>
                            <div class="dropdown d-flex justicy-content-right">
                                @if(auth()->user()->isAbleTo('add_project'))
                                <a href="#" id="importProjectLink"class="btn btn-icon btn-sm btn-outline-success mr-2 importProjectLink" title="Add New Project">
                                    <i class="ik ik-upload" aria-hidden="true"></i>
                                </a>
                                    <a href="{{ route('admin.projects.create') }}"
                                        class="btn btn-sm btn-outline-primary mr-2" title="Add New Project"><i class="fa fa-plus" aria-hidden="true"></i> {{ __('admin/ui.add') }} 
                                    </a>
                                @endif
                                <button
                                    class="dropdown-toggle p-0 custom-dopdown bulk-btn btn btn-light" type="button" id="dropdownMenu1"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                        class="ik ik-more-vertical fa-lg pl-1"></i></button>
                                <ul class="dropdown-menu dropdown-position multi-level" role="menu"
                                    aria-labelledby="dropdownMenu">                           <a href="javascript:void(0)" data-action="Restore"
                                            class="dropdown-item action trash-option @if(request()->get('trash') != 1)  d-none @endif">Restore</a>
                                      
                                            <button type="submit" class="dropdown-item action text-danger fw-700"
                                                    data-value="" data-message="You want to delete these?"
                                                    data-action="Delete Permanently" data-callback="bulkDeleteCallback"><i
                                                    class="ik ik-trash"></i> Bulk
                                                Delete
                                            </button>
                                    
                                </ul>
                            </div>
                        </div>
                        <div id="ajax-container">
                            @include('panel.admin.projects.load')
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @include('panel.admin.projects.include.filter')
        @include('panel.admin.projects.include.import-model')
        <!-- push external js -->
        @push('script')
        @include('panel.admin.include.more-action',['actionUrl'=>
        "admin/projects",'routeClass'=>"projects"])
        <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
        <script>
            
            $(document).ready(function() {
                $('#importProjectLink').click(function(e) {
                    e.preventDefault(); // Prevent the default link behavior

                    // Open the modal
                    $('#importProjectModal').modal('show');
                });
            });

            $(document).ready(function() {
                $('#projectSelectionForm').submit(function(e) {
                    e.preventDefault(); // Prevent the default form submission

                    var projectId = $('#projectSelect').val();
                    var api_key = $('#postmanApiKey').val();
                    var directory_name = $('#directoryName').val();

                    // Hide submit button text and show loading indicator
                    $('#buttonText').hide();
                    $('#loadingIndicator').show();

                    $.ajax({
                        type: 'POST',
                        url: '{{ route("admin.projects.import-postman-collection") }}', // Make sure to replace 'projects.create' with your actual route
                        data: {
                            _token: '{{ csrf_token() }}',
                            project_id: projectId,
                            api_key: api_key,
                            directory_name: directory_name
                        },
                        success: function(response) {
                            alert(response);
                            $('#loadingIndicator').hide();
                            $('#importProjectModal').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            $('#loadingIndicator').hide();
                            $('#importProjectModal').modal('hide');
                        },
                        complete: function() {
                            $('#buttonText').show();
                        }
                    });
                });
            });

        
            function tableHeadIconFixer(clonedTable) {
            clonedTable.find('i.icon-head').each(function() {
            var dataTitle = $(this).data('title');
            $(this).replaceWith(dataTitle);
            });
            return clonedTable;
            }

            function html_table_to_excel(type) {
            let table_core = $("#table").clone();
            let clonedTable = $("#table").clone();
            clonedTable.find('[class*="no-export"]').remove();
            clonedTable.find('[class*="d-none"]').remove();
            clonedTable = tableHeadIconFixer(clonedTable);
            $("#table").html(clonedTable.html());

            // Use in reverse format beacuse we are prepending it.
            var report_format = [
            { 'label': "Status", 'value': "All Status" },
            { 'label': "Date Range", 'value': "-----" },
            { 'label': "Report Name", 'value': "Projects Report" },
            { 'label': "Company", 'value': "zStarter" }
            ];

            var report_name = report_format[2]['value']+" | "+Date.now();
            // Create a single blank row
            var blankRow = document.createElement('tr');
            var blankCell = document.createElement('th');
            blankCell.colSpan = clonedTable.find('thead tr th').length;
            blankRow.appendChild(blankCell);

            // Append the blank row to the cloned table's thead
            clonedTable.find('thead').prepend(blankRow);

            // Iterate through the report_format array and add metadata rows to the cloned table's thead
            $.each(report_format, function (index, item) {
            var metadataRow = document.createElement('tr');
            var labelCell = document.createElement('th');
            var valueCell = document.createElement('th');

            labelCell.innerHTML = item.label;
            valueCell.innerHTML = item.value;

            metadataRow.appendChild(labelCell);
            metadataRow.appendChild(valueCell);

            clonedTable.find('thead').prepend(metadataRow);
            });

            var data = clonedTable[0]; // Use the cloned table for export

            var file = XLSX.utils.table_to_book(data, {
            sheet: "sheet1"
            });

            // Write and download the Excel file
            XLSX.write(file, {
            bookType: type,
            bookSST: true,
            type: 'base64'
            });
            XLSX.writeFile(file, report_name+'.' + type);

            $("#table").html(table_core.html());

            }

            $(document).on('click','#export_button',function(){
            html_table_to_excel('xlsx');
            })

            

    </script>
            <script>
    </script>
            @endpush
            @endsection
