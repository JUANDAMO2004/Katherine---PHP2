<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location: login.php');
    exit();
}

if (isset($_POST['modify_info'])) {
    $modify_id = $_POST['info_id'];

    // Fetch the existing information for the selected ID
    $select_info_query = mysqli_query($conn, "SELECT * FROM `university_info` WHERE id = '$modify_id' AND user_id = '$user_id'");
    $info = mysqli_fetch_assoc($select_info_query);

    // Debugging: Print the values for debugging
    echo "Modify ID: $modify_id<br>";

    // Add this line to print the image path
    echo "Image Path: " . $info['id_photo'] . "<br>";

    if (file_exists($info['id_photo'])) {
        echo "File exists: Yes<br>";

        // Attempt to delete the file
        if (unlink($info['id_photo'])) {
            echo "File deletion successful<br>";
        } else {
            echo "File deletion failed<br>";
        }
    } else {
        echo "File exists: No<br>";
    }

    // Display a form to modify the information
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modify Information</title>

        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>

    <div class="container">

        <div class="info-form">
            <h2>Modify Information</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="modify_id" value="<?php echo $modify_id; ?>">
                <label for="info_major">Major:</label>
                <input type="text" name="info_major" value="<?php echo $info['major']; ?>" required>
                <label for="info_faculty">Faculty:</label>
                <input type="text" name="info_faculty" value="<?php echo $info['faculty']; ?>" required>
                <label for="info_gender">Gender:</label>
                <select name="info_gender" required>
                    <option value="Male" <?php echo ($info['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($info['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($info['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
                <label for="info_campus">Campus:</label>
                <input type="text" name="info_campus" value="<?php echo $info['campus']; ?>" required>
                <label for="info_id">ID:</label>
                <input type="text" name="info_id" value="<?php echo $info['info_id']; ?>" required>
                <label for="id_photo">ID Photo:</label>
                <input type="file" name="id_photo" accept="image/*">
                <button type="submit" name="update_info">Update Information</button>
            </form>
        </div>

    </div>

    </body>
    </html>
    <?php
} elseif (isset($_POST['update_info'])) {
    $modify_id = $_POST['modify_id'];
    $info_major = $_POST['info_major'];
    $info_faculty = $_POST['info_faculty'];
    $info_gender = $_POST['info_gender'];
    $info_campus = $_POST['info_campus'];
    $info_id = $_POST['info_id'];

    // Check if a new ID photo is uploaded
    if (!empty($_FILES['id_photo']['name'])) {
        $id_photo_name = time() . '_' . $_FILES['id_photo']['name'];
        $id_photo_tmp = $_FILES['id_photo']['tmp_name'];
        $id_photo_type = $_FILES['id_photo']['type'];

        $uploads_dir = __DIR__ . "/uploads/";
        $id_photo_path = $uploads_dir . $id_photo_name;

        if (move_uploaded_file($id_photo_tmp, $id_photo_path)) {
            // Delete the old ID photo file
            $old_id_photo_path_query = mysqli_query($conn, "SELECT id_photo FROM `university_info` WHERE id = '$modify_id' AND user_id = '$user_id'");
            $old_id_photo_path = mysqli_fetch_assoc($old_id_photo_path_query)['id_photo'];
            if (file_exists($old_id_photo_path)) {
                unlink($old_id_photo_path);
            }

            // Update information with the new ID photo
            mysqli_query($conn, "UPDATE `university_info` SET major = '$info_major', faculty = '$info_faculty', gender = '$info_gender', campus = '$info_campus', info_id = '$info_id', id_photo = '$id_photo_path' WHERE id = '$modify_id' AND user_id = '$user_id'") or die('query failed');
        }
    } else {
        // Update information without changing the ID photo
        mysqli_query($conn, "UPDATE `university_info` SET major = '$info_major', faculty = '$info_faculty', gender = '$info_gender', campus = '$info_campus', info_id = '$info_id' WHERE id = '$modify_id' AND user_id = '$user_id'") or die('query failed');
    }

    header('location: index.php');
    exit();
}
?>