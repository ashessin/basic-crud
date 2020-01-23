$(document).ready(function () {
    console.log("document ready!");

    let orderingEnabled = true;
    const pathname = window.location.pathname;
    if (pathname.endsWith("create_publications.php")){
        $("#publications").addClass('active')
        orderingEnabled = false;
    } else if (pathname.endsWith("create_users.php")) {
        $("#students").addClass('active')
        orderingEnabled = false;
    }

    $('.table').dataTable({
        // disable sorting/ordering for now...
        "ordering": orderingEnabled,
        "language": {
            "lengthMenu": "_MENU_",
            "search": ""
        }
    });
    $("#DataTables_Table_0_filter > label > input[type=search]").addClass('form-control form-inline');
    $("#DataTables_Table_0_filter > label > input[type=search]").attr("placeholder", "Search");
    $("#DataTables_Table_0_length > label > select").addClass('custom-select');

    const $tableBody = $('.table').find("tbody");
    $('#add-row').on("click", function () {
        const $trLast = $tableBody.find('tr:last');
        const $trNew = $trLast.clone();
        $trLast.after($trNew).show().find('input[required], select[required]').prop('disabled', false);
    });
});

