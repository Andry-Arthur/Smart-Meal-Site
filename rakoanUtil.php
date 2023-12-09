<?php
session_start();
include_once("db_connect.php");
//Author: Andry
function showSettings($uid, $db)
{
    echo "<div class='settings'>";
    $sql = "SELECT userName, password, email, fname, lname 
            FROM account 
            WHERE userID=$uid";

    $res = $db->query($sql);

    if ($res != FALSE) {
        $row = $res->fetch();
        $uname = $row['userName'];
        $password = $row['password'];
        $fname = $row['fname'];
        $lname = $row['lname'];
        $email = $row['email'];

        echo "<h1>Settings</h1>";
        echo "<h2>Personal Details:</h2>";
        echo "<h3>username: $uname</h3>";
        echo "<h3>first name: $fname</h3>";
        echo "<h3>last name: $lname</h3>";
?>
        <form name='personalForm' action='?op=changeName' method='POST' onsubmit='return validatePersonalForm()'>
            <label for='username'>Username: </label>
            <input type="text" id="username" name="username"><br><br>
            <label for="fname">First name:</label>
            <input type="text" id="fname" name="fname"><br><br>
            <label for="lname">Last name:</label>
            <input type="text" id="lname" name="lname"><br><br>
            <input type="submit" value="confirm change">
        </form>
        <?php
        echo "<h2>Security:</h2>";
        echo "<script>var myPassword = '$password'</script>";
        ?>

        <form name='passwordForm' action='?op=changePw' method='POST' onsubmit='return validatePwForm(myPassword)'>
            <label for='currPw'>Current Password: </label><br>
            <input type="password" id="currPw" name="currPw" required><br><br>
            <label for="newPw">New Password:</label><br>
            <input type="password" id="newPwOne" name="newPwOne" minlength='8' required><br><br>
            <label for="lname">Confirm new password:</label><br>
            <input type="password" id="newPwTwo" name="newPwTwo" minlength='8' required><br><br>
            <input type="submit" value="Change password">
        </form>
    <?php

        echo "</div>";
    }
}

function changeName($data, $db)
{
    $uid = $_SESSION['userID'];
    $newUname = $data['username'];
    $fname = $data['fname'];
    $lname = $data['lname'];

    $sql = "UPDATE account";

    if ($newUname != '') {
        if (strlen($sql) == 14) {
            $sql .= " SET";
        }
        $sql .= " userName = '$newUname'";
    }

    if ($fname != '') {
        if (strlen($sql) == 14) {
            $sql .= " SET";
        }
        $sql .= " fname = '$fname'";
    }

    if ($lname != '') {
        if (strlen($sql) == 14) {
            $sql .= " SET";
        }
        $sql .= " lname = '$lname'";
    }

    $sql .= " WHERE userID=$uid";

    $res = $db->query($sql);

    if ($res != FALSE) {
        echo "<h2>Your personal details have been successfully changed.<h2>";
        header("refresh:3;url=dashboard.php");
    } else {
        echo "<h2>An error occured. Modifications have not been made.\n$sql<h2>";
        header("refresh:3;url=home.php?op=settings");
    }
}

function changePassword($newPw, $db)
{
    $uid = $_SESSION['userID'];
    $sql = "UPDATE account
            SET password = '$newPw'
            WHERE userID=$uid";

    $res = $db->query($sql);

    if ($res != FALSE) {
        echo "<h2>Your password has been successfully changed.<h2>";
        header("refresh:3;url=dashboard.php");
    } else {
        echo "<h2>An error occured. Password has not been changed. UID=$uid<h2>";
        header("refresh:3;url=home.php?op=settings");
    }
}

function showHistory($uid, $db)
{
    
    $sql = "SELECT AM.mealID, AM.date, meal.name
            FROM AM LEFT JOIN meal ON AM.mealID=meal.mealID
            WHERE AM.userID=$uid
            GROUP BY AM.mealID, AM.date
            ORDER BY date DESC";

    $res = $db->query($sql);

    if ($res != FALSE) {
        echo "<h2><u>Meal History:</u></h2>";
        echo "<table class='history'><tr>";
        echo "<th>Meal</th> <th>date</th></tr>";
        while ($row = $res->fetch()) {
            $name = $row['name'];
            $date = $row['date'];
            $mealID = $row['mealID'];

            echo "<tr><td><a href='home.php?op=showMeal&mealID=$mealID' style='color: black'>$name</a></td><td>$date</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<h1>No past recipe has been found. Please feel free to start browsing recipes!</h1>";
    }
}

function showMeal($mealID, $db)
{
    $uid = $_SESSION['userID'];
    echo "<div class='mealPage'>";
    $sql = "SELECT meal.*, 
                (SELECT ROUND(AVG(rating), 2)
                FROM rating 
                WHERE mealID =$mealID) AS rating 
            FROM meal 
            WHERE mealID=$mealID";
    $res = $db->query($sql);

    if ($res != FALSE) {
        $row = $res->fetch();
        $name = $row['name'];
        $desc = $row['description'];
        $calories = $row['calories'];
        $recipe = $row['recipe'];
        $imgPath = $row['imagePath'];
        $rating = $row['rating'];

        echo "<h2 align='left'><u>Meal Page</u>:</h2>";
        echo "<img src='$imgPath' width=200 height=200 border=5px>";
        echo "<h3>$name ($calories cal)</h3>";
        echo "<h3><u>description</u></h3>";
        echo "<p>$desc</p>";
        echo "<h3><u>recipe</u></h3>";
        echo "<p>$recipe</p>";

        $sql = "SELECT name FROM MI JOIN ingredient ON ingredient.ingID=MI.ingID WHERE mealID=$mealID";
        $res = $db->query($sql);
        if ($res != FALSE) {
            echo "<h3><u>Ingredients</u></h3>";
            echo "<p>";
            while ($row = $res->fetch()) {
                $ing = $row['name'];
                echo $ing . " ";
            }
            echo "</p>";
        }
        echo "<h3><u>overall ratings</u>: $rating/5</h3>";

        $sql = "SELECT rating FROM rating WHERE userID=$uid and mealID=$mealID";
        $res = $db->query($sql);

        echo "<h3><u>personal rating</u>: ";

        if ($res != FALSE) {
            $row = $res->fetch();

            $personalRating = $row['rating'];

            if (isset($personalRating)) {
                echo "$personalRating/5 </h3>";
            } else {
                echo " No ratings yet. <a href='home.php?op=rateMealForm&mealID=$mealID' style = 'color: #9c1e21'> Rate Meal </a>";
            }
        }
    }
    echo "</div>";

    echo "<div class='reviews'>";
    echo "<h3 align='left'><u>Reviews:</u></h3>";

    $sql = "SELECT userName, rating, comment 
            FROM rating LEFT JOIN account ON rating.userID=account.userID
            WHERE mealID=$mealID";

    $res = $db->query($sql);

    if ($res != false) {
        while ($row = $res->fetch()) {
            $uname = $row['userName'];
            $rating = $row['rating'];
            $comment = $row['comment'];

            echo "<h4 align='left'>$uname</h4>";
            echo "<p align='left' style='margin-left: 25px' ><b>$rating/5:</b> &#39;&#39;$comment&#39;&#39;</p><hr>";
        }
    }

    echo "</div>";

    $sql = "SELECT COUNT(*) as count FROM AM WHERE userID=$uid and mealID=$mealID";
    $res = $db->query($sql);
    if($res != FALSE) {
        $row = $res->fetch();
        if($row['count'] > 0) {
            $sql = "UPDATE AM
                    SET date=NOW()
                    WHERE userID=$uid and mealID=$mealID";

            $res = $db->query($sql);
            if($res == FALSE) {
                echo "<H2>ERROR: Meal History Failed to Updated.</H2>";
            }
        }
        else {
            $sql = "INSERT INTO AM (userID, mealID, date)
                    VALUES ($uid, $mealID, NOW())";
            
            $res = $db->query($sql);
            if($res == FALSE) {
                echo "<H2>ERROR: Meal History Failed to Updated.</H2>";
            }
        }
    }

}

function showRateForm($mealID, $db)
{
    $sql = "SELECT name FROM meal WHERE mealID=$mealID";
    $res = $db->query($sql);
    $name = $res->fetch()['name'];

    echo "<div class='rateMealForm'>";
    echo "<h3><u> Rate the meal</u>: $name</h3> "
    ?>

    <form name='rateMealForm' method='POST' action='?op=rateMeal&mealID=<?php echo $mealID ?>'>
        <p> Please rate how much you enjoyed the meal out of 5: (1- Terrible, 5- Excellent) </p>
        <label for="1">1<input type="radio" id="1" name="rb_rating" value="1" required></label>&emsp;&emsp;
        <label for="2">2<input type="radio" id="2" name="rb_rating" value="2"></label>&emsp;&emsp;
        <label for="3">3<input type="radio" id="3" name="rb_rating" value="3"></label>&emsp;&emsp;
        <label for="4">4<input type="radio" id="4" name="rb_rating" value="4"></label>&emsp;&emsp;
        <label for="5">5<input type="radio" id="5" name="rb_rating" value="5"></label><br><br>

        <p>Please comment on the meal in a few sentences (max 400 characters): </p>
        <textarea id="comments" name="comments" rows="10" cols="50" maxlength="400" required></textarea><br><br>


        <input type="submit" value="Submit ratings">
    </form>
    </div>
<?php
}

function rateMeal($data, $db)
{
    print_r($data);
    $rate = $data['rb_rating'];
    $comment = '"' . $data['comments'] . '"';
    $uid = $_SESSION['userID'];
    $mealID = $_GET['mealID'];

    $sql = "INSERT INTO rating (userID, mealID, rating, comment)
            VALUES ($uid, $mealID, $rate, $comment)";

    $res = $db->query($sql);

    if ($res != FALSE) {
        echo "<h2>Thank you for your feedback.</h2>";
        header("refresh:3;url=?op=showMeal&mealID=$mealID");
    } else {
        echo "<h2>An error occurred. Feedback has not been saved.</h2>";
        echo $sql;
    }
}

?>

<script>
    function validatePwForm(password) {
        var curr = document.forms["passwordForm"]["currPw"].value;
        var pw1 = document.forms["passwordForm"]["newPwOne"].value;
        var pw2 = document.forms["passwordForm"]["newPwTwo"].value;
        if (curr.trim() != password) {
            alert("Current password is incorrent.");
            return false;
        }
        if (pw1.trim() != pw2.trim()) {
            alert("Please make sure the new passwords match.");
            return false;
        }
        return true;
    }

    function validatePersonalForm() {
        var input1 = document.forms["personalForm"]["username"];
        var input2 = document.forms["personalForm"]["fname"];
        var input3 = document.forms["personalForm"]["lname"];

        if (input1.value == "" && input2.value == "" && input3.value == "") {
            alert("Error: Input fields are all empty");
            return false
        }
        return true;
    }
</script>