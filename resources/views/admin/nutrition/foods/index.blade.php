@extends('layouts.admin')

@php
  $title = 'Master Food Data';
  $subTitle = 'Diet Chart / Master Food Data';
@endphp

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Master Food Data</h5>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
        <i class="fa fa-upload"></i> Bulk Upload
      </button>
    </div>
    
    <div class="card-body">
      <div class="table-responsive">
        <table class="table basic-data-table" id="foodsTable">
          <thead>
            <tr>
              <th>Dish ID</th>
              <th>Dish Name</th>
              <th>Food Group</th>
              <th>Slot</th>
              <th>Diet Type</th>
              <th>Is Veg</th>
              <th>Region</th>
              <th>Energy (kcal)</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loaded via AJAX -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Bulk Upload Modal -->
  <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Bulk Upload Foods (Excel / CSV)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Choose File (.xlsx, .xls, .csv)</label>
            <input type="file" id="bulkUploadFile" class="form-control" accept=".xlsx,.xls,.csv">
            <small class="text-muted d-block mt-2">
              Columns: dish_id, dish_name, food_group, slot, diet_type, is_veg, region, energy_kcal
            </small>
          </div>
          <div id="bulkUploadResult"></div>

          <!-- Failed Rows Table -->
          <div id="bulkUploadFailedWrap" style="display:none;">
            <hr>
            <h6 class="text-danger mb-2">Failed Rows</h6>
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
              <table class="table table-sm table-bordered">
                <thead class="table-danger">
                  <tr>
                    <th>Dish ID</th>
                    <th>Dish Name</th>
                    <th>Food Group</th>
                    <th>Slot</th>
                    <th>Reason</th>
                  </tr>
                </thead>
                <tbody id="bulkUploadFailedBody"></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="bulkUploadSubmit">
            <i class="fa fa-upload"></i> Upload
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@php
$script = '<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable("#foodsTable")) {
            $("#foodsTable").DataTable().destroy();
        }
        
        var table = $("#foodsTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: "' . route('admin.nutrition.foods.data') . '",
            columns: [
                { data: "dish_id", name: "dish_id" },
                { data: "dish_name", name: "dish_name" },
                { data: "food_group", name: "food_group" },
                { data: "slot", name: "slot" },
                { data: "diet_type", name: "diet_type" },
                { data: "is_veg", name: "is_veg" },
                { data: "region", name: "region" },
                { data: "energy_kcal", name: "energy_kcal" },
                { 
                    data: "id", 
                    name: "action", 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        return \'<button class="btn btn-sm btn-danger delete-food-btn" data-id="\' + data + \'" title="Delete"><i class="fa fa-trash"></i> Delete</button>\';
                    }
                }
            ],
            order: [[1, "asc"]],
            language: {
                searchPlaceholder: "Search records...",
                processing: \'<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> \'
            }
        });

        // Delete button handler
        $(document).on("click", ".delete-food-btn", function() {
            var id = $(this).data("id");
            var button = $(this);

            if (!confirm("Are you sure you want to delete this food item?")) {
                return;
            }

            button.prop("disabled", true).html(\'<i class="fa fa-spinner fa-spin"></i> Deleting...\');

            $.ajax({
                url: "' . url('admin/nutrition/foods') . '/" + id,
                type: "POST",
                data: {
                    _method: "DELETE",
                    _token: $(\'meta[name="csrf-token"]\').attr("content")
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload(null, false);
                        if (typeof toastr !== "undefined") {
                            toastr.success(response.message || "Deleted successfully.");
                        } else {
                            alert(response.message || "Deleted successfully.");
                        }
                    } else {
                        button.prop("disabled", false).html(\'<i class="fa fa-trash"></i> Delete\');
                        alert(response.message || "Delete failed.");
                    }
                },
                error: function(xhr) {
                    button.prop("disabled", false).html(\'<i class="fa fa-trash"></i> Delete\');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Something went wrong.";
                    alert(msg);
                }
            });
        });

        // Bulk Upload submit handler
        $("#bulkUploadSubmit").on("click", function() {
            var fileInput = $("#bulkUploadFile")[0];
            var btn = $(this);

            if (!fileInput.files.length) {
                alert("Please choose a file first.");
                return;
            }

            var formData = new FormData();
            formData.append("file", fileInput.files[0]);
            formData.append("_token", $(\'meta[name="csrf-token"]\').attr("content"));

            btn.prop("disabled", true).html(\'<i class="fa fa-spinner fa-spin"></i> Uploading...\');
            $("#bulkUploadResult").html("");
            $("#bulkUploadFailedWrap").hide();
            $("#bulkUploadFailedBody").html("");

            $.ajax({
                url: "' . route('admin.nutrition.foods.bulk-upload.store') . '",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    btn.prop("disabled", false).html(\'<i class="fa fa-upload"></i> Upload\');

                    if (response.success) {
                        var resultHtml = \'<div class="alert alert-success">\' + response.message + \'</div>\';

                        if (response.failedCount && response.failedCount > 0) {
                            var warnMsg = \'<strong>⚠ \' + response.failedCount + \' row(s) failed!</strong><br>\';

                            if (response.duplicateCount && response.duplicateCount > 0) {
                                warnMsg += \'• <b>\' + response.duplicateCount + \'</b> duplicate <b>dish_id</b> — ye already database me hain.<br>\';
                            }

                            var otherFailed = response.failedCount - (response.duplicateCount || 0);
                            if (otherFailed > 0) {
                                warnMsg += \'• <b>\' + otherFailed + \'</b> other error(s) — neeche table dekho.<br>\';
                            }

                            resultHtml += \'<div class="alert alert-danger">\' + warnMsg + \'</div>\';
                        }

                        $("#bulkUploadResult").html(resultHtml);
                        table.ajax.reload(null, false);
                        $("#bulkUploadFile").val("");

                        if (response.failedRows && response.failedRows.length > 0) {
                            var rowsHtml = "";
                            response.failedRows.forEach(function(r) {
                                var isDuplicate = (r.reason || \'\').toLowerCase().indexOf(\'duplicate\') !== -1;
                                var rowClass = isDuplicate ? \'table-warning\' : \'\';

                                rowsHtml += "<tr class=\'" + rowClass + "\'>" +
                                    "<td>" + (r.dish_id || \'-\') + "</td>" +
                                    "<td>" + (r.dish_name || \'-\') + "</td>" +
                                    "<td>" + (r.food_group || \'-\') + "</td>" +
                                    "<td>" + (r.slot || \'-\') + "</td>" +
                                    "<td class=\'text-danger\'>" + (r.reason || \'Unknown error\') + "</td>" +
                                "</tr>";
                            });
                            $("#bulkUploadFailedBody").html(rowsHtml);
                            $("#bulkUploadFailedWrap").show();
                        } else {
                            setTimeout(function() {
                                $("#bulkUploadModal").modal("hide");
                                $("#bulkUploadResult").html("");
                                $("#bulkUploadFailedWrap").hide();
                            }, 2000);
                        }
                    } else {
                        $("#bulkUploadResult").html(\'<div class="alert alert-danger">\' + response.message + \'</div>\');
                    }
                },
                error: function(xhr) {
                    btn.prop("disabled", false).html(\'<i class="fa fa-upload"></i> Upload\');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Upload failed.";
                    $("#bulkUploadResult").html(\'<div class="alert alert-danger">\' + msg + \'</div>\');
                }
            });
        });
    });
</script>';
@endphp