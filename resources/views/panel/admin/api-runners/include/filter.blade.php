<div class="side-slide" style="right: -100%;">
    <div class="filter">
        <div class="card-header d-flex justify-content-between ">
            <h5 class="mt-3 mb-0">Filter</h5>
            <button type="button" class="close off-canvas mt-2 mb-0" data-type="close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.api-runners.index') }}"
                method="GET" id="TableForm" class="d-flex">
                <div class="row">
                                            <div class="form-group col-12">
                            <label for="">
Status</label>
                            <select id="status" name="status" class="select2 form-control course-filter">
                                <option readonly value="">Select status</option>
                                <option value="Published">Published</option>
                                <option value="Unpublished">Unpublished</option>
                            </select>
                        </div>
                                        <div class="form-group col-12">
                        <label for="">From Date</label>
                        <input type="date" name="from" class="form-control" value="">
                    </div>
                    <div class="form-group col-12">
                        <label for="">To Date</label>
                        <input type="date" name="to" class="form-control" value="">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <a href="javascript:void(0);" id="reset" type="button" class="btn btn-light ml-2">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
