<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location: login.php');
    exit();
}

if (isset($_GET['logout'])) {
    unset($user_id);
    session_destroy();
    header('location: login.php');
    exit();
}

// CRUD operations for university_info
if (isset($_POST['add_info'])) {
    $info_major = $_POST['info_major'];
    $info_faculty = $_POST['info_faculty'];
    $info_gender = $_POST['info_gender'];
    $info_campus = $_POST['info_campus'];
    $info_id = $_POST['info_id'];

    $uploads_dir = __DIR__ . "/uploads/";
    $id_photo_path = $uploads_dir . time() . '_' . $_FILES['id_photo']['name'];

    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0755, true);
    }

    if (move_uploaded_file($_FILES['id_photo']['tmp_name'], $id_photo_path)) {
        mysqli_query($conn, "INSERT INTO `university_info` (user_id, major, faculty, gender, campus, info_id, id_photo) VALUES ('$user_id', '$info_major', '$info_faculty', '$info_gender', '$info_campus', '$info_id', '$id_photo_path')") or die(mysqli_error($conn));
        $message[] = 'Information added successfully!';
    } else {
        $message[] = 'Failed to move uploaded file. Information not added.';
    }
}

if (isset($_POST['delete_info'])) {
    $delete_id = $_POST['info_id'];
    $id_photo_path_query = mysqli_query($conn, "SELECT id_photo FROM `university_info` WHERE id = '$delete_id' AND user_id = '$user_id'");
    $id_photo_path = mysqli_fetch_assoc($id_photo_path_query)['id_photo'];

    if (file_exists($id_photo_path)) {
        if (unlink($id_photo_path)) {
            mysqli_query($conn, "DELETE FROM `university_info` WHERE id = '$delete_id' AND user_id = '$user_id'") or die(mysqli_error($conn));
            $message[] = 'Information deleted successfully!';
        } else {
            $message[] = 'Failed to delete file. Information not deleted. Check file permissions.';
        }
    } else {
        $message[] = 'File not found. Information not deleted. Check file path.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Information</title>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<div class="message" onclick="this.remove();">' . $msg . '</div>';
    }
}
?>

<div class="container">

    <div class="user-profile">
        <?php
        $select_user = mysqli_query($conn, "SELECT * FROM `user_info` WHERE id = '$user_id'");
        if (mysqli_num_rows($select_user) > 0) {
            $fetch_user = mysqli_fetch_assoc($select_user);
        };
        ?>
        <p> username : <span><?php echo $fetch_user['name']; ?></span> </p>
        <p> email : <span><?php echo $fetch_user['email']; ?></span> </p>
        <div class="flex">
            <a href="login.php" class="btn">login</a>
            <a href="register.php" class="option-btn">register</a>
            <a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('Are you sure you want to logout?');" class="delete-btn">logout</a>
        </div>
    </div>

    <div class="info-form">
        <h2>Add Information</h2>
        <form method="post" action="" enctype="multipart/form-data">
            <label for="info_major">Major:</label>
            <input type="text" name="info_major" required>
            <label for="info_faculty">Faculty:</label>
            <input type="text" name="info_faculty" required>
            <label for="info_gender">Gender:</label>
            <select name="info_gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <label for="info_campus">Campus:</label>
            <input type="text" name="info_campus" required>
            <label for="info_id">ID:</label>
            <input type="text" name="info_id" required>
            <label for="id_photo">ID Photo:</label>
            <input type="file" name="id_photo" accept="image/*" required>
            <button type="submit" name="add_info">Add Information</button>
        </form>
    </div>

    <div class="info-list">
        <h2>University Information</h2>
        <table>
            <thead>
                <tr>
                    <th>Major</th>
                    <th>Faculty</th>
                    <th>Gender</th>
                    <th>Campus</th>
                    <th>ID</th>
                    <th>ID Photo</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_info = mysqli_query($conn, "SELECT * FROM `university_info` WHERE user_id = '$user_id'");
                while ($info = mysqli_fetch_assoc($select_info)) {
                    echo '<tr>';
                    echo '<td>' . $info['major'] . '</td>';
                    echo '<td>' . $info['faculty'] . '</td>';
                    echo '<td>' . $info['gender'] . '</td>';
                    echo '<td>' . $info['campus'] . '</td>';
                    echo '<td>' . $info['info_id'] . '</td>';
                    echo '<td><img src="' . $info['id_photo'] . '" alt="ID Photo" style="max-width: 100px; max-height: 100px;"></td>';
                    echo '<td>
                            <form method="post" action="">
                                <input type="hidden" name="info_id" value="' . $info['id'] . '">
                                <button type="submit" name="delete_info">Delete</button>
                            </form>
                            <form method="post" action="modify_info.php">
                                <input type="hidden" name="info_id" value="' . $info['id'] . '">
                                <button type="submit" name="modify_info">Modify</button>
                            </form>
                          </td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </