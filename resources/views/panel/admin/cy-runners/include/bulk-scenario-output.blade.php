<div class="col-md-6 col-12">
  <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="d-flex align-items-center mb-3">
                    <i class="ik ik-terminal fa-lg text-dark"></i>
                    <h6 class="mb-0 ml-2 fw-bold text-dark">{{ @$response['cy_runner']['name'] }} </h6>
                </div>
            </div>
            <div >
                <form action="{{ url('admin/upload/task') }}" method="post" enctype="multipart/form-data" class="ajaxForm" id="ProjectForm">
                    @csrf
                   <input type="hidden" name="project_id" value="{{$response['project_response']['project_register_id']}}">
                   <input type="hidden" name="task_description" value="{{$response['response']['payload']}}">

                   <button type="submit" title="Error Task Upload" id="" class="btn btn-secondary">
                    <i class="ik ik-external-link"></i>
                   </button>    
                </form>
        </div>
        </div>
        
        

          <div class="video-container">
              <video controls class="w-100">
                  <source src="{{ asset('storage/videos/' . $response['response']['id'] . '/runner.cy.js.mp4') }}" type="video/mp4">
                  Your browser does not support the video tag.
              </video>
          </div>
          <div class="p-4 border-top" style="overflow-y: auto; max-height: 200px;">
              <i class="fa fa-bug text-danger"></i>
              <span>{!! nl2br(e($response['response']['payload'])) !!}</span>
          </div>
      </div>
  </div>
</div>
