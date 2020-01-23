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
        $delete_publication = $_POST['delete_publication'];
        if (empty($delete_publication)) {
            $updated_publication_ids = $_POST['id'];
        } else {
            $updated_publication_ids = array_diff($_POST['id'], $delete_publication);
            foreach ($delete_publication as $val) {
                $sql = "DELETE FROM `publications` WHERE `id` = $val";
                $delete_publication_sqlresult = mysqli_query($connection, $sql);
                if (!$delete_publication_sqlresult) {
                    die("DELETE TABLE `crud`.`publications`., failed: " . mysqli_error($connection));
                }
                // must be 1 in each loop
                $delete_count += mysqli_affected_rows($connection);
            }
        }
        $message .= "You have deleted " . $delete_count . " row(s), ";

        // By default assume that all rows were updated
        $publication_titles = $_POST['title'];
        $publication_years = $_POST['year'];
        $publication_ids = $_POST['id'];
        $user_emails = $_POST['email'];

        // if the user sends two entries for the same publication id, only the last insert/update will reflect.
        foreach ($updated_publication_ids as $i => $updated_publication_id) {
            $sql = "INSERT INTO `publications`(`id`, `student_id`, `title`, `year`)"
                . "VALUES ($publication_ids[$i], (SELECT id FROM users WHERE email='$user_emails[$i]'),'$publication_titles[$i]', $publication_years[$i])"
                . "ON DUPLICATE KEY UPDATE"
                . "`id`= $updated_publication_id, `student_id`=(SELECT id FROM users WHERE email='$user_emails[$i]'), title = '$publication_titles[$i]', `year` = $publication_years[$i]";

            $upsert_publication_sqlresult = mysqli_query($connection, $sql);
            if (!$upsert_publication_sqlresult) {
                die("UPDATE/INSERT TABLE `crud`.`publications`., failed: " . mysqli_error($connection));
            }
            // must be 1 in each loop
            $modify_count += mysqli_affected_rows($connection);
        }
        $message .= "modified (insert+update) " . $modify_count . " row(s).";
    }

    $sql = "SELECT p.id, u.email, p.title, p.year FROM `publications` p INNER JOIN `users` u on p.student_id = u.id";
    $select_publications_sqlresult = mysqli_query($connection, $sql);
    if (!$select_publications_sqlresult) {
        die("SELECT TABLE `crud`.`publications`., failed: " . mysqli_error($connection));
    }

    $sql = "SELECT email FROM users";
    $select_users_sqlresult = mysqli_query($connection, $sql);
    if (!$select_users_sqlresult) {
        die("SELECT TABLE `crud`.`users`., failed: " . mysqli_error($connection));
    }
    ?>

    <form class="form-horizontal" action="create_publications.php" method="POST">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-highlight">
                <thead>
                <tr>
                    <th scope="col">DELETE</th>
                    <th scope="col">Publication ID</th>
                    <th scope="col">Email</th>
                    <th scope="col">Title</th>
                    <th scope="col">Year</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $emails = array();
                while ($row = mysqli_fetch_array($select_users_sqlresult)) {
                    $emails[] = $row['email'];
                }
                sort($emails);

                if (mysqli_num_rows($select_users_sqlresult) == 0) {
                    $message = "You have no users, redirecting to create_users.php...";
                    header("refresh:2;url=create_users.php");
                }

                while ($row = mysqli_fetch_assoc($select_publications_sqlresult)) { ?>
                    <tr>
                        <td>
                            <label class="checkbox">
                                <input type="checkbox" data-toggle="checkbox" class="custom-checkbox"
                                       name="delete_publication[]" value="<?= $row['id'] ?>"/>
                                <span class="icons"><span class="icon-unchecked"></span><span
                                            class="icon-checked"></span></span>
                            </label>
                        </td>
                        <td data-search="<?= $row['id'] ?>">
                            <input required readonly type="number" name="id[]" class="form-control"
                                   value="<?= $row['id'] ?>"/></td>
                        <td data-search="<?= $row['email'] ?>">
                            <select required name="email[]" class="form-control custom-select">
                                <option selected value="<?= $row['email'] ?>"><?= $row['email'] ?></option>
                                <?php
                                foreach ($emails as $key => $email) {
                                    echo '<option value="' . $email . '"/>' . $email . '</option>';
                                } ?>
                            </select>
                        </td>
                        <td data-search="<?= $row['title'] ?>">
                            <input required type="text" name="title[]" class="form-control"
                                   value="<?= $row['title'] ?>"/>
                        </td>
                        <td data-search="<?= $row['year'] ?>">
                            <input type="number" name="year[]" min="1900" max="2020" step="1" class="form-control"
                                   value="<?= $row['year'] ?>"/></td>
                    </tr>
                    <?php
                } ?>
                <tr class="new-entry">
                    <td>
                        <label class="checkbox">
                            <input disabled type="checkbox" value="" data-toggle="checkbox" class="custom-checkbox"
                                   name="delete_publication[]"/>
                            <span class="icons"><span class="icon-unchecked"></span><span
                                        class="icon-checked"></span></span>
                        </label>
                    </td>
                    <td><input required disabled type="number" name="id[]" class="form-control" value=""/></td>
                    <td><select required disabled name="email[]" class="form-control custom-select">
                            <option hidden disabled selected value></option>
                            <?php
                            foreach ($emails as $key => $email) {
                                echo '<option value="' . $email . '"/>' . $email . '</option>';
                            } ?>
                        </select>
                    </td>
                    <td><input required disabled type="text" name="title[]" class="form-control" value=""/></td>
                    <td><input required disabled type="number" name="year[]" min="1900" max="2020" step="1"
                               class="form-control"
                               value=""/></td>
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
