<table id="table" class="table">
    <thead>
        <tr>
            @if (!isset($print_mode))
                <th class="no-export">
                    <input type="checkbox" class="mr-2 " id="selectall" value="">
                    {{ __('Actions') }}
                </th>
                <th class="text-center no-export"># <div class="table-div"><i class="ik ik-arrow-up  asc"
                            data-val="id"></i><i class="ik ik ik-arrow-down desc" data-val="id"></i></div>
                </th>
            @endif
            <th>Name</th>
            <th>System Payload</th>
            <th>Postman Payload</th>
            <th class="col_1"> Register</th>
            <th> Interface </th>
            <th class="">
                <i class="icon-head" data-title="Created At"><i class="fa fa-clock pl-30"></i></i>
            </th>
        </tr>
    </thead>
    <tbody>
        @if ($projects->count() > 0)
            @foreach ($projects as $project)
                <tr id="{{ $project->id }}">
                    @if (!isset($print_mode))
                        <td class="no-export">
                            <div class="dropdown d-flex">
                                <input type="checkbox" class="mr-2 text-center" name="id" onclick="countSelected()"
                                    value="{{ $project->id }}">
                                @if (auth()->user()->isAbleTo('edit_project') || auth()->user()->isAbleTo('delete_project'))
                                    <button style="background: transparent;border:none;" class="dropdown-toggle p-0"
                                        type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false"><i class="ik ik-more-vertical pl-1"></i></button>
                                    <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                                       
                                        @if (auth()->user()->isAbleTo('view_cy_runners'))
                                            <a href="{{ route('admin.cy-runners.index', ['project_id'=>secureToken($project->id)]) }}"
                                                title="Scenarios" class="dropdown-item ">
                                                <li class="p-0"><i class="ik ik-play mr-2"></i>Scenarios</li>
                                            </a>
                                        @endif
                                        @if (auth()->user()->isAbleTo('view_api_runners'))
                                            <a href="{{ route('admin.api-runners.index', ['project_id'=>secureToken($project->id)]) }}"
                                                title="Scenarios" class="dropdown-item ">
                                                <li class="p-0"><i class="ik ik-terminal mr-2"></i>Api Runner</li>
                                            </a>
                                        @endif
                                        @if (auth()->user()->isAbleTo('edit_project'))
                                            <a href="{{ route('admin.projects.edit', secureToken($project->id)) }}"
                                                title="Edit Project" class="dropdown-item ">
                                                <li class="p-0"><i class="ik ik-edit mr-2"></i>Edit</li>
                                            </a>
                                        @endif
                                        @if (auth()->user()->isAbleTo('delete_project'))
                                            <a href="{{ route('admin.projects.destroy', secureToken($project->id)) }}"
                                                title="Delete Project"
                                                class="dropdown-item text-danger fw-700 delete-item ">
                                                <li class=" p-0"><i class="ik ik-trash mr-2"></i>Delete</li>
                                            </a>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                        </td>
                        <td class="text-center no-export"> {{ $project->getPrefix() }}</td>
                    @endif
                    <td class="col_1">{{ @$project->name ?? 'N/A' }}
                        <td class="col_1">{{ @$project->system_variable_payload['base_url'] ?? 'N/A' }}
                    <td class="col_1">{{ @$project->postman_payload['project_repo_url'] ?? 'N/A' }}
                    </td>
                    <td class="col_1">{{ @$project->project_register_id ?? 'N/A' }}
                    </td>
                    <td class="col_6">
                        <a class="btn btn-primary mr-2" title="Flow Runner" target="_blank" href="{{route('admin.cy-runners.get-bulk-scenario',$project->id)}}">
                            <i class="ik ik-play mr-0"></i>
                        </a>
                        <a class="btn btn-secondary" title="Api Runner " target="_blank" href="{{route('admin.api-runners.get-bulk-scenario',$project->id)}}">
                            <i class="ik ik-terminal mr-0"></i>
                        </a>
                    </td>
                    <td class="col_5">{{ $project->created_at->diffForHumans() ?? '...' }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="8">No Data Found...</td>
            </tr>
        @endif
    </tbody>
</table>
@include('panel.admin.projects.include.run-modal')
