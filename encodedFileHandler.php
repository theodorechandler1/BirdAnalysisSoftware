<?php
class converter {
    private $experimentNo = null;
    private $phaseNo = null;
    private $fileName = null;
    private $fileHandle = null;
    private $birdNo = null;
    private $startDate = null;
    private $weight = null;
    private $box = null;
    private $id = null;
    private $iType = null;
    private $iData = null;
    private $birdTime = null;
    
    public function openFile( $fileName, $experimentNo = null, $phaseNo = null ) { #TODO: File checking + ensuring that the file is only from a certain path
        $this->experimentNo = $experimentNo
        $this->phaseNo = $phaseNo
        $this->fileName = $fileName;
        $this->fileHandle = fopen($fileName, "rb");
        #Headder information
        $this->birdNo = $this->get16();
        $dateTimeTemp = DateTime::createFromFormat( 'U', $this->get32() ); #Grab the unix encoded timestamp and convert it into MM-DD-YYYY HH:MM:SS
        $this->startDate = $dateTimeTemp->format( 'm-d-Y H:i:s' );
        $this->weight = $this->get16();
        $this->box = $this->get16();
        $this->id = $this->get32();
        
        #Initialize arrays
        $this->iType = array();
        $this->iData = array();
        $this->birdTime = array();
        $iType = null;
        $iData = null;
        $birdTime = null;
        
        while($iType != 5 and $iType != 8){ #While we do not find program end (5) or error condition (8) continue reading the file
            $iType = $this->get8();
            $iData = $this->get8();
            $birdTime = $this->get32();
            #Store data here
            array_push($this->iType, $iType);
            array_push($this->iData, $iData);
            array_push($this->birdTime, $birdTime);
            
        }
        fclose($this->fileHandle); #Make sure we tidy up
        
    }
    /*
        Private Method, this should never need to be used by external classes
        input 8 bits of data from file
    */
    private function get8(){
        if(isset($this->fileHandle)){
            $data = fread($this->fileHandle, 1);
            return unpack("C", $data)[1];
        }
    }
    /*
        Private Method, this should never need to be used by external classes
        input 16 bits of data from file (2 bytes)
    */
    private function get16(){
        if(isset($this->fileHandle)){
            $data = fread($this->fileHandle, 2);
            return unpack("v" ,$data)[1]; 
        }
    }
    /*
        Private Method, this should never need to be used by external classes
        input 32 bits of data from file (4 bytes)
    */
    private function get32(){
        if(isset($this->fileHandle)){
            $data = fread($this->fileHandle, 4);
            return unpack("I", $data)[1];
        }
    }
    
    public function getSessionNo(){
        $sessionNo = explode(".", $fileName)[0];
        return $sessionNo;
    }
    
    public function printClass(){
        echo "Experiment No: $this->experimentNo()";
        echo "Session No: $this->getSessionNo()<br>"; 
        echo "Bird #: $this->birdNo<br>";
        echo "Date: $this->startDate <br>";
        echo "Weight: $this->weight<br>";
        echo "Box: $this->box<br>";
        echo "ID: $this->id<br>";
        print_r($this->iType);
        print_r($this->iData);
        print_r($this->birdTime);
    }
    
    public function uploadData(){
        require_once('DatabaseModule.php');
        $dbMod = new DatabaseModule();
        $dbConnection = $dbMod->connect();
        #Check to see if Experiment Exists
        if($stmt = $dbConnection->prepare("SELECT COUNT(experimentNo) FROM Experiment WHERE experimentNo = ?"))
        {
            $stmt->bind_param("i", $this->experimentNo);
            $stmt->execute();
            $stmt->bind_result($experimentCount);
            if($experimentCount == 0){
                #Create experiment
            }
        }
        #Check to see if Phase Exists
        if($stmt = $dbConnection->prepare("SELECT COUNT(phaseNo) FROM Experiment WHERE experimentNo = ? and phaseNo = ?"))
        {
            $stmt->bind_param("ii", $this->experimentNo, $this->phaseNo);
            $stmt->execute();
            $stmt->bind_result($phaseCount);
            if($phaseCount == 0){
                #Create Phase
            }
        }
        #Check to see if Bird Exists
        if($stmt = $dbConnection->prepare("SELECT COUNT(birdNo) FROM Bird WHERE birdNo = ?"))
        {
            $stmt->bind_param("i", $this->birdNo);
            $stmt->execute();
            $stmt->bind_result($birdCount);
            if($birdCount == 0){
                #Ask for data about bird
            }
        }
        #Check to see if session exists
        if($stmt = $dbConnection->prepare("SELECT COUNT(sessionID) FROM Session WHERE (SELECT birdPhaseID FROM BirdPhase WHERE experimentNo = ? AND phaseNo = ? AND birdNo = ?) = birdPhaseID AND sessionID = ?"))
        {
            $stmt->bind_param("iiii", $this->experimentNo, $this->phaseNo, $this->birdNo, $this->getSessionNo());
            $stmt->execute();
            $stmt->bind_result($sessionIDCount);
            if($sessionIDCount != 0){
                echo "Complain that the session already exists";
                #Complain that the session already exists
            }else{
                #Create session
                if($stmt = $dbConnection->prepare("INSERT INTO Session (`birdPhaseID`, `sessionID`, `startDate`, `boxNo`, `birdWeight`) VALUES ((SELECT birdPhaseID FROM BirdPhase WHERE experimentNo = ? AND phaseNo = ? AND birdNo = ?), '?', '?', '?', '?');"))
                {
                    $stmt->bind_param("iiiisii", $this->experimentNo, $this->phaseNo, $this->birdNo, $this->getSessionNo(), $this->startDate, $this->box, $this->weight);
                    $stmt->execute();
                }
            }
        }
        #For loop here
        
        #Check to see if event exists
        #Create events
    }
}
#$filename = "302.001";
#$instance = new converter();
#$instance->openFile($filename);
#$instance->printClass();
#$instance->addToDatabase("1","1");



?>
