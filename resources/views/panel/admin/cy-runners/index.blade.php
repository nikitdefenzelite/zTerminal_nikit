@extends('layouts.main')
@section('title', 'Cy Runners')
@section('content')
    @php
        /**
         * Cy Runner
         *
         * @category ZStarter
         *
         * @ref zCURD
         * @author Defenzelite <hq@defenzelite.com>
         * @license https://www.defenzelite.com Defenzelite Private Limited
         * @version <zStarter: 1.1.0>
         * @link https://www.defenzelite.com
         */
        $breadcrumb_arr = [['name' => 'Cy Runners', 'url' => 'javascript:void(0);', 'class' => 'active']];
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
                            <h5>{{@$project->name}} Cy Runners</h5>
                            <span>{{ __('admin/ui.list_of') }} Cy Runners</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @include('panel.admin.include.breadcrumb')
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
                        <h3>Cy Runners</h3>
                        <span class="font-weight-bold border-bottom trash-option   d-none ">Trash</span>
                        <div class="d-flex justicy-content-right">
                            @if (auth()->user()->isAbleTo('add_cy_runner'))
                                <a href="{{ route('admin.cy-runners.create',['project_id'=>request()->get('project_id')]) }}" class="btn btn-sm btn-outline-primary mr-2"
                                    title="Add New Cy Runner"><i class="fa fa-plus" aria-hidden="true"></i>
                                    {{ __('admin/ui.add') }} </a>
                            @endif
                            <div class="dropdown d-flex justicy-content-left">
                                <button class="dropdown-toggle p-0 custom-dopdown bulk-btn btn btn-light" type="button"
                                    id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                        class="ik ik-more-vertical fa-lg pl-1"></i></button>
                                <ul class="dropdown-menu dropdown-position multi-level" role="menu"
                                    aria-labelledby="dropdownMenu">
                                    @php
                                        $arr = ['Draft', 'Active', 'Discard'];
                                    @endphp
                                    @foreach (getSelectValues($arr) as $key => $option)
                                        <a href="javascript:void(0)" class="dropdown-item action"
                                            data-action="status-{{ $option }}">{{ $option }}</a>
                                    @endforeach
                                    <a href="javascript:void(0)" data-action="Restore"
                                        class="dropdown-item action trash-option @if (request()->get('trash') != 1) d-none @endif">Restore</a>
                                    <hr class="m-1">
                                    <button type="submit" class="dropdown-item action text-danger fw-700" data-value=""
                                        data-message="You want to delete these?" data-action="Delete Permanently"
                                        data-callback="bulkDeleteCallback"><i class="ik ik-trash"></i> Bulk
                                        Delete
                                    </button>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="ajax-container">
                        @include('panel.admin.cy-runners.load')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('panel.admin.cy-runners.include.filter')
    <!-- push external js -->
    @push('script')
        @include('panel.admin.include.more-action', [
            'actionUrl' => 'admin/cy-runners',
            'routeClass' => 'cy-runners',
        ])
        <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
      
        <script>
            $(document).on('submit','.ajaxForm',function(e){
                e.preventDefault();
                let route = $(this).attr('action');
                let method = $(this).attr('method');
                let data = new FormData(this);
                let response = postData(method,route,'json',data,null,null,1,null,'not-reload');
            })
        </script>
      <script>
           $(document).ready(function(){
            $('.scenarioRunner').on('click',function(){
                var cy_runner_id = $(this).data('id');
                var title = $(this).data('title');
                $('#scenarioRunnerTitle').html(title);
                $.ajax({
                    url: "{{ route('admin.cy-runners.run-scenario') }}",
                    method: "get",
                    data: {
                        cy_runner_id: cy_runner_id,
                        timestamp: new Date().getTime() // Add timestamp parameter
                    },
                    success: function(res) {
                        $('.all-scenarios').html(res);
                    }
                })
                $('#scenarioRunner').modal('show');
            })
        })

            
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
                var report_format = [{
                        'label': "Status",
                        'value': "All Status"
                    },
                    {
                        'label': "Date Range",
                        'value': "-----"
                    },
                    {
                        'label': "Report Name",
                        'value': "Cy Runners Report"
                    },
                    {
                        'label': "Company",
                        'value': "zStarter"
                    }
                ];

                var report_name = report_format[2]['value'] + " | " + Date.now();
                // Create a single blank row
                var blankRow = document.createElement('tr');
                var blankCell = document.createElement('th');
                blankCell.colSpan = clonedTable.find('thead tr th').length;
                blankRow.appendChild(blankCell);

                // Append the blank row to the cloned table's thead
                clonedTable.find('thead').prepend(blankRow);

                // Iterate through the report_format array and add metadata rows to the cloned table's thead
                $.each(report_format, function(index, item) {
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
                XLSX.writeFile(file, report_name + '.' + type);

                $("#table").html(table_core.html());

            }

            $(document).on('click', '#export_button', function() {
                html_table_to_excel('xlsx');
            })
        </script>
        <script></script>
    @endpush
@endsection
