<?php
//AUTHORS: All
session_start();
include_once('db_connect.php');
include_once("navBar.php");
include_once("phongUtil.php");
include_once("benUtil.php");
include_once("rakoanUtil.php");
if (isset($_GET['op'])) {
  $op = $_GET['op'];
} else {
  $op = '';
}

?>

<!DOCTYPE html>
<html>

<head>
  <link rel="stylesheet" type="text/css" href="styles.css">
  <title>We Are Cooking!</title>
</head>

<body>
  <div class="wrapper">
    <main>
      <?php
      if ($op == 'main' || $op == '') {
      ?>
        <section class="hero">
          <h1>We Are Cooking!</h1>
          <p style="color: wheat">We are going to make something yummy! Give us information so that this meal will be very extra NICE!</p>
          <div class="cta">
            <button onclick='window.location.href = "?op=searchForm"'>LET'S COOK</button>
          </div>
        </section>
      <?php
      } else if ($op == 'loginForm') {
        logInForm();
      } else if ($op == 'signUp') {
        signUp();
      } else if ($op == 'processSignUp') {
        addToUser($db, $_POST);
      } else if ($op == 'processLogin') {
        logIn($db, $_POST);
      } else if ($op == 'logout') {
        // Unset individual session variables
        unset($_SESSION['userID']);
        unset($_SESSION['userName']);
        unset($_SESSION['fname']);
        unset($_SESSION['lname']);
        unset($_SESSION['email']);
        // Destroy the session
        session_destroy();
        // Regenerate session ID (optional)
        session_regenerate_id(true);
        header("Location: home.php");
        exit();
      } else if ($op == 'aboutUs') {
        aboutUs();
      } else if ($op == 'resource') {
        resource();
      } else if ($op == 'pantry') {
        pantryForm($db);
      } else if ($op == 'processPantry') {
        processPantry($db, $_POST);
      } else if ($op == 'removeFromPantry') {
        removeFromPantryForm($db);
      } else if ($op == 'processRemoveFromPantry') {
        processRemoveFromPantry($db, $_POST);
      } else if ($op == 'searchForm') {
        ben_genSearchForm($db, $_SESSION['userID']);
      } else if ($op == 'popular') {
        ben_genPopularTable($db);
      } else if ($op == 'history') {
        showHistory($_SESSION['userID'], $db);
      } else if ($op == 'settings') {
        showSettings($_SESSION['userID'], $db);
      } else if ($op == 'preference') {
        preferenceForm($db);
      } else if ($op == 'processPreference') {
        processPreference($db, $_POST);
      } else if ($op == 'removeFromAllergen') {
        removeFromPreference($db);
      } else if ($op == 'processRemoveFromPreference') {
        processRemoveFromPreference($db, $_POST);
      } else if ($op == 'search') {
        //runs sql search and$sql = "SELECT * FROM rating WHERE mealID=$mealID and userID=$uid";
    // $res = $db->query($sql);
    // if ($res->fetch() == TRUE) {
    //     $sql = "UPDATE rating
    //                 SET date=NOW()
    //                 WHERE userID=$uid and mealID=$mealID";
    //     $res = $db->query($sql);
    //     if ($res == FALSE) {
    //         "<H1> ERROR: Meal History not updated</H1>";
    //     }
    // } else {

    // }creates a table with results
        ben_search($db, $_POST);
      } else if ($op == 'changePw') {
        changePassword($_POST['newPwOne'], $db);
      } else if ($op == 'changeName') {
        changeName($_POST, $db);
      } else if ($op == 'showMeal') {
        showMeal($_GET['mealID'], $db);
      } else if ($op == 'rateMealForm') {
        showRateForm($_GET['mealID'], $db);
      } else if ($op == 'rateMeal') {
        rateMeal($_POST, $db);
      }

      ?>
    </main>
  </div>
</body>
<footer>
  <p class="copyright">&copy; 2023 We Are Cooking!</p>
</footer>

</html>