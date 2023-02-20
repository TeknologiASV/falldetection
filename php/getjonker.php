<?php
require_once "db_connect.php";

session_start();

if(isset($_POST['startDate'], $_POST['endDate'])){
    $startDate = filter_input(INPUT_POST, 'startDate', FILTER_SANITIZE_STRING);
    $endDate = filter_input(INPUT_POST, 'endDate', FILTER_SANITIZE_STRING);
    $fallCount = 0;
    $stepCount = 0;
    $start;
    $end;

    if ($select_stmt = $db->prepare("SELECT * FROM fall WHERE Date>=? AND Date<=? ORDER BY Date")) {
        $select_stmt->bind_param('ss', $startDate, $endDate);
        
        // Execute the prepared query.
        if ($select_stmt->execute()) {
            $result = $select_stmt->get_result();
            
            
            while ($row = $result->fetch_assoc()) {
                $fallCount += (int)$row['Fall'];
            } 
            
            $select_stmt->close();
        }
    }

    if ($select_stmt2 = $db->prepare("SELECT * FROM step WHERE Date>=? AND Date<=? ORDER BY Date")) {
        $select_stmt2->bind_param('ss', $startDate, $endDate);
        
        // Execute the prepared query.
        if ($select_stmt2->execute()) {
            $result2 = $select_stmt2->get_result();
            $i=0;
            
            while ($row2 = $result2->fetch_assoc()) {
                $stepCount += (int)$row2['step'];

                if($i == 0){
                    $start = $row2['Date'];
                }
                else{
                    $end = $row2['Date'];
                }

                $i++;
            } 
            
            $select_stmt2->close();
        }
    }

    $db->close();

    $datetime1 = strtotime($start);
    $datetime2 = strtotime($end);

    $secs = $datetime2 - $datetime1;// == <seconds between the two times>
    $days = $secs / 60;

    echo json_encode(
        array(
            "status" => "success",
            "fallCount" => $fallCount,
            "stepCount" => $stepCount,
            "minutes" => floor($days)
        )
    );  
}
else{
    echo json_encode(
        array(
            "status" => "failed",
            "message" => "Missing Parameter"
        )
    ); 
}