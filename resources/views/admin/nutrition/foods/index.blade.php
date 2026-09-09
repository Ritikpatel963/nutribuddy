@extends('layout.layout')

@php
  $title = 'Master Food Data';
  $subTitle = 'Diet Chart / Master Food Data';
@endphp

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Master Food Data</h5>
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
            </tr>
          </thead>
          <tbody>
            <!-- Loaded via AJAX -->
          </tbody>
        </table>
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
        
        $("#foodsTable").DataTable({
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
                { data: "energy_kcal", name: "energy_kcal" }
            ],
            order: [[1, "asc"]],
            language: {
                searchPlaceholder: "Search records...",
                processing: \'<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> \'
            }
        });
    });
</script>';
@endphp
