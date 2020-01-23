<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="CRUD - Basic CRUD Application.">
    <meta name="author" content="Ashesh Kumar Singh (asing80@uic.edu)">
    <title>CRUD</title>
    <!-- Twitter Bootstrap Core CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Theme CSS -->
    <link href="node_modules/designmodo-flat-ui-free/dist/css/flat-ui.min.css" rel="stylesheet">
    <!-- Datatables CSS-->
    <link href="node_modules/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Main CSS -->
    <link href="public/css/main.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <?php include 'public/nav.php' ?>
    <?php include 'public/setup.php'; ?>
    <table class="table table-bordered table-striped table-highlight">
        <thead>
        <tr>
            <th scope="col">Publication ID</th>
            <th scope="col">Email</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Title</th>
            <th scope="col">Year</th>
        </tr>
        </thead>
        <?php
        $sql = "SELECT p.id, u.email, u.first_name, u.last_name, p.title, p.year FROM `publications` p INNER JOIN `users` u on p.student_id = u.id";
        $select_publications_sqlresult = mysqli_query($connection, $sql);
        if (!$select_publications_sqlresult) {
            die("SELECT TABLE `crud`.`publications`., failed: " . mysqli_error($connection));
        }

        $sql = "SELECT * FROM users";
        $select_users_sqlresult = mysqli_query($connection, $sql);
        if (!$select_users_sqlresult) {
            die("SELECT TABLE `crud`.`users`., failed: " . mysqli_error($connection));
        }

        $message = ".";
        if (mysqli_num_rows($select_users_sqlresult) == 0) {
            $message = "You have no users, redirecting to create_users.php...";
            header("refresh:2;url=create_users.php");
        } elseif (mysqli_num_rows($select_publications_sqlresult) == 0) {
            $message = "You have no publications, redirecting to create_publications.php...";
            header("refresh:2;url=create_publications.php");
        } else {
            while ($row = mysqli_fetch_assoc($select_publications_sqlresult)) {
                echo "<tr>";
                foreach ($row as $field => $value) {
                    echo "<td>" . $value . "</td>";
                }
                echo "</tr>";
            }
        }
        ?>
    </table>
    <div class="mt-5 alert alert-warning alert-dismissible fade show" role="alert">
        <?php echo $message ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<!-- jQuery -->
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<!-- Twitter Bootstrap Core JS -->
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Theme JS -->
<script src="node_modules/designmodo-flat-ui-free/dist/scripts/flat-ui.min.js"></script>
<!-- Datatables JS -->
<script src="node_modules/datatables/media/js/jquery.dataTables.min.js"></script>
<!-- Main JS -->
<script src="public/js/main.js"></script>
</body>
</html>
