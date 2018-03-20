<?php
$servername = "localhost";
$username = "root";
$password = "devitm";
$dbname ="refuge";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submitbutton'])) {   

	$sql = "INSERT INTO activity (activity, date, location) VALUES ('".$_POST['activityName']."', '".$_POST['time']."', '".$_POST['place']."')";
	$conn->query($sql);

	$activity_id = $conn->insert_id;
	
	$sql = "INSERT INTO friends (friend_name, activity_id, confirmed) VALUES (".$_POST['uid'].", ".$activity_id.", 1)";
	$conn->query($sql);

	$friend = $_POST['selectFriend'];
    $N = count($friend);
    for($i=0; $i < $N; $i++)
    {
    	$sql = "INSERT INTO friends (friend_name, activity_id,confirmed) VALUES (".$friend[$i].", ".$activity_id.",0)";
    	$conn->query($sql);
    }

}

if(isset($_POST['viewActivities'])){
	$unid = $_POST['unid'];

	$sql = "SELECT activity_id FROM friends WHERE friend_name='".$unid."' and confirmed=0";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$activities = array();
		$j=0;
	    while($row = $result->fetch_assoc()) {
	    	$activity_id = $row["activity_id"];
	    	$sql2 = "SELECT * FROM activity WHERE id=".$activity_id;
			$result2 = $conn->query($sql2);
			if ($result2->num_rows > 0) {
			    while($row2 = $result2->fetch_assoc()) {
			    	$sql3 = "SELECT friend_name FROM friends WHERE activity_id=".$activity_id;
					$result3 = $conn->query($sql3);
					$friends = array();

					if ($result3->num_rows > 0) {
						$i=0;
		    			while($row3 = $result3->fetch_assoc()) {
		    				$friends[$i]=$row3["friend_name"];
		    				$i=$i+1;

		    			}
		    		}
		    		$activity = array();
		    		$activity['id'] = $row2["id"];
		    		$activity['name'] =  $row2["activity"];
		    		$activity['date'] = $row2["date"];
		    		$activity['location'] = $row2["location"];
		    		$activity['friends']= $friends;
		    		$friends = array(); $i=0;


		    		$activities[$j]=$activity;
		    		$j=$j+1;
					
			    }
			}
	    }

	    $activitiesjson=json_encode($activities);
	    echo $activitiesjson;

	}
}

if(isset($_POST['confirmActivity'])){

	$sql = "UPDATE friends SET confirmed=1 WHERE activity_id=".$_POST['activity_id']." AND friend_name ='".$_POST['unid']."'";
	echo $sql;
	$conn->query($sql);

	/*$sql = "SELECT * from activity where id=".$_POST['activity_id'];
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$sql = "INSERT INTO confirmed_activities (activity, date, location,activity_id) VALUES ('".$row['activity']."', '".$row['date']."', '".$row['location']."', ".$_POST['activity_id'].")";
	//$sql = "INSERT INTO activity (activity, date, location) VALUES ('".$_POST['activityName']."', '".$_POST['time']."', '".$_POST['place']."')";
	$conn->query($sql);

	$sql = "DELETE FROM activity where id=".$_POST['activity_id'];
	$conn->query($sql);
	}*/

}

if(isset($_POST['viewConfirmed'])){

	$unid = $_POST['unid'];

	$sql = "SELECT activity_id FROM friends WHERE friend_name='".$unid."'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		$activities = array();
		$j=0;
	    while($row = $result->fetch_assoc()) {
	    	$activity_id = $row["activity_id"];
	    	$sql2 = "SELECT * FROM activity WHERE id=".$activity_id; 
			$result2 = $conn->query($sql2);
			if ($result2->num_rows > 0) {
			    while($row2 = $result2->fetch_assoc()) {
			    	$sql3 = "SELECT friend_name FROM friends WHERE activity_id=".$activity_id." and confirmed=1";
					$result3 = $conn->query($sql3);
					$friends = array();

					if ($result3->num_rows > 0) {
						$i=0;
		    			while($row3 = $result3->fetch_assoc()) {
		    				$friends[$i]=$row3["friend_name"];
		    				$i=$i+1;

		    			}
		    		}
		    		$activity = array();
		    		$activity['name'] =  $row2["activity"];
		    		$activity['date'] = $row2["date"];
		    		$activity['location'] = $row2["location"];
		    		$activity['friends']= $friends;
		    		$friends = array(); $i=0;


		    		$activities[$j]=$activity;
		    		$j=$j+1;
					
			    }
			}
	    }
	}


	    $activitiesjson=json_encode($activities);
	    echo $activitiesjson;
}


	$conn->close();


?>