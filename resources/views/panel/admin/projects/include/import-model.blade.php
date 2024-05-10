<div class="modal fade" id="importProjectModal" tabindex="-1" role="dialog" aria-labelledby="importProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importProjectModalLabel">Import Project</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="projectSelectionForm" action="" type='post'>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="postmanApiKey">Api Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="postmanApiKey" name="postmanApiKey" placeholder="Enter Postman Collection API Key" required>
                    </div>
                    <div class="form-group">
                        <label for="directoryName">Directory Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="directoryName" name="directoryName" placeholder="Enter Postman Collection Directory Name" required>
                    </div>
                    <div class="form-group">
                        <label for="projectSelect">Select Project <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="projectSelect" name="project_id" required>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <span id="buttonText">Submit</span> <!-- Initially show submit button text -->
                        <div id="loadingIndicator" style="display: none;">
                            <i class="fas fa-circle-notch fa-spin"></i>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
