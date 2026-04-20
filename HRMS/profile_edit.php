<?php
include("connection.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid customer id");
}

$query = "SELECT * FROM customers WHERE id='$id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
if (!$row) {
    die("Customer not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="app_theme.css?v=<?php echo time(); ?>">
    <style>
        .page-header { background: rgba(255,255,255,0.58); backdrop-filter: blur(20px) saturate(160%); padding: 20px; color: var(--app-blue); text-align: center; position: relative; border-bottom: 1px solid rgba(36,75,138,0.1); }
        .page-header a { color: var(--app-blue); text-decoration: none; position: absolute; left: 20px; top: 20px; font-weight: 700; }
        form { max-width: 680px; margin: 34px auto; padding: 30px; background: var(--app-glass); border: 1px solid var(--app-glass-border); box-shadow: 0 12px 32px rgba(18,34,61,0.08); border-radius: 18px; }
        label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--app-blue); }
        input { width: 100%; padding: 12px; margin-bottom: 18px; border: 1px solid rgba(36,75,138,0.2); border-radius: 10px; box-sizing: border-box; font-size: 15px; font-family: var(--app-font); }
        input[type="submit"] { background: var(--app-blue); color: white; border: none; cursor: pointer; font-size: 15px; font-weight: 700; margin-top: 10px; transition: background 0.2s; }
        input[type="submit"]:hover { background: #1f3f75; }
    </style>
</head>
<body>

<div class="page-header">
    <a href="profile.php?id=<?= $id ?>">&larr; Back to Profile</a>
    <h2 style="margin: 0;">Edit Profile</h2>
</div>

<form action="profile_edit_process.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $id ?>">

    <label for="cPhoto">Profile Photo (Leave blank to keep current)</label>
    <input type="file" id="cPhoto" name="cPhoto" accept="image/*">

    <label for="full_name">Full Name</label>
    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($row['full_name']) ?>" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>

    <label for="phone">Phone Number</label>
    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($row['mobile_number']) ?>" required>

    <label for="location">Location</label>
    <input type="text" id="location" name="location" value="<?= htmlspecialchars($row['location']) ?>" required>

    <input type="submit" value="Save Changes">
</form>

</body>
</html>
