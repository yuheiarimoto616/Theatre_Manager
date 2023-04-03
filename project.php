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
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Reset</h2>
        <p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

        <form method="POST" action="project.php">
            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form>

        <hr />

        <h2>Insert Values into Movie</h2>
        <form method="POST" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            ID: <input type="text" name="insID"> <br /><br />
            Duration: <input type="text" name="insDuration"> <br /><br />
            Rating: <input type="text" name="insRating"> <br /><br />
            Name: <input type="text" name="insName"> <br /><br />

            <p><input type="submit" value="Insert" name="insertSubmit"></p>
        </form>

        <hr />

        <h2>Update Name in Movie</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Old Name: <input type="text" name="oldName"> <br /><br />
            New Name: <input type="text" name="newName"> <br /><br />

            <p><input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <hr />

        <h2>Count the Tuples in Movie</h2>
        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <p><input type="submit" name="countTuples"></p>
        </form>
        <?php
        if (isset($_GET['countTupleRequest'])) {
            handleGETRequest();
        }
        ?>

        <hr />

        <h2>Display the Tuples in Movie</h2>
        <form method="GET" action="project.php"> <!--refresh page when submitted-->
            <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
            <p><input type="submit" name="displayTuples"></p>
        </form>
        <?php 
        if (isset($_GET['displayTupleRequest'])) {
            handleGETRequest();
        }
        ?>

        <hr />

        <h2>Movies Shown</h2>
        <form method="GET" action = "project.php">
            <input type = "hidden" id = "movieShown" name = "displayMoviesShownRequest">
            Location:
            <select id = "locations" name = "locations">
                <option value = "1234WM">1234 West Mall</option>
                <option value = "452SW">452 SW Marine Dr</option>
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
        <h2>Sales by Location</h2>
        <form method="GET" action = "project.php">
            <input type = "hidden" id = "movieShown" name = "displaySalesRequest">
            <p><input type="submit" value = "Submit" name="displaySales"></p>
        </form>
        <?php 
        if (isset($_GET['displaySalesRequest'])) {
            handleGETRequest();
        }
        ?>
        

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
                    //echo "<br>".$bind."<br>";
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

        function printResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table Movie:<br>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Duration</th><th>Rating</th><th>Name</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                // echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["duration"] . "</td><td>" . $row["Rating"] . "</td><td>" . $row["Name"] . "</td></tr>"; //or just use "echo $row[0]"
                // echo "<tr><td" . $row["ID"]
                // echo $row[0];
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["DURATION"] . "</td><td>" . $row["RATING"] . "</td><td>" . $row["NAME"] . "</td></tr>";
            }

            echo "</table>";
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

        function handleUpdateRequest() {
            global $db_conn;

            $old_name = $_POST['oldName'];
            $new_name = $_POST['newName'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE Movie SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
            OCICommit($db_conn);
        }

        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            executePlainSQL("DROP TABLE Movie");

            // Create new table
            echo "<br> creating new table <br>";
            executePlainSQL("CREATE TABLE Movie (ID	VARCHAR(20) PRIMARY KEY, duration INTEGER NOT NULL, rating	VARCHAR(10), name VARCHAR(50) NOT NULL)");
            OCICommit($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insID'],
                ":bind2" => $_POST['insDuration'],
                ":bind3" => $_POST['insRating'],
                ":bind4" => $_POST['insName']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into Movie values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM Movie");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in Movie: " . $row[0] . "<br>";
            }
        }

        function handleDisplayRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM Movie");
            printResult($result);
        }

        function handleMovieShownRequest() {
            global $db_conn;

            if ($_GET['locations'] == '1234WM') {
                $result = executePlainSQL(
                    "SELECT DISTINCT m.name, m.duration 
                    FROM Movie m, Shows s 
                    WHERE m.ID = s.movieID AND s.address = '1234 West Mall, Vancouver, BC'"
                );
            } else if ($_GET['locations'] == '452SW') {
                $result = executePlainSQL(
                    "SELECT DISTINCT m.name, m.duration 
                    FROM Movie m, Shows s 
                    WHERE m.ID = s.movieID AND s.address = '452 SW Marine Dr, Vancouver, BC'"
                );
            } else {
                $result = executePlainSQL(
                    "SELECT DISTINCT m.name, m.duration 
                    FROM Movie m, Shows s 
                    WHERE m.ID = s.movieID"
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

        function handleDisplaySalesRequest() {
            global $db_conn;

            $result = executePlainSQL(
                "SELECT t.address, SUM(t.price) as sales 
                FROM Ticket t
                GROUP BY t.address");

            echo "<table>";
            echo "<tr><th>Theatre</th><th>Total Sales</th>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                // echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["duration"] . "</td><td>" . $row["Rating"] . "</td><td>" . $row["Name"] . "</td></tr>"; //or just use "echo $row[0]"
                // echo "<tr><td" . $row["ID"]
                // echo $row[0];
                echo "<tr><td>" . $row["ADDRESS"] . "</td><td>" . $row["SALES"] . "</td></tr>";
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

                disconnectFromDB();
            }
        }

        if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        }
    
		?>
	</body>
</html>
