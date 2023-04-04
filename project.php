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
        }

        function connectToDB() {
            global $db_conn;


            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
            // ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_yuhei616", "a36561967", "dbhost.students.cs.ubc.ca:1522/stu");


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
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                }


                disconnectFromDB();
            }
        }


        // HANDLE ALL GET ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                }
                else if (array_key_exists('displayTuples', $_GET)) {
                    handleDisplayRequest();
                }
                else if (array_key_exists('displayMoviesShown', $_GET)) {
                    handleMovieShownRequest();
                }
                else if (array_key_exists('displayMoviesRating', $_GET)) {
                    handleMovieRatingRequest();
                }
                else if (array_key_exists('displaySales', $_GET)) {
                    handleDisplaySalesRequest();
                }
                else if (array_key_exists('displayMoviesSelect', $_GET)) {
                    handleDisplayMoviesSelectRequest();
                }
                else if (array_key_exists('theatreDelete', $_GET)) {
                    handleTheatreDeleteRequest();
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
