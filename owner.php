<html>
	<head>
        <title>Movie Theatre Manager</title>
        <style>
            table, th, td {
                border: 1px solid black;
            }
        </style>
    </head>

	<body>
		<button onclick = "document.location = 'project.php'">Customer</button>
		<button onclick = "document.location = 'owner.php'">Manage Theatre</button>

		<h2>Add Room</h2>
        <form method="POST" action="owner.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Room Number: <input type="text" name="insRoomNum"> <br /><br />
            Location:
            <select id = "theatres" name = "theatres">
                <?php
                if (connectToDB()) {
                    global $db_conn;
                    $result = executePlainSQL("SELECT address FROM Theatre");
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                ?>
                        <option value = "<?php echo $row["ADDRESS"]; ?>">
                        <?php echo $row["ADDRESS"];?>
                        </option>
                <?php }}?>
            </select> 
            <br /><br />
            Capacity: <input type="text" name="insCapacity"> <br /><br />


            <p><input type="submit" value="Insert" name="insertSubmit"></p>
        </form>


        <hr />

		<!-- <h2>Count the Tuples in Movie</h2>
        <form method="GET" action="owner.php">
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <p><input type="submit" name="countTuples"></p>
        </form>
        ?php
        if (isset($_GET['countTupleRequest'])) {
            handleGETRequest();
        }
        ?>

        


        <hr /> -->

        <h2>Display Data</h2>
        <form method="GET" action=""> <!--refresh page when submitted-->
            <input type="hidden" id="displayColsRequest" name="displayColsRequest">
            Type:
            <select id = "tables" name = "tables">
                <?php
                if (connectToDB()) {
                    global $db_conn;
                    $result = executePlainSQL("SELECT table_name FROM user_tables");
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                ?>
                        <option value = "<?php echo $row["TABLE_NAME"]; ?>">
                        <?php echo $row["TABLE_NAME"];?>
                        </option>
                <?php }}?>
            </select>
            <p><input type="submit" value="Select" name="displayCols"></p>
        </form>
        <?php
        if (isset($_GET['displayColsRequest'])) {
            handleGETRequest();
        }
        if (isset($_GET['displayTupleRequest'])) {
            handleGETRequest();
        }
        ?>

		<!-- <h2>Display the Tuples in Movie</h2>
        <form method="GET" action="owner.php"> 
            <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
            Table:
            <select id = "tables" name = "tables">
                ?php
                if (connectToDB()) {
                    global $db_conn;
                    $result = executePlainSQL("SELECT table_name FROM user_tables");
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                ?>
                        <option value = "?php echo $row["TABLE_NAME"]; ?>">
                        ?php echo $row["TABLE_NAME"];?>
                        </option>
                ?php }}?>
            </select>
            <p><input type="Select" name="displayTuples"></p>
        </form>
        ?php
        if (isset($_GET['displayTupleRequest'])) {
            handleGETRequest();
        }
        ?> -->

		<hr />

		<h2>Sales by Location</h2>
        <form method="GET" action = "owner.php">
            <input type = "hidden" id = "movieShown" name = "displaySalesRequest">
            <p><input type="submit" value = "Submit" name="displaySales"></p>
        </form>
        <?php
        if (isset($_GET['displaySalesRequest'])) {
            handleGETRequest();
        }
        ?>

        <hr />

        <h2>Find the average number of foodstuff a customer eats</h2>
        <form method="GET" action = "owner.php">
            <input type = "hidden" id = "movieShown" name = "displayAvgFoodRequest">
            <p><input type="submit" value = "Submit" name="displayAvgFood"></p>
        </form>
        <?php
        if (isset($_GET['displayAvgFoodRequest'])) {
            handleGETRequest();
        }
        ?>

		<hr />

        <h2>Find customers who have eaten all types of food offered</h2>
        <form method="GET" action = "owner.php">
            <input type = "hidden" id = "movieShown" name = "displayDivisionRequest">
            <p><input type="submit" value = "Submit" name="displayDivision"></p>
        </form>
        <?php
        if (isset($_GET['displayDivisionRequest'])) {
            handleGETRequest();
        }
        ?>

        <hr />

        <h2>Delete Theatre</h2>
        <form method="POST" action = "owner.php">
            <input type = "hidden" id = "theatreDelete" name = "theatreDeleteRequest">
            Theatre address:
            <select id = "theatres" name = "theatres">
                <?php
                if (connectToDB()) {
                    global $db_conn;
                    $result = executePlainSQL("SELECT address FROM Theatre");
                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                ?>
                        <option value = "<?php echo $row["ADDRESS"]; ?>">
                        <?php echo $row["ADDRESS"];?>
                        </option>
                <?php }}?>
            </select>
            <p><input type="submit" value = "Submit" name="theatreDelete"></p>
        </form>

		<hr />

		<?php
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
                $errorMessage = $e['message'];
                echo htmlentities($errorMessage);  //error message
                echo '<script>alert("'.$errorMessage.'")</script>';
                $success = False;
            }


            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                $errorMessage = $e['message'];
                echo htmlentities($errorMessage);  //error message
                echo '<script>alert("'.$errorMessage.'")</script>';
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
                $errorMessage = $e['message'];
                echo htmlentities($errorMessage);  //error message
                echo '<script>alert("'.$errorMessage.'")</script>';
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
                    $errorMessage = $e['message'];
                    // echo '<script>alert(' . $errorMessage . ')</script>';
                    // echo '<script>alert("Invalid input!  Error: ' . $errorMessage . '")</script>';
                    echo htmlentities($errorMessage);  //error message
                    $needle = "unique";
                    if (str_contains($errorMessage, $needle)) {
                        echo '<script>alert("Failed! Already exists!")</script>';
                    }
                    else {
                        echo '<script>alert("'.$errorMessage.'")</script>';
                    }
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

		function printResult($result) { //prints results from a select statement
            global $db_conn;
            echo "<table>";
            $cols =  executePlainSQL("SELECT DISTINCT column_name from USER_TAB_COLS WHERE table_name='$table'");
            echo "<tr>";
            while ($colsArray =  OCI_Fetch_Array($cols, OCI_BOTH)) {
                echo "<th>" . $colsArray["COLUMN_NAME"] . "</th>";
            }
            echo "</tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                // echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["duration"] . "</td><td>" . $row["Rating"] . "</td><td>" . $row["Name"] . "</td></tr>"; //or just use "echo $row[0]"
                // echo "<tr><td" . $row["ID"]
                // echo $row[0];
                echo "<tr>";
                $table = $_GET['tables'];
                $cols =  executePlainSQL("SELECT DISTINCT column_name from USER_TAB_COLS WHERE table_name='$table'");
                while ($colsArray =  OCI_Fetch_Array($cols, OCI_BOTH)) {
                    $colName = $colsArray["COLUMN_NAME"];
                    echo  "<td>" . $row[$colName] . "</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
        }

		function handleInsertRequest() {
            global $db_conn, $success;


            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['insRoomNum'],
                ":bind2" => $_POST['theatres'],
                ":bind3" => $_POST['insCapacity']
            );


            $alltuples = array (
                $tuple
            );

            if ($_POST['insRoomNum'] == "") {
                echo '<script>alert("Failed RoomNum cannot be empty!")</script>';
            }

            executeBoundSQL("insert into Room values (:bind1, :bind2, :bind3)", $alltuples);
            OCICommit($db_conn);
        }

		function handleCountRequest() {
            global $db_conn;


            $result = executePlainSQL("SELECT Count(*) FROM Movie");


            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in Movie: " . $row[0] . "<br>";
            }
        }

		// function handleResetRequest() {
		// 	global $db_conn;
		// 	// Drop old table
		// 	executePlainSQL("DROP TABLE Movie");


		// 	// Create new table
		// 	echo "<br> creating new table <br>";
		// 	executePlainSQL("CREATE TABLE Movie (ID VARCHAR(20) PRIMARY KEY, duration INTEGER NOT NULL, rating  VARCHAR(10), name VARCHAR(50) NOT NULL)");
		// 	OCICommit($db_conn);
		// }

        function handleDisplayColsRequest() {
            global $db_conn;

            $table = $_GET['tables'];

            echo "<form method='GET' action='owner.php'>";
            echo "<input type='hidden' id='displayTupleRequest' name='displayTupleRequest'>";
            echo "<input type = 'hidden' id='table' name='table' value=" . $table . ">";
            echo "Columns: <br/>";
            $cols =  executePlainSQL("SELECT DISTINCT column_name from USER_TAB_COLS WHERE table_name='$table'");
            while ($colsArray =  OCI_Fetch_Array($cols, OCI_BOTH)) {
                echo "<input type='checkbox' name='cols[]' value=" . $colsArray["COLUMN_NAME"] .  ">" . $colsArray["COLUMN_NAME"] . "<br/>";
            }
            echo "<p><input type='Submit' name='displayTuples'></p>";
            echo "</form>";
        }

		function handleDisplayRequest() {
            global $db_conn;

            $cols = $_GET['cols'];
            $table = $_GET['table'];
            $select = "";
            foreach($cols as $col) {
                $select .= $col . ", ";
            }
            $select = chop($select, ", ");

            $result = executePlainSQL("SELECT $select FROM $table");
            // printResult($result);
            echo "<table>";
            echo "<tr>";
            foreach ($cols as $col) {
                echo "<th>" . $col. "</th>";
            }
            echo "</tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr>";
                foreach ($cols as $col) {
                    echo  "<td>" . $row[$col] . "</td>";
                }
                echo "</tr>";
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
                echo "<tr><td>" . $row["ADDRESS"] . "</td><td>" . $row["SALES"] . "</td></tr>";
            }


            echo "</table>";
        }

        function handleDisplayAvgFoodRequest() {
            global $db_conn;


            $result = executePlainSQL(
                "SELECT AVG(countPerCustomer) as averageFoodPerCustomer
                FROM (SELECT customerID, COUNT(*) as countPerCustomer
                    FROM Eats
                    GROUP BY customerID) 
                ");


            echo "<table>";
            echo "<tr><th>Average food items per customers</th></tr>";


            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td></tr>";
            }


            echo "</table>";
        }

        function handleDisplayDivisionRequest() {
            global $db_conn;


            $result = executePlainSQL("SELECT * FROM Customer C WHERE NOT EXISTS 
                ((SELECT F.ID FROM FoodStuff F) MINUS 
                (SELECT E.foodStuffID FROM Eats E WHERE E.customerID = C.ID))");

            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th>";


            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>";
            }


            echo "</table>";
        }

        function handleTheatreDeleteRequest() {
            global $db_conn, $success;

            $tuple = array (
                ":bind1" => $_POST['theatres']
            );


            $alltuples = array (
                $tuple
            );

            executeBoundSQL("DELETE FROM Theatre WHERE address = :bind1", $alltuples);
            OCICommit($db_conn);
        }

		function handlePOSTRequest() {
            global $success;

			if (connectToDB()) {
				if (array_key_exists('updateQueryRequest', $_POST)) {
					handleUpdateRequest();
				} else if (array_key_exists('insertQueryRequest', $_POST)) {
					handleInsertRequest();
				} else if (array_key_exists('theatreDeleteRequest', $_POST)) {
                    handleTheatreDeleteRequest();
                }

                if ($success) {
                    echo '<script>alert("Success")</script>';
                }

				disconnectFromDB();
			}
		}

		function handleGETRequest() {
            global $success;

            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                }
                else if (array_key_exists('displayCols', $_GET)) {
                    handleDisplayColsRequest();
                }
                else if (array_key_exists('displayTuples', $_GET)) {
                    handleDisplayRequest();
                }
                else if (array_key_exists('displaySales', $_GET)) {
                    handleDisplaySalesRequest();
                }
                else if (array_key_exists('displayAvgFood', $_GET)) {
                    handleDisplayAvgFoodRequest();
                }
                else if (array_key_exists('displayDivision', $_GET)) {
                    handleDisplayDivisionRequest();
                }


                disconnectFromDB();
            }
        }

		if (isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['theatreDelete'])) {
            handlePOSTRequest();
		} 
		?>
	</body>
</html>
