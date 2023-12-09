<?php
//Authors: ben (+phong for last two functions)
function ben_genSearchForm($db, $uid)
{
    if (!isset($uid)) {
        header("refresh:3;url=?op=main");
        printf("<H3>You must be logged in to search for a meal!</H3>\n");
    } else {
        ?>
        <section class="hero">
            <h1>Let's Start Cooking!</h1>
        </section>
        <DIV class='searchForm'>
            <FORM name='fmSearch' id='fmSearch' method='POST' action='?op=search' class='cta'>
                <!--mealtype-->
                <label for="mealType">What kind of meal do you want?</label><br>
                <select id="mealType" name="mealType">
                    <option value="any">Any</option>
                    <option value="Bf">Breakfast</option>
                    <option value="Lun">Lunch</option>
                    <option value="Din">Dinner</option>
                    <option value="Des">Dessert</option>
                    <option value="Sn">Snack</option>
                </select><br>
                <!--keyword-->
                <LABEL for='keyword'>Put in a keyword for your meal! (Optional)</LABEL><br>
                <INPUT type='text' id='keyword' name='keyword' placeholder='keyword (ie. eggs)' /><br>
                <!--calories-->
                Minimum and Maximum Calories (Optional)
                <br>
                <INPUT type="number" id="caloriesMin" name="caloriesMin" min="0">
                -
                <INPUT type="number" id="caloriesMax" name="caloriesMax" min="1"><br>

                Options
                <br>
                <label for="pantry">Include only meals that I have ingredients for</label>
                <INPUT type="checkbox" id="pantry" name="pantry" value="pantry"><br>

                <label for="preference">Exclude meals that I may be allergic to</label>
                <INPUT type="checkbox" id="preference" name="preference" value="preference" checked><br>

                <?php
                echo "<INPUT type='hidden' name='uid' value='$uid' />";
                ?>

                <INPUT type='submit' id='searchSubmit' value='Look For Meals!' />
            </FORM>
        </DIV>
        <?php
    }
}

function ben_search($db, $data)
{
    //get $uid
    $uid = $data['uid'];

    //first part of statement
    $stmt = ("SELECT mealID, name, description, recipe, imagePath, calories
        FROM meal as M1
        WHERE 1=1 ");

    //mealtype
    //always set, check if not set to any
    $mealType = $data['mealType'];
    if ($mealType != 'any') {
        $stmt .= "AND mealType LIKE '%$mealType%";
    }

    //keywords
    if ($data['keyword'] != "") {
        $keyword = $data['keyword'];
        $stmt .= " AND name LIKE '%$keyword%'";
    }

    //calories
    //min
    if ($data['caloriesMin'] != "") {
        $caloriesMin = $data['caloriesMin'];
        $stmt .= " AND calories >= $caloriesMin";
    }

    //max
    if ($data['caloriesMax'] != "") {
        $caloriesMax = $data['caloriesMax'];
        $stmt .= " AND calories <= $caloriesMax";
    }

    //preferences
    if ($data['preference'] == "preference") {
        $return = ben_phong_searchPreference($db, $uid);
        $stmt .= "$return";
    }

    //pantry
    if ($data['pantry'] == "pantry") {
        $return = ben_phong_searchPantry($db, $uid);
        $stmt .= "$return";
    }

    //send query and verify if correct
    $res = $db->query($stmt);

    if ($res == FALSE) {
        //header("refresh:3;url=?op=searchForm");
        printf("<H3>Error while attempting to search for meal!</H3>\n");
    } else {
        //send to searchTable
        ben_searchTable($res);
    }
}

function ben_searchTable($data)
{
    //make table header
    ?>
    <h2> Try these tailored meals just for you! </h2>
    <TABLE id="searchTable">
        <TR>
            <TH>Name</TH>
            <TH>Description</TH>
            <TH>Calories</TH>
            <TH>Select Meal</TH>
        </TR>
        <?php
        //for loop for all entries
        while ($row = $data->fetch()) {
            $name = $row['name'];
            $desc = $row['description'];
            $calories = $row['calories'];
            $mealID = $row['mealID'];

            //TODO, $mealID will be turned into a <button> for going to the meal (call a function?)
    
            echo "<TR><TD>$name</TD><TD>$desc</TD><TD>$calories</TD><TD><a href='home.php?op=showMeal&mealID=$mealID' style ='color:black'>Select</a></TD></TR>\n";
        }

        //close table
        echo "</TABLE>";
}

function ben_genPopularTable($db)
{
    //some formatting
    ?>
        <section class="hero">
            <h1>Popular Meals</h1>
            <p style="color:#e8e1c7">Try some meals others <u><i>LOVED!</i></u></p>
        </section>
        <?php
        //create SQL query
    
        //GOALS:
        //1) query needs to grab the avg rating of individual meals (avg rating for each meal, not avg rating overall)
        //2) query then groups by mealID (nat join meal) and sorts by highest rated (orderby)
        $stmt = "SELECT mealID, name, description, calories, (SELECT ROUND(AVG(rating))
                                                              FROM rating
                                                              WHERE meal.mealID = rating.mealID) AS rating
        FROM meal
        GROUP BY mealID
        HAVING rating >= 0
        ORDER BY rating DESC
        LIMIT 10;";

        //send query and error check
        $res = $db->query($stmt);

        if ($res == FALSE) {
            header("refresh:3;url=?op=main");
            printf("<H3>Error while attempting to search for popular meals!</H3>\n");
        }

        //make table
        ?>
        <TABLE id="searchTable">
            <TR>
                <TH>Name</TH>
                <TH>Description</TH>
                <TH>Calories</TH>
                <TH>Overall Rating</TH>
                <TH>Select Meal</TH>
            </TR>
            <?php
            //for loop for all entries
            while ($row = $res->fetch()) {
                $name = $row['name'];
                $desc = $row['description'];
                $calories = $row['calories'];
                $rating = $row['rating'];
                $mealID = $row['mealID'];

                //TODO, $mealID will be turned into a <button> for going to the meal (call a function?)
        
                echo "<TR><TD>$name</TD><TD>$desc</TD><TD>$calories</TD><TD>$rating</TD><TD><a href='home.php?op=showMeal&mealID=$mealID' style ='color:black'>Select</a></TD></TR>\n";
            }

            //close table
            echo "</TABLE>";
}

function ben_phong_searchPreference($db, $userID)
{
    $allergens = getUserAllergens($db, $userID);
    $stmt = " AND M1.mealID IN (SELECT M.mealID
                FROM meal AS M
                WHERE 1=1 ";

    foreach ($allergens as $al) {
        $aid = $al['aid'];
        $subquery = "AND $aid NOT IN (SELECT aid
                                      FROM MI NATURAL JOIN IA
                                      WHERE M.mealID = MI.mealID) ";

        $stmt .= $subquery;
    }
    $stmt .= ") ";
    return $stmt;
}

function ben_phong_searchPantry($db, $userID)
{
    $pantryIngredients = getUserPantryIngredients($db, $userID);
    $sql = " OR M1.mealID IN (SELECT m.mealID
                               FROM meal m
                               LEFT JOIN MI ON m.mealID = MI.mealID
                               GROUP BY m.mealID
                               HAVING ";

    $havingConditions = [];
    foreach ($pantryIngredients as $ingredient) {
        $havingConditions[] = "SUM(ingID = {$ingredient['iid']}) > 0";

    }
    if (!empty($havingConditions)) {
        $sql .= implode(' AND ', $havingConditions);
        $sql .= " AND COUNT(DISTINCT MI.ingID) >= 1 * (SELECT COUNT(DISTINCT ingID) FROM MI WHERE MI.mealID = m.mealID)) ";
    }
    return $sql;

}
?>