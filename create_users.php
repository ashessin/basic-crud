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
    <?php include 'public/setup.php';

    $message = "No operations performed";
    $delete_count = 0;
    $modify_count = 0;

    if (isset($_POST['submit'])) {
        $message = "";
        $delete_user = $_POST['delete_user'];
        if (empty($delete_user)) {
            $updated_user_ids = $_POST['id'];
        } else {
            $updated_user_ids = array_diff($_POST['id'], $delete_user);
            foreach ($delete_user as $val) {
                $sql = "DELETE FROM `users` WHERE id=$val";
                $delete_user_sqlresult = mysqli_query($connection, $sql);
                if (!$delete_user_sqlresult) {
                    die("DELETE TABLE `crud`.`users`., failed: " . mysqli_error($connection));
                }
                // must be 1 in each loop
                $delete_count += mysqli_affected_rows($connection);
            }
        }
        $message .= "You have deleted " . $delete_count . " row(s), ";

        // By default assume that all rows were updated
        $user_first_names = $_POST['first_name'];
        $user_last_names = $_POST['last_name'];
        $user_ids = $_POST['id'];
        $user_emails = $_POST['email'];

        // if the user sends two entries for the same publication id, only the last insert/update will reflect.
        foreach ($updated_user_ids as $i => $updated_user_id) {
            $sql = "INSERT INTO `users`(`id`, `email`, `first_name`, `last_name`)"
                . "VALUES ($user_ids[$i], '$user_emails[$i]', '$user_first_names[$i]', '$user_last_names[$i]')"
                . "ON DUPLICATE KEY UPDATE"
                . "`id`= $updated_user_id, `email` = '$user_emails[$i]', `first_name` = '$user_first_names[$i]', `last_name` = '$user_last_names[$i]'";

            $upsert_publication_sqlresult = mysqli_query($connection, $sql);
            if (!$upsert_publication_sqlresult) {
                die("UPDATE/INSERT TABLE `crud`.`users`., failed: " . mysqli_error($connection));
            }
            // must be 1 in each loop
            $modify_count += mysqli_affected_rows($connection);
        }
        $message .= "modified (insert+updated) " . $modify_count . " row(s).";
    }

    $sql = "SELECT * FROM users";
    $select_users_sqlresult = mysqli_query($connection, $sql);
    if (!$select_users_sqlresult) {
        die("SELECT TABLE `crud`.`users`., failed: " . mysqli_error($connection));
    }
    ?>

    <form class="form-horizontal" action="create_users.php" method="POST">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-highlight">
                <thead>
                <tr>
                    <th scope="col">DELETE</th>
                    <th scope="col">User ID</th>
                    <th scope="col">Email</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($select_users_sqlresult)) { ?>
                    <tr>
                    <td>
                        <label class="checkbox">
                            <input type="checkbox" data-toggle="checkbox" class="custom-checkbox"
                                   name="delete_user[]" value="<?= $row['id'] ?>"/>
                            <span class="icons"><span class="icon-unchecked"></span><span
                                        class="icon-checked"></span></span>
                        </label>
                    </td>
                    <td data-search="<?= $row['id'] ?>">
                        <input required readonly type="number" name="id[]" class="form-control"
                               value="<?= $row['id'] ?>"/>
                    </td>
                    <td data-search="<?= $row['email'] ?>">
                        <input required type="email" name="email[]" class="form-control" value="<?= $row['email'] ?>"/>
                    </td>
                    </td>
                    <td data-search="<?= $row['first_name'] ?>">
                        <input required type="text" name="first_name[]" class="form-control"
                               value="<?= $row['first_name'] ?>"/></td>
                    <td data-search="<?= $row['last_name'] ?>">
                        <input required type="text" name="last_name[]" class="form-control"
                               value="<?= $row['last_name'] ?>"/></td>
                    </tr><?php
                } ?>
                <tr class="new-entry">
                    <td>
                        <label class="checkbox">
                            <input disabled type="checkbox" value="" data-toggle="checkbox" class="custom-checkbox"
                                   name="delete_user[]"/>
                            <span class="icons"><span class="icon-unchecked"></span><span
                                        class="icon-checked"></span></span>
                        </label>
                    </td>
                    <td><input required disabled type="number" name="id[]" class="form-control" value=""/></td>
                    <td><input required disabled type="email" name="email[]" class="form-control" value=""/></td>
                    <td><input required disabled type="text" name="first_name[]" class="form-control" value=""/></td>
                    <td><input required disabled type="text" name="last_name[]" class="form-control" value=""/></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-5 alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $message ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="row mt-5">
            <div class="col">
                <a href="#fake-link" id="add-row" class="btn btn-secondary btn-embossed btn-info"><span
                            class="fui-plus"></span> Add Row</a>
            </div>
            <div class="col">
                <button class="btn btn-block btn-embossed btn-primary" type="submit" name="submit"><span
                            class="fui-check"></span> Save
                </button>
            </div>
        </div>
    </form>
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