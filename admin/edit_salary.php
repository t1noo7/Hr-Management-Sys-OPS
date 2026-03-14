<?php

require_once('process/dbh.php');
$sql = "SELECT * FROM `salary` WHERE 1";

$result = mysqli_query($conn, $sql);
if (isset($_POST['update'])) {

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $base = mysqli_real_escape_string($conn, $_POST['base']);
    $bonus = mysqli_real_escape_string($conn, $_POST['bonus']);

    $result = mysqli_query($conn, "UPDATE `salary` SET `base`='$base',`bonus`='$bonus' WHERE id=$id");
    echo ("<SCRIPT LANGUAGE='JavaScript'>
    window.alert('Succesfully Updated')
    window.location.href='salaryemp.php';
    </SCRIPT>");



}
?>

<?php
$id = (isset($_GET['id']) ? $_GET['id'] : '');
$sql = "SELECT * from `salary` WHERE id=$id";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($res = mysqli_fetch_assoc($result)) {
        $base = $res['base'];
        $bonus = $res['bonus'];
        // $total = $employee['base'] + ($employee['base'] * $employee['bonus'] / 100);
    }
}

?>
<html>

<head>
    <title>View Employee | Admin Panel | XYZ Corporation</title>
    <!-- Icons font CSS-->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <!-- Font special for pages-->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i"
        rel="stylesheet">

    <!-- Vendor CSS-->
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/main.css" rel="stylesheet" media="all">
</head>

<body>
    <header>
        <nav>
            <h1>XYZ Corp.</h1>
            <ul id="navli">
                <li><a class="homeblack" href="index.html">HOME</a></li>
                <li><a class="homeblack" href="addemp.php">Add Employee</a></li>
                <li><a class="homered" href="viewemp.php">View Employee</a></li>
                <li><a class="homeblack" href="../elogin.html">Log Out</a></li>
            </ul>
        </nav>
    </header>

    <div class="divider"></div>

    <div class="page-wrapper bg-blue p-t-100 p-b-100 font-robo">
        <div class="wrapper wrapper--w680">
            <div class="card card-1">
                <div class="card-heading"></div>
                <div class="card-body">
                    <h2 class="title">Update Salary Info</h2>
                    <form id="update" action="edit_salary.php" method="POST">

                        <div class="row row-space">
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="number" name="base"
                                        value="<?php echo $base; ?>">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="input-group">
                                    <input class="input--style-1" type="number" name="bonus"
                                        value="<?php echo $bonus; ?>">
                                </div>
                            </div>
                        </div>


                        <!-- <div class="input-group">
                            <input class="input--style-1" type="number" name="total" value="<?php echo $total; ?>">
                        </div> -->

                        <input type="hidden" name="id" id="textField" value="<?php echo $id; ?>"
                            required="required"><br><br>
                        <div class="p-t-20">
                            <button class="btn btn--radius btn--green" type="submit" name="update">Submit</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>