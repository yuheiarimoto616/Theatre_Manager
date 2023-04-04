<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values


  IF YOU HAVE A TABLE CALLED "Movie" IT WILL BE DESTROYED


  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->


  <html>
    <head>
        <title>Movie Theatre</title>
        <style>
            table, th, td {
                border: 1px solid black;
            }
        </style>
    </head>


    <body>
        <button onclick = "document.location = 'project.php'">Customer</button>
        <button onclick = "document.location = 'owner.php'">Manage Theatre</button>

        <h2>Personal Information</h2>
        <form method="GET" action="project.php">
        <input type="hidden" id="updateQueryRequest" name="showInfoRequest">
        Your ID: <input type="text" name="id"> <br /><br />
        <p><input type="submit" value="Show" name="showInfo"></p>
        </form>
        <?php
        if (isset($_GET['showInfoRequest'])) {
            handleGETRequest();
        }
        ?>   

        <h2>{UPDATE} Update Personal Information</h2>
        <p>Put your ID to update your personal info. Leave the field empty for unchanged information.</p>

        <form method="POST" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Your ID: <input type="text" name="updateID"> <br /><br />
            Update Name: <input type="text" name="updateName"> <br /><br />
            Update Email: <input type="text" name="updateEmail"> <br /><br />
            Update Phone: <input type="text" name="updatePhone"> <br /><br />
            Update Birthday: <input type="date" name="updateDOB"> <br /><br />

            <p><input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <hr />

        <h2>Movies Rating</h2>
        <form method="GET" action = "project.php">
            <input type = "hidden" id = "movieShown" name = "displayMoviesRatingRequest">
            <p><input type="submit" value = "Submit" name="displayMoviesRating"></p>
        </form>
        <?php
        if (isset($_GET['displayMoviesRatingRequest'])) {
            handleGETRequest();
        }
        ?>      
       
        <hr />

        <h2>Movies Shown</h2>
        <form method="GET" action = "project.php">
            <input type = "hidden" id = "movieShown" name = "displayMoviesShownRequest">
            Location:
            <select id = "locations" name = "locations">
                <?php
                if (connectToDB()) {
                    global $db_conn;
                    $result = executePlainSQL("SELECT DISTINCT address FROM Shows");
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                ?>
                        <option value = "<?php echo $row["ADDRESS"]; ?>">
                        <?php echo $row["ADDRESS"];?>
                        </option>
                <?php }}?>
                <!-- <option value = "1234WM">1234 West Mall</option>
                <option value = "452SW">452 SW Marine Dr</option>
                 -->
                <option value = "all">All</option>
            </select>
            <p><input type="submit" value = "Submit" name="displayMoviesShown"></p>
        </form>
        <?php
        if (isset($_GET['displayMoviesShownRequest'])) {
            handleGETRequest();
        }
        ?>


        <hr />
        <h2>{SELECTION} Select movies based on rating</h2>
        <form method="GET" action = "project.php">
            <input type = "hidden" id = "moviesSelect" name = "displayMoviesSelectRequest">
            Rating:
            <select id = "ratings" name = "ratings">
                <?php
                if (connectToDB()) {
                    global $db_conn;
                    $result = executePlainSQL("SELECT DISTINCT rating FROM Movie");
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                ?>
                        <option value = "<?php echo $row["RATING"]; ?>">
                        <?php echo $row["RATING"];?>
                        </option>
                <?php }}?>
                <option value = "all">All</option>
            </select>
            <p><input type="submit" value = "Submit" name="displayMoviesSelect"></p>
        </form>
        <?php
        if (isset($_GET['displayMoviesSelectRequest'])) {
            handleGETRequest();
        }
        ?>

        <hr />


        <?php
        //this tells the system that it's no longer just parsing html; it's now parsing PHP


        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())


        function debugAlertMessage($message) {
            global $show_debug_alert_messages;


            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }


        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;


            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work


            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }


            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }


            return $statement;
        }


        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
        In this case you don't need to create the statement several times. Bound variables cause a statement to only be
        parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
        See the sample code below for how this function is used */


            global $db_conn, $success;
            $statement = OCIParse($db_conn, $cmdstr);


            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }


            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    // echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
                }


                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }

            return $statement;
        }

        function connectToDB() {
            global $db_conn;


            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
            // ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_seanquan", "a43496900", "dbhost.students.cs.ubc.ca:1522/stu");


            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }


        function disconnectFromDB() {
            global $db_conn;


            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function showInfo() {
            global $db_conn;

            $tuple = array (
                ":bind1" => $_GET['id']
            );

            $alltuples = array (
                $tuple
            );

			$result = executeBoundSQL("SELECT * FROM Customer WHERE ID=:bind1", $alltuples);

            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Birthday</th></tr>";
            while ($row = OCI_Fetch_Array($result)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td><td>" . $row["EMAIL"] . "</td><td>" . $row["PHONE"] . "</td><td>" . $row["DATEOFBIRTH"] . "</td></tr>";
            }
            echo "</table>";
        }

        function handleUpdateRequest() {
            global $db_conn;
 
            $newName = $_POST['updateName'];
            $newEmail = $_POST['updateEmail'];
            $newPhone = $_POST['updatePhone'];
            $newDOB = $_POST['updateDOB'];

			if ($_POST['updateID'] == "") {
                echo '<script>alert("Need ID to update")</script>';
            }
            if ($_POST['updateName'] == "") {
                $result = executePlainSQL("SELECT name FROM Customer WHERE ID ='" . $_POST['updateID'] . "'");
                $row = oci_fetch_row($result);
                $newName = $row[0];
            }
            if ($_POST['updateEmail'] == "") {
                $result = executePlainSQL("SELECT email FROM Customer WHERE ID ='" . $_POST['updateID'] . "'");
                $row = oci_fetch_row($result);
                $newEmail = $row[0];
            }
            if ($_POST['updatePhone'] == "") {
                $result = executePlainSQL("SELECT phone FROM Customer WHERE ID ='" . $_POST['updateID'] . "'");
                $row = oci_fetch_row($result);
                $newPhone = $row[0];
            }
            if ($_POST['updateDOB'] == "") {
                $result = executePlainSQL("SELECT dateOfBirth FROM Customer WHERE ID ='" . $_POST['updateID'] . "'");
                $row = oci_fetch_row($result);
                $newDOB = $row[0];
            }

            // $date = new DateTime($newDOB);
            // $newDOB = $date->format('Y-m-d H:i:s');

            $tuple = array (
                ":bind1" => $_POST['updateID'],
                ":bind2" => $newName,
                ":bind3" => $newEmail,
                ":bind4" => $newPhone,
                ":bind5" => $newDOB,
            );

            $alltuples = array (
                $tuple
            );


            if ($_POST['updateDOB'] == "") {
                executeBoundSQL(
                    "UPDATE Customer 
                    SET ID =:bind1, name=:bind2, email=:bind3, phone=:bind4, dateOfBirth=:bind5
                    WHERE ID=:bind1"
                    , $alltuples
                );
            }
            else {
                executeBoundSQL(
                    "UPDATE Customer 
                    SET ID =:bind1, name=:bind2, email=:bind3, phone=:bind4, dateOfBirth=DATE'$newDOB'
                    WHERE ID=:bind1"
                    , $alltuples
                );
            }
            OCICommit($db_conn);
        }

        function handleMovieShownRequest() {
            global $db_conn;

            $location = $_GET['locations'];
            if ($location == 'all') {
                $result = executePlainSQL(
                    "SELECT DISTINCT m.name, m.duration
                    FROM Movie m, Shows s
                    WHERE m.ID = s.movieID"
                );
            } else {
                $result = executePlainSQL(
                    "SELECT DISTINCT m.name, m.duration
                    FROM Movie m, Shows s
                    WHERE m.ID = s.movieID AND s.address = '$location'"
                );
            }

            echo "<table>";
            echo "<tr><th>Name</th><th>Duration (min)</th>";


            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                // echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["duration"] . "</td><td>" . $row["Rating"] . "</td><td>" . $row["Name"] . "</td></tr>"; //or just use "echo $row[0]"
                // echo "<tr><td" . $row["ID"]
                // echo $row[0];
                echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["DURATION"] . "</td></tr>";
            }


            echo "</table>";
        }


        //SEAN
        function handleDisplayMoviesSelectRequest() {
            global $db_conn;

            $rating = $_GET['ratings'];

            if ($rating == 'all') {
                $result = executePlainSQL(
                    "SELECT * FROM Movie"
                );
            } else {
                $result = executePlainSQL(
                    "SELECT *
                    FROM Movie
                    WHERE rating = '$rating'"
                );
            }

            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Duration</th><th>Rating</th></tr>";
            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td><td>" . $row["DURATION"] . "</td><td>" . $row["RATING"] . "</td></tr>";
            }
            echo "</table>";
        }


        function handleMovieRatingRequest() {
            global $db_conn;


            $result = executePlainSQL(
                "SELECT m.name, AVG(r.star) as rating
                FROM Review r, Movie m
                WHERE r.movieID = m.ID
                GROUP BY m.name
                HAVING COUNT(*) > 1");


            echo "<table>";
            echo "<tr><th>Movie</th><th>Rating</th>";


            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                // echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["duration"] . "</td><td>" . $row["Rating"] . "</td><td>" . $row["Name"] . "</td></tr>"; //or just use "echo $row[0]"
                // echo "<tr><td" . $row["ID"]
                // echo $row[0];
                echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["RATING"] . "</td></tr>";
            }


            echo "</table>";
        }


        // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            global $success;
            if (connectToDB()) {
                 if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } 

                if ($success) {
                    echo '<script>alert("POST success")</script>';
                }
                else {
                    echo '<script>alert("POST failed")</script>';
                }

                disconnectFromDB();
            }
        }


        // HANDLE ALL GET ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('displayMoviesShown', $_GET)) {
                    handleMovieShownRequest();
                }
                else if (array_key_exists('displayMoviesRating', $_GET)) {
                    handleMovieRatingRequest();
                }
                else if (array_key_exists('displayMoviesSelect', $_GET)) {
                    handleDisplayMoviesSelectRequest();
                } 
                else if (array_key_exists('showInfo', $_GET)) {
                    showInfo();
                }


                disconnectFromDB();
            }
        }


        if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        }
   
        ?>
    </body>
</html>
