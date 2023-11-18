document.addEventListener("DOMContentLoaded", function () {
  var searchInput = document.getElementById("searchInput");
  var tableRows = document.querySelectorAll("#appointments-table tbody tr");

  searchInput.addEventListener("input", function () {
    var searchTerm = searchInput.value.toLowerCase();

    tableRows.forEach(function (row) {
      var rowData = row.innerText.toLowerCase();

      // Show/hide rows based on search term
      row.style.display = rowData.includes(searchTerm) ? "table-row" : "none";
    });
  });
});

jQuery(document).ready(function ($) {
  // DataTables initialization with search on specific columns
  var table = $("#employee-appoiment-table").DataTable();
  // Handle status filter change
  $("#status-filter").on("change", function () {
    var status = $(this).val();
    table.column(5).search(status).draw(); // Assuming the status column is at index 5
  });

  table.DataTable({
    searchCols: [null, null, null, null, null, null], // Enable searching on all columns
  });
});
