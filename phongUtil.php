<?php

// Philip Pham

session_start();
include_once('db_connect.php');

function logInForm()
{
    ?>
    <h2>Login</h2>
    <form id='loginForm' method='POST' action='?op=processLogin'>
        <label class='login' for="userName">User name</label>
        <input type='text' name='userName' required><br>
        <label class='login' for="password">Password</label>
        <input type='text' name='password' required><br>
        <input type='submit' value='Login' />
    </form>
    <p>Don't have an account? <a href="?op=signUp">Sign up now</a></p>

    <?php
}

function logIn($db, $post_data)
{
    // Handle login form submission
    if (
        isset($post_data['userName'])
        && isset($post_data['password'])
    ) {
        // Process the user's input and create an account
        $uname = $post_data['userName'];
        $pwd = $post_data['password'];

        $sql = "SELECT * FROM account WHERE userName = '$uname' AND password = '$pwd'";
        $res = $db->query($sql);
        if ($res != false) {
            $row = $res->fetch();
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['userName'] = $row['userName'];
            $_SESSION['fname'] = $row['fname'];
            $_SESSION['lname'] = $row['lname'];
            $_SESSION['email'] = $row['email'];
            header("refresh:3;url=?op=main");
            echo "<H3>Welcome " . $_SESSION['userName'] . "!</H3>";
        } else {
            header("refresh:3;url=?op=loginForm");
            echo "<h3>Error logging in<h3>";
        }

    }

}

function signUp()
{
    // Display the sign-up form
    ?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>Sign Up</title>
    </head>

    <body>
        <h2>Sign Up</h2>
        <form name='fmSignup' method='POST' action='?op=processSignUp'>
            <label for="userName" style="color: #e8e1c7;">User name</label>
            <input type='text' name='userName' required><br> <br>
            <label for="password" style="color: #e8e1c7;">Password</label>
            <input type='text' name='password' required><br> <br>
            <label for="fname" style="color: #e8e1c7;">First Name</label>
            <input type='text' name='fname' required><br><br>
            <label for="lname" style="color: #e8e1c7;">Last Name</label>
            <input type='text' name='lname' required><br><br>
            <label for="mail" style="color: #e8e1c7;">Email</label>
            <input type='text' name='mail'><br><br>
            <input type='submit' value='Sign Up' />
        </form>
    </body>

    </html>
    <?php
}

function addToUser($db, $post_data)
{
    // Handle sign-up form submission
    if (
        isset($post_data['userName'])
        && isset($post_data['password'])
    ) {
        // Process the user's input and create an account
        $uname = $post_data['userName'];
        $pwd = $post_data['password'];
        $fname = $post_data['fname'];
        $lname = $post_data['lname'];
        $email = $post_data['mail'];

        // Check if the username already exists
        $checkUname = "SELECT COUNT(*) as count FROM account WHERE userName = '$uname'";
        $resCheck = $db->query($checkUname);
        $row = $resCheck->fetch();
        if ($row['count'] > 0) {
            echo "<h3>Username '$uname' already exists. Please choose another username.</h3>";
            header("refresh:3;url=?op=signUp");
            exit();
        }
        $sql = "INSERT INTO account (userName, password, fname, lname, email) VALUES ('$uname', '$pwd', '$fname', '$lname', '$email')";
        $res = $db->query($sql);
        if ($res != false) {
            header("refresh:2;url=?op=main");
            echo "<H3>Welcome new user " . $fname . "!.</H3>";
        } else {
            header("refresh:2;url=?op=signUp");
            echo "<h3>Error creating new user<h3>";
        }

    }
}

function aboutUs()
{
    echo "<h2>About Us</h2>";

    // Brief introduction
    echo "<p>Welcome to our project! We are a team of passionate individuals who love to create amazing things.</p>";

    // Information about team members
    echo "<h3>Team Members</h3>";

     // Member 1
     echo "<div>";
     echo "<h4>Benedict DelBaggio</h4>";
     echo "<p>Class Year: 2024</p>";
     echo "<p>Hobbies: Video Games, Reading, and Tennis.</p>";
     echo "<p>Email: delbbe01@gettyburg.edu</p>";
     echo "</div>";
 
     // Member 2
     echo "<div>";
     echo "<h4>Andry A. Rakotonjanabelo</h4>";
     echo "<p>Class Year: 2025</p>";
     echo "<p>Hobbies: Eating, Cooking, Games, and Sports in general.</p>";
     echo "<p>Email: rakoan02@gettysburg.edu</p>";
     echo "</div>";
 
     // Member 3
     echo "<div>";
     echo "<h4>Phong Pham</h4>";
     echo "<p>Class Year: 2025</p>";
     echo "<p>Hobbies: Basketball, discovering Viet indie music.</p>";
     echo "<p>Email: phamph01@gettysburg.edu</p>";
     echo "</div>";
}

function resource()
{

    ?>
    <h2>Resources</h2>

    <!-- Where to buy ingredients -->
    <h3>Where to Buy Ingredients</h3>
    <ul>
        <li><a href='https://www.amazon.com' target='_blank'>Amazon</a></li>
        <li><a href='https://www.sayweee.com' target='_blank'>Weee!</a></li>
    </ul>

    <!-- Cooking channels to follow -->
    <h3>Cooking Channels to Follow</h3>
    <ul>
        <li><a href='https://www.youtube.com/@foodwishes' target='_blank'>Food Wishes</a></li>
        <li><a href='https://www.youtube.com/@buzzfeedtasty' target='_blank'>Tasty</a></li>
        <li><a href='https://www.youtube.com/@JoshuaWeissman' target='_blank'>Joshua Weissman</a></li>
        <li><a href='https://www.youtube.com/@babishculinaryuniverse' target='_blank'>Babish Culinary Universe</a></li>
        <li><a href='https://www.youtube.com/@TheKoreanVegan' target='_blank'>The Korean Vegan</a></li>

        <!-- Add more channels as needed -->
    </ul>
    <?php
}

function pantryForm($db)
{
    // Display the form with checkboxes for ingredients
    echo "<h2>Add to pantry</h2>";

    // Fetch ingredients from the "ingredient" table
    $sql = "SELECT i.ingID, i.name
            FROM ingredient i
            LEFT JOIN have h ON i.ingID = h.iid AND h.uid = {$_SESSION['userID']}
            WHERE h.iid IS NULL";
    $result = $db->query($sql);

    if ($result->rowCount() > 0) {
        echo "<form method='POST' action='?op=processPantry'>";
        echo "<div class='ingredient-container'>";
        echo "<label class='ingredient-checkbox'>";
        echo "<input type='checkbox' id='select-all'> Select All";
        echo "</label>";
        while ($row = $result->fetch()) {
            $iid = $row['ingID'];
            $ingredientName = $row['name'];

            // Display checkbox for each ingredient
            echo "<label class='ingredient-checkbox'>";
            echo "<input type='checkbox' name='ingredients[]' value='$iid'> $ingredientName";
            echo "</label><br>";
        }
        echo "</div>";
        ?>
        <script>

            document.getElementById('select-all').addEventListener('change', function () {
                var checkboxes = document.getElementsByName('ingredients[]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
            });
        </script>
        <br><input type='submit' value='Add to pantry'>
        <br>
        </form>

        <?php
    } else {
        echo "<p>No ingredients available.</p>";
    }
    ?>
    <form method='POST' action='?op=removeFromPantry'>
        <p class='idk'> Doesn't have certain ingredients anymore? Remove it here! ->
            <input type='submit' value='Remove from pantry'>
    </form>
    <?php
}

function processPantry($db, $post_data)
{
    if (isset($post_data['ingredients'])) {
        $userID = $_SESSION['userID'];
        $ingredients = $post_data['ingredients'];

        // Loop through selected ingredients and insert into the "have" table
        foreach ($ingredients as $ingredientID) {
            $sql = "INSERT INTO have (uid, iid) VALUES ('$userID', '$ingredientID')";
            $result = $db->query($sql);
            if ($result == false) {
                echo "<h3> Failed to add to your pantry.</h3>";
            }
        }
        header("refresh:2;url=dashboard.php");
        echo "<h3>Successfully add to your pantry</h3>";
        exit();
    }
}

function removeFromPantryForm($db)
{
    // Display the form with checkboxes for ingredients to remove from the pantry
    echo "<h2>Remove from Pantry</h2>";

    // Fetch ingredients that the user has in their pantry
    $userID = $_SESSION['userID'];
    $sql = "SELECT h.iid, i.name
            FROM have h
            JOIN ingredient i ON h.iid = i.ingID
            WHERE h.uid = $userID";
    $res = $db->query($sql);

    // Display checkboxes for ingredients to remove from the pantry
    if ($res->rowCount() > 0) {
        echo "<form method='POST' action='?op=processRemoveFromPantry'>";
        echo "<div class='ingredient-container'>";
        echo "<label class='ingredient-checkbox'>";
        echo "<input type='checkbox' id='select-all'> Select All";
        echo "</label>";
        while ($row = $res->fetch()) {
            $iid = $row['iid'];
            $ingredientName = $row['name'];
            echo "<label class='ingredient-checkbox'>";
            echo "<input type='checkbox' name='ingredients[]' value='$iid'>$ingredientName";
            echo "</label>";
        }

        echo "</div>";
        ?>
        <script>

            document.getElementById('select-all').addEventListener('change', function () {
                var checkboxes = document.getElementsByName('ingredients[]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
            });
        </script>
        <br><input type='submit' value='Remove from Pantry'>
        </form>

        <?php
    } else {
        echo "<p>No ingredients in your pantry.</p>";
    }
    ?>
    <form method='POST' action='?op=pantry'>
        <p> Want to add certain ingredients? Add it here! ->
            <input type='submit' value='Add to Pantry'>
    </form>
    <?php
}

function processRemoveFromPantry($db, $post_data)
{
    if (isset($post_data['ingredients'])) {
        $userID = $_SESSION['userID'];
        $ingredients = $post_data['ingredients'];

        // Loop through selected ingredients and remove them from the "have" table
        foreach ($ingredients as $ingredientID) {
            $sql = "DELETE FROM have WHERE uid = $userID AND iid = $ingredientID";
            $res = $db->query($sql);
            if ($res == false) {
                echo "<h3> Failed to remove from your pantry.</h3>";
            }
        }

        // Redirect or display a success message
        header("refresh:2;url=dashboard.php");
        echo "<h3>Successfully remove from your pantry</h3>";
        exit();
    }
}

function isAdmin($db, $userID)
{
    $sql = "SELECT * FROM admin";
    $res = $db->query($sql);

    return true;
}

function getRandomMeal($db)
{
    $sql = "SELECT *   
            FROM meal m
            ORDER BY RAND()
            LIMIT 1";
    $res = $db->query($sql);
    $row = $res->fetch();
    return $row;
}

function getUserPantryIngredients($db, $userID)
{
    $sql = "SELECT h.iid, i.name
            FROM have h
            JOIN ingredient i ON h.iid = i.ingID
            WHERE h.uid = $userID";
    $res = $db->query($sql);
    $row = $res->fetchAll();
    return $row;
}

function getRandomRecipe($db, $pantryIngredients)
{
    // Create a WHERE clause to filter meals based on pantry ingredients
    $whereClause = '';
    foreach ($pantryIngredients as $ingredient) {
        $whereClause .= "AND EXISTS (
                            SELECT 1 
                            FROM MI 
                            WHERE MI.mealID = m.mealID  
                            OR ingID = {$ingredient['iid']}
                        ) ";
    }

    // Fetch a random meal that matches user's pantry ingredients with at least 50% match
    $sql = "SELECT m.*, 
                   COALESCE((SELECT COUNT(DISTINCT MI.ingID)
                             FROM MI
                             WHERE MI.mealID = m.mealID
                             AND ingID IN (" . implode(",", array_column($pantryIngredients, 'iid')) . ")
                            ), 0) AS matchCount
            FROM meal m
            WHERE 1 $whereClause
            ORDER BY RAND()
            LIMIT 1";

    $res = $db->query($sql);

    if (!$res) {
        echo "Error executing the query: " . $db->errorInfo()[2];
    } elseif ($res->rowCount() == 0) {
        echo "No matching meals found.";
    } else {
        return $res->fetch();
    }
}




// Function to display the recommended recipe box
function displayRecommendedRecipeBox($db)
{
    $userID = $_SESSION['userID'];
    $pantryIngredients = getUserPantryIngredients($db, $userID);


    // If the user has ingredients in their pantry
    if (!empty($pantryIngredients)) {
        $recommendedRecipe = getRandomRecipe($db, $pantryIngredients);
        if ($recommendedRecipe) {
            $id = $recommendedRecipe['mealID'];
            echo "<div class='recommended-box'>";
            echo "Recommended recipe: <a href='home.php?op=showMeal&mealID=$id'> {$recommendedRecipe['name']} </a>";
            echo "<br></br>";
            echo $recommendedRecipe['description'];
            echo "</div>";
        } else {
            $random = getRandomMeal($db);
            $id = $random['mealID'];
            // Display a random recipe if no matching recipes found
            echo "<div class='recommended-box'>";
            echo "Recommended Recipe: <a href='home.php?op=showMeal&mealID=$id'>{$random['name']}</a>";
            echo "<br></br>";
            echo $random['description'];
            echo "</div>";
        }
    } else {
        echo "<div class='recommended-box'>";
        echo "Add ingredients to your pantry to get personalized recipe recommendations.";
        echo "</div>";
    }
}

function preferenceForm($db)
{
    // Display the form with checkboxes for ingredients
    echo "<h2>Modify your allergens</h2>";

    // Fetch ingredients from the "ingredient" table
    $sql = "SELECT a.aid, a.name
            FROM allergen a
            LEFT JOIN AA ON a.aid = AA.aid AND AA.userID = {$_SESSION['userID']}
            WHERE AA.aid IS NULL";
    $result = $db->query($sql);

    if ($result->rowCount() > 0) {
        echo "<form method='POST' action='?op=processPreference'>";
        echo "<div class='ingredient-container'>";
        echo "<label class='ingredient-checkbox'>";
        echo "<input type='checkbox' id='select-all'> Select All";
        echo "</label>";
        while ($row = $result->fetch()) {
            $aid = $row['aid'];
            $allergeName = $row['name'];

            // Display checkbox for each ingredient
            echo "<label class='ingredient-checkbox'>";
            echo "<input type='checkbox' name='allergy[]' value='$aid'> $allergeName";
            echo "</label><br>";
        }
        echo "</div>";
        ?>
        <script>

            document.getElementById('select-all').addEventListener('change', function () {
                var checkboxes = document.getElementsByName('allergy[]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
            });
        </script>
        <br><input type='submit' value='Add to allergen'>
        <br>
        </form>

        <?php
    } else {
        echo "<p>No allergens available.</p>";
    }
    ?>
    <form method='POST' action='?op=removeFromAllergen'>
        <p class='idk'> Doesn't have certain allergy anymore? Remove it here! ->
            <input type='submit' value='Remove allergens'>
    </form>
    <?php
}

function processPreference($db, $post_data)
{
    if (isset($post_data['allergy'])) {
        $userID = $_SESSION['userID'];
        $allergens = $post_data['allergy'];

        // Loop through selected ingredients and insert into the "have" table
        foreach ($allergens as $aid) {
            $sql = "INSERT INTO AA (userID, aid) VALUES ('$userID', '$aid')";
            $result = $db->query($sql);
            if ($result == false) {
                echo "<h3> Failed to add to your allergens.</h3>";
            }
        }
        header("refresh:2;url=dashboard.php");
        echo "<h3>Successfully add to your allergens</h3>";
        exit();
    }
}

function removeFromPreference($db)
{
    // Display the form with checkboxes for ingredients to remove from the pantry
    echo "<h2>Remove from allergens</h2>";

    // Fetch ingredients that the user has in their pantry
    $userID = $_SESSION['userID'];
    $sql = "SELECT AA.aid, a.name
            FROM AA
            JOIN allergen a ON AA.aid = a.aid
            WHERE AA.userID = $userID";
    $res = $db->query($sql);

    // Display checkboxes for ingredients to remove from the pantry
    if ($res->rowCount() > 0) {
        echo "<form method='POST' action='?op=processRemoveFromPreference'>";
        echo "<div class='ingredient-container'>";
        echo "<label class='ingredient-checkbox'>";
        echo "<input type='checkbox' id='select-all'> Select All";
        echo "</label>";
        while ($row = $res->fetch()) {
            $aid = $row['aid'];
            $allergenName = $row['name'];
            echo "<label class='ingredient-checkbox'>";
            echo "<input type='checkbox' name='allergy[]' value='$aid'>$allergenName";
            echo "</label>";
        }

        echo "</div>";
        ?>
        <script>

            document.getElementById('select-all').addEventListener('change', function () {
                var checkboxes = document.getElementsByName('allergy[]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
            });
        </script>
        <br><input type='submit' value='Remove from allergens'>
        </form>

        <?php
    } else {
        echo "<p>You don't have any allergies.</p>";
    }
    ?>
    <form method='POST' action='?op=preference'>
        <p> Want to add certain allergens? Add it here! ->
            <input type='submit' value='Add to Allergens'>
    </form>
    <?php
}

function processRemoveFromPreference($db, $post_data)
{
    if (isset($post_data['allergy'])) {
        $userID = $_SESSION['userID'];
        $allergens = $post_data['allergy'];

        // Loop through selected ingredients and remove them from the "have" table
        foreach ($allergens as $allergenID) {
            $sql = "DELETE FROM AA WHERE userID = $userID AND aid = $allergenID";
            $res = $db->query($sql);
            if ($res == false) {
                echo "<h3> Failed to remove from your allergens.</h3>";
            }
        }

        // Redirect or display a success message
        header("refresh:2;url=dashboard.php");
        echo "<h3>Successfully remove from your allergens</h3>";
        exit();
    }
}

function getUserAllergens($db, $userID)
{
    $sql = "SELECT AA.aid
            FROM AA 
            JOIN allergen a ON a.aid = AA.aid
            WHERE AA.userID = $userID";
    $res = $db->query($sql);
    $row = $res->fetchAll();
    return $row;
}


?>