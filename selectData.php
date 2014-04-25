<html>
<body>
<?php

require('DatabaseModule.php');
$dbMod = new DatabaseModule();
$dbConnection = $dbMod->connect();
if($_GET["experimentNo"]){
    echo sprintf("Experiment No: %d", $_GET["experimentNo"]); 
}
if($_GET["phaseNo"]){
    echo sprintf("<p>Phase No: %d </p>", $_GET["phaseNo"]); 
}
if($_GET["sessionNo"]){
    echo sprintf("<p>SessionNo No: %d </p>", $_GET["sessionNo"]); 
}

if( $_GET["experimentNo"] && $_GET["phaseNo"] && $_GET["sessionNo"])
{
    if($stmt = $dbConnection->prepare("SELECT time, type, value FROM Event WHERE sessionID = ?"))
    {
        $stmt->bind_param("i", $_GET["sessionNo"]);
        $stmt->execute();
        $stmt->bind_result($sqlTime, $sqlType, $sqlValue);
        echo "<p>Time, Type, Value </p>";
        while ($stmt->fetch()) {
            echo sprintf("<p> %d, %d, %d </p>",$sqlTime, $sqlType, $sqlValue);
        }
        $stmt->close();	
    }
}else if( $_GET["experimentNo"] && $_GET["phaseNo"] )
{
    if($stmt = $dbConnection->prepare("SELECT sessionID, startDate FROM Session WHERE birdPhaseID = (SELECT birdPhaseID FROM BirdPhase WHERE experimentNo = ? AND phaseNo = ?)"))
    {
        $stmt->bind_param("ii", $_GET["experimentNo"], $_GET["phaseNo"]);
        $stmt->execute();
        $stmt->bind_result($sessionNo, $startDate);
        echo sprintf("<form action=\"%s\" method=\"GET\">",$_PHP_SELF);
        echo "Session <select name='sessionNo' onchange=''>";
        while ($stmt->fetch()) {
            echo "<option value=\"" . $sessionNo . "\">" . $sessionNo . "," . $startDate . "</option>";
        }
        echo "</select>";
        echo sprintf("<input type=\"hidden\" name=\"phaseNo\" value=\"%d\">", $_GET["phaseNo"]);        
        echo sprintf("<input type=\"hidden\" name=\"experimentNo\" value=\"%d\">", $_GET["experimentNo"]);        
        echo "<input type=\"submit\" />";
        echo "</form>";
        $stmt->close();	
    }
}else if($_GET["experimentNo"]){
    if($stmt = $dbConnection->prepare("SELECT phaseNo, phaseName FROM Phase WHERE experimentNo=?"))
    {
        $stmt->bind_param("i", $_GET["experimentNo"]);
        $stmt->execute();
        $stmt->bind_result($sqlPhaseNo, $sqlPhaseName);
        echo sprintf("<form action=\"%s\" method=\"GET\">",$_PHP_SELF);
        echo "Phase <select name='phaseNo' onchange=''>";
        /* populate the form*/
        while ($stmt->fetch()) {
            echo "<option value=\"" . $sqlPhaseNo . "\">" . $sqlPhaseNo . "," . $sqlPhaseName . "</option>";
        }
        echo "</select>";
        echo sprintf("<input type=\"hidden\" name=\"experimentNo\" value=\"%d\">", $_GET["experimentNo"]);        
        echo "<input type=\"submit\" />";
        echo "</form>";
        $stmt->close();	
    }
}else{
    if($stmt = $dbConnection->prepare("SELECT experimentNo, experimentName FROM Experiment"))
    {
        $stmt->execute();
        $stmt->bind_result($sqlExperimentNo, $sqlExperimentName);
        echo sprintf("<form action=\"%s\" method=\"GET\">",$_PHP_SELF);
        echo "Experiment <select name='experimentNo' onchange=''>";
        /* populate the form*/
        while ($stmt->fetch()) {
            echo "<option value=\"" . $sqlExperimentNo . "\">" . $sqlExperimentNo . "," . $sqlExperimentName . "</option>";
        }
        echo "</select>";
        echo "<input type=\"submit\" />";
        echo "</form>";
        $stmt->close();	
    }
}
?>
</body>
</html>