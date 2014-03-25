<?php
/**
 *  Class definition for a seat
 *  File name: Seat.php
 *  @author David Demaree <dave.demaree@yahoo.com>
 *** History ***
 * 14-03-23 Added dbg().  DHD
 * 14-03-21 Cloned from Member.  DHD
 */

class Seat
{

/**
 *Attributes
 */
    protected $game_id;
    protected $member_id;
    protected $response;
    protected $note_member;
    protected $note_master;
    protected $stamp;

// List of names of SQL columns for the seats table
    private $GAME_TABLE_COLUMNS = array("game_id", "member_id", "response", 
                                        "note_member", "note_master", 
                                        "stamp");

/*
 * Constructor
 */
    function __construct()
    {
        
#    dbg("=".__METHOD__.";");
        $this->game_id = null;
        $this->member_id = null;
        $this->response = null;
        $this->note_member = null;
        $this->note_master = null;
        $this->stamp = null;
    }

/**
 * Getters
 */
    public function get_game_id() { return $this->game_id; }
    public function get_member_id() { return $this->member_id; }
    public function get_member_name() { return date('stub'); }
    public function get_response() { return ($this->response); }
    public function get_note_member() { return $this->note_member; }
    public function get_note_master() { return $this->note_master; }
    public function get_stamp() { return $this->stamp; }

/**
 * Setters
 */
    public function set_game_id($P) { $this->game_id = $P; }
    public function set_member_id($P) { $this->member_id = $P; }
    public function set_response($P) { $this->response = $P; }
    public function set_note_member($P) { $this->note_member = $P; }
    public function set_note_master($P) { $this->note_master = $P; }
    public function set_stamp($P) { $this->stamp = $P; }

/**
 * Validation for individual column values.
 */
    public function validate_game_id() {
        # numeric
        # !> highest existing game_id + 1
//    dbg("=".__METHOD__.":Seat.validate_game_id=$this->game_id");
        $e = array(0,"");
        return($e);
    }

    public function validate_member_id() {
        # "S" or "M"
        $e = array(0,"");
        return($e);
    }

    public function validate_response() {
        # string
        # alphabetic/spaces, starts with capital?, 
//  public function validate_response() { if(is_string($P)) { $this->response = (string)$P; } }
        $e = array(0,"");
        return($e);
    }

    public function validate_note_member() {
        # string
        # alphabetic/spaces, starts with capital?, 
        $e = array(0,"");
        return($e);

    }

    public function validate_note_master() {
        # string
        # alphabetic/spaces, starts with capital?, 
        $e = array(0,"");
        return($e);

    }

    public function validate_stamp() {
        # string
        # alphabetic/spaces, starts with capital?, 
        $e = array(0,"");
        return($e);

    }

/**
 * Validate seats fields
 */
    public function validate()
    {
        
        dbg("+".__METHOD__."={$this->game_id}");
        $errors = array();
        $foo = array();
#    global $GAME_TABLE_COLUMNS;
        # validate fields
        foreach ($this->GAME_TABLE_COLUMNS as $column) {
            $func = "validate_$column";
//      dbg("=".__METHOD__.":Seat.validate column={$func}");
            $foo = $this->$func();
//      dbg("=".__METHOD__.":Seat.validate col:$column="; var_dump($foo); echo "");
            if ($foo[0]) {
                $errors["$column"][0] = $foo[0];
                $errors["$column"][1] = $foo[1];
//        dbg("=".__METHOD__.":Seat.validate col:$column:$foo[0]:$foo[1]");
            }
        }
//        if ($debug) {
//        foreach ($errors as $col => $val) {
//      echo "Seat.validate errors=$col:"; list($n,$s) = $val; echo "$n:$s");
//            echo "Seat.validate errors=$col:$val[0]:$val[1]");
//        }
        dbg("-".__METHOD__.":error arraysize=".sizeof($errors));
        return($errors);
    }

/**
 * create a list of comma-separated column names for SQL statements.          
 */
    private function sql_column_name_list() {
        
        $list = "";
        foreach ($this->GAME_TABLE_COLUMNS as $item) {
            if ($item != "stamp") { # stamp must be null for auto-update
                $list .= "$item, ";
            }
        }
        $list = rtrim($list, ", ");
//    dbg("=".__METHOD__.":Seat:sql_column_name_list()=$list");
        return $list;
    }  

/**
 * create a list of quoted, comma-separated column values for SQL statements. 
 */
    private function sql_column_value_list() {
        
        $list = "";
        foreach ($this->GAME_TABLE_COLUMNS as $item) {
            if ($item != "stamp") { # stamp must be null for auto-update
                $list = $list . "\"{$this->$item}\", ";
            }
        }
        $list = rtrim($list, ", ");
//    dbg("=".__METHOD__.":Seat:sql_column_value_list()=$list");
        return $list;
    }  


/**
 * create a list of pairs of column names with values, used for SQL INSERT    
 */
    private function sql_column_name_value_pairs() {
        
        $list = "";
        foreach ($this->GAME_TABLE_COLUMNS as $item) {
            if ($item == "stamp") { # stamp must be null for auto-update
                $list = $list . "$item = NULL, ";
            } else {
                $list = $list . "$item = \"{$this->$item}\", ";
            }
        }
        $list = rtrim($list, ", ");
        dbg("=".__METHOD__."=$list");
        return $list;
    }  

/**
 * set the data seats of this seat to the values found in $_POST.       
 */
    public function set_to_POST()
    {
    $this->set_game_id($_POST['game_id']);
    $this->set_member_id($_POST['member_id']);
    $this->set_response($_POST['response']);
    $this->set_note_member($_POST['note_member']);
    $this->set_note_master($_POST['note_master']);
    $this->set_stamp($_POST['stamp']);
    }

/**
 * get a seat row by game_id.                   
 */
    public function get($getType)
    {
        $this_game = "game_id = (\"$this->game_id\"";
        $prev_game = "game_id = ("
                     .   "SELECT MAX(game_id) FROM seats "
                     .   "WHERE game_id < \"$this->game_id\") ";
        $next_game = "game_id = ("
                     .   "SELECT MIN(game_id) FROM seats "
                     .   "WHERE game_id > \"$this->game_id\") ";
        $first_player = "member_id = (SELECT MIN(member_id) FROM seats ";
        $last_player  = "member_id = (SELECT MAX(member_id) FROM seats ";
        $next_player  = "member_id = (SELECT MIN(member_id) FROM seats "
                        .   "WHERE member_id > \"$this->member_id\" ";

        
        dbg("+".__METHOD__ . "={$getType}");
        try {
require(BASE_URI . "includes/pok.open.inc.php");
            switch ($getType) {
                case 'preg': 
                    $query = "SELECT * FROM seats " .
                             # previous game
                             "WHERE game_id = (" . 
                                 "SELECT MAX(game_id) FROM seats " . 
                                 "WHERE game_id < \"$this->game_id\") " .
                             # last player
                             "AND member_id = (" . 
                                 "SELECT MAX(member_id) FROM seats " . 
                                 # previous game
                                 "WHERE game_id = (" . 
                                     "SELECT MAX(game_id) FROM seats " . 
                                     "WHERE game_id < \"$this->game_id\")) ";
                    break;
                case 'prep': 
                    $query = "SELECT * FROM seats " .
                             # this game
                             "WHERE game_id = \"$this->game_id\" " .
                             # previous player
                             "AND member_id = (" . 
                                 "SELECT MAX(member_id) FROM seats " . 
                                 "WHERE member_id < \"$this->member_id\" " .
                                 # this game
                                 "AND game_id = \"$this->game_id\")";
                    break;
                case 'nexg':  
                    $query = "SELECT * FROM seats " . 
                             # next game
                             "WHERE game_id = (" . 
                                 "SELECT MIN(game_id) FROM seats " . 
                                 "WHERE game_id > \"$this->game_id\") " .
                             # first player
                             "AND member_id = (" . 
                                 "SELECT MIN(member_id) FROM seats " . 
                                 # next game
                                 "WHERE game_id = (" . 
                                     "SELECT MIN(game_id) FROM seats " . 
                                     "WHERE game_id > \"$this->game_id\")) ";
                    break;
                case 'nexp':
                    $query = "SELECT * FROM seats " . 
                             # this game
                             "WHERE game_id = \"$this->game_id\" " .
                             # next player
                             "AND member_id = (" . 
                                 "SELECT MIN(member_id) FROM seats " . 
                                 "WHERE member_id > \"$this->member_id\" " .
                                 # this game
                                 "AND game_id = \"$this->game_id\") ";
                    break;
                default:
                    $query = "SELECT * FROM seats " . 
                             "WHERE game_id = \"$this->game_id\" " . 
                             "AND member_id = \"$this->member_id\" ";
                    break;
            }
            # get seats row
            dbg("=".__METHOD__.";query=$query");
            $stmt = $pokdb->prepare($query);
            $stmt->execute();
            $row_count = $stmt->rowCount();
#            dbg("=".__METHOD__.":Seat:get:$this->game_id:rows=$row_count");
            if ($row_count == 1) {
                $row = $stmt->fetch();
                $this->setThisToSeatRow($row);
                #echo "row:"; var_dump($row); echo ".<br>";
            } elseif ($row_count < 1) {
#                dbg("=".__METHOD__.":Seat:get=seat not found");
                #error_log($e->getTraceAsString());
                if ($getType == 'prev') {
                    throw new seatException('No previous seat for ID ' . $this->game_id . ' found', 32210);
                } elseif ($getType == 'next') {
                    $this->getNew();
                    throw new seatException('Add new seat (' . $this->game_id . ')', 32213);
                } else {
                    throw new seatException('No seat found with this ID (' . $this->game_id . ')', 32212);
                }
            } else {
#                dbg("=".__METHOD__.":Seat:get=multiple seat records found");
                #error_log($e->getTraceAsString());
                throw new seatException('Multiple records for this seat were found', 32211);
            }
        } catch (PDOException $e) {
            echo "PDO Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";
            throw new seatException('mySQL Error', 32212);
//    } catch (Exception $e) {
//      echo "Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>"; 
//      rethrow??? 
        }
        dbg("-".__METHOD__ . "={$this->game_id}");
    }
/**
 * populate a seat object with data from a seat table row        
 */
    protected function setThisToSeatRow($row) 
    {
        $this->game_id = $row['game_id'];
        $this->member_id = $row['member_id'];
        $this->response = $row['response'];
        $this->note_member = $row['note_member'];
        $this->note_master = $row['note_master'];
        $this->stamp = $row['stamp'];
    }

/**
 * Get the next available seat number and date
 */
    public function getNew()
    {
        
        dbg("+".__METHOD__.";");
        try {
require(BASE_URI . "includes/pok.open.inc.php");
            # get seat
            $stmt = $pokdb->prepare("SELECT game_id, member_id FROM seats WHERE game_id =  (SELECT MAX(game_id) FROM seats) ");
            $stmt->execute();
            $foo = array($stmt->fetch());
#var_dump($foo); echo "<br>";
//            $this->game_id = $stmt->fetchColumn(0) + 1;
//            $this->member_id = $stmt->fetchColumn();
            $prev_game_id = $foo[0][0];
            $this->game_id = $prev_game_id + 1;
            $prev_member_id = $foo[0][1];
            $phpdate = strtotime( $prev_member_id );
            $dayOfMonth = date( 'd', $phpdate );
#            echo "Seat date:$this->member_id:$phpdate:dayOfMonth:$dayOfMonth.<br>";
            if ($dayOfMonth < 15 ) { # it's the 1st seat of the month (Wed) so calc 4th Friday
                $baseDate = date("Y-m-", $phpdate) . "01";
                $nextDate = date("Y-m-d", strtotime("4 weeks friday", strtotime($baseDate)));
#                echo "nextDate:$nextDate:$baseDate.<br>";
            } else { # it's a Friday so...) calc 2nd Wed.
                $nextDate = date("Y-m-d", strtotime("2 weeks wednesday", $phpdate));
#                echo "nextDate:$nextDate.<br>";
            }
            $this->member_id = $nextDate;
            #$wed = date("Y-m-d", strtotime("2 weeks wednesday",mktime(0,0,0,11,1,2014)));
            #$wed = date("Y-m-d", strtotime("2 weeks wednesday", $phpdate));
            #echo "2nd Wed:$wed=" . date('l', strtotime($wed)) . ".<br>";


//$month = date("M", $this->member_id);
//echo "Month:$month.<br>";

            #$tomorrow  = date('F jS, Y = l', mktime(0, 0, 0, date("m", $phpdate)  , date("d", $phpdate)+1, date("Y", $phpdate)));
            #echo "Tomorrow:$tomorrow.<br>";

            #echo $this->member_id . ":" . date('l', strtotime( $this->member_id)) . "<br>";
#":" . date_format($this->member_id, 'Y-m-d H:i:s') . 

            #$tempDate = date('F jS, Y = l', strtotime(" next wednesday {$this->member_id}"));
            #echo $tempDate . "<br>";


#      dbg("=".__METHOD__.":$this->game_id");
        } catch (PDOException $e) {
            echo "PDO Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";
        } catch (Exception $e) {
            echo "Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";  
        }
        dbg("-".__METHOD__.";{$this->game_id}:{$this->member_id}");
    }


/**
 * List a seat
 */
    public function listing()
    {
        echo "Seat." . __FUNCTION__ . ".<br>";
        $this->listIt(".<br>");
    }

    public function listRow()
    {
        echo "Seat." . __FUNCTION__ . ":";
        $this->listIt("; ");
        echo ".<br>";
    }

    private function listIt($d)
    {
        echo "Seat_id=$this->game_id$d";
        echo "member_id=$this->member_id$d";
        echo "note_member=$this->note_member$d";
        echo "response=$this->response$d";
        echo "note_master=$this->note_master$d";
        echo "stamp=$this->stamp$d";
    }

    public function dump()
    {
        echo "Seat.dump.<br>";
        var_dump($this);
        echo ".<br>";
    }

/**
 * find a seats row by game_id.
 */
    public function find()
    {
        
        $row_count = -1;
        dbg("+".__METHOD__ . "={$this->game_id}");
        try {
require(BASE_URI . "includes/pok.open.inc.php");
            # find seat rows
            $query = "SELECT * FROM seats " . 
                                  "WHERE game_id = \"$this->game_id\"  ";
            dbg("=".__METHOD__ . ":query=$query");
            $stmt = $pokdb->prepare($query);
            $stmt->execute();
            $row_count = $stmt->rowCount();
            dbg("=".__METHOD__ . ":$this->game_id:rows=$row_count");
        } catch (PDOException $e) {
            echo "PDO Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";
            throw new seatException('PDO Exception', -32010, $e);
//    } catch (Exception $e) {
//      echo "Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>"; 
//      rethrow??? 
        }
        dbg("-".__METHOD__ . "={$this->game_id}=$row_count");
        return($row_count);
    }

/**
 * insert a seats row from a seat object
 */
    public function insert()
    {
        
        $val_errors = array ();
        dbg("+".__METHOD__.";$this->game_id:{$this->sql_column_name_value_pairs()}");
        $val_errors = ($this->validate());
        if (sizeof($val_errors) == 0 ) {
            try {
require(BASE_URI . "includes/pok.open.inc.php");
                # insert seat
                $query = "INSERT INTO seats ({$this->sql_column_name_list()}) " .
                  "VALUES ({$this->sql_column_value_list()})" ;
                dbg("=".__METHOD__.":Seat:find:query=$query");
                $stmt = $pokdb->prepare($query);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "Seat.insert: PDO Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";
                throw new seatException('Unknown error', -32110, $e);
            } catch (Exception $e) {
                echo "Seat.insert: Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";  
                throw new seatException($e);
            }
        } else {
            throw new seatException("Data validation errors", 2104, null, $val_errors);
        }
//    dbg("=".__METHOD__.":Seat added");
//    $inserted_game_id = $pokdb->lastInsertId(); 
//    dbg("=".__METHOD__.":Seat number:$inserted_game_id");
        dbg("-".__METHOD__.";$this->game_id:".sizeof($val_errors)."");
    }

/**
 * update a seats row from a seat object                         
 */
    public function update()
    {
        
        dbg("+".__METHOD__."=$this->game_id");
        $val_errors = $this->validate();
//    dbg("=".__METHOD__.":Seat.update error list size:"; echo sizeof($val_errors); echo "");
        if (sizeof($val_errors) == 0 ) {
            try {
# open database
require(BASE_URI . "includes/pok.open.inc.php");
                # update seat
                $update = "UPDATE seats SET {$this->sql_column_name_value_pairs()} " . 
                      " WHERE game_id = \"{$this->game_id}\" ";
                dbg("=".__METHOD__.";stmt_str=$update");
                $stmt = $pokdb->prepare($update);
                $stmt->execute();
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    #error_log($e->getTraceAsString());
                    throw new seatException('Duplicate entry', 32110, $e);
                } else {
                    echo "Seat.update: PDO Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";
                    throw new seatException('Unknown error', -32110, $e);
                }
            } catch (Exception $e) {
                echo "Seat.update: Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";  
                throw new seatException($e);
            }
        } else {
            throw new seatException("Data validation errors", 32104, null, $val_errors);
        }
        dbg("-".__METHOD__.";$this->game_id:".sizeof($val_errors)."");
    }
/**
 * delete all seats and seats rows for a seat                         
 */
    public function delete()
    {
        $deleted_seats = 0;
        $deleted_members = 0;
        dbg("+".__METHOD__.";$this->game_id");
//        dbg("=".__METHOD__.":player.delete error list size:"; echo sizeof($val_errors); echo "");
//        if (sizeof($val_errors) == 0 ) {
            try {
require(BASE_URI . "includes/pok.open.inc.php");
                # delete attendance
                $delete = "DELETE FROM seats " . 
                      " WHERE game_id = \"{$this->game_id}\" ";
                dbg("=".__METHOD__.":Seat::" . __FUNCTION__ . ":stmt_str=$delete");
                $stmt = $pokdb->prepare($delete);
                $stmt->execute();
                $deleted_seats = $stmt->rowCount();
                $delete = "DELETE FROM seats " . 
                      " WHERE game_id = \"{$this->game_id}\" ";
                dbg("=".__METHOD__.":Seat::" . __FUNCTION__ . ":stmt_str=$delete");
                $stmt = $pokdb->prepare($delete);
                $stmt->execute();
                $deleted_seats = $stmt->rowCount();
            } catch (PDOException $e) {
                echo "Seat.delete: PDO Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";
                throw new seatException('Unknown error', -32110, $e);
            } catch (Exception $e) {
                echo "Seat.delete: Exception: " . $e->getCode() . ": " . $e->getMessage() . "<br>";  
                throw new seatException($e);
            }
//        } else {
//            throw new playerException("Data validation errors", 2104, null, $val_errors);
//        }
        dbg("-".__METHOD__.";$this->game_id");
    }

/**
 * create a fictitious seat for test purposes                         
 */
    function testSeat()
    {
        
        dbg("+".__METHOD__."");
        # id, date
        $this->getNew();
        # get highest player_id
        $testPlay = new Player();
        $testPlay->get_next_id();
        $high = $testPlay->get_member_id();
        $this->response = rand(1,$high);
        $this->note_member = rand(1,$high);
        $this->note_master = rand(1,$high);
//        $this->member_id = rand(1,$high);
        dbg("=".__METHOD__.":Seat::" . __FUNCTION__ . ":end:high=$high:{$this->game_id}:{$this->member_id}:{$this->response}:{$this->note_member}:{$this->note_master}");
        unset($testy);
        dbg("-".__METHOD__."");
/*
        # response
require("../inc/testdb_open.php"); #
        $nameCount = $this->getRowCount($testdb, "family_names");
        $nameId = rand(1, $nameCount);
        $query="SELECT * FROM family_names WHERE name_id = $nameId";
//    echo "testSeat family_names query=$query.<br>";
        $stmt = $testdb->prepare($query);
        $stmt->execute();
        $row_count = $stmt->rowCount();
        dbg("=".__METHOD__.":Seat.testSeat rows:$row_count");
        if ($row_count == 1) {
            $row = $stmt->fetch();
            $this->response = $row['response'];
#      echo "Seat.testSeat response=$this->response.<br>";
        } else {
            echo "testSeat __construct response error: Too many rows:$row_count.<br>";
            throw new Exception('testSeat response SELECT error: Too many rows', -1);
        }
        # note_member
        $gender = rand(0, 1); 
        if ($gender) {
            $table_name = "male_names";
        } else {
            $table_name = "female_names";
        }
        $nameCount = $this->getRowCount($testdb, $table_name);
        $nameId = rand(1, $nameCount); # Pick random male, female
        $query = "SELECT note_member FROM $table_name WHERE name_id = $nameId";
        $stmt = $testdb->prepare($query);
#    dbg("=".__METHOD__.":Seat.testSeat stmt:"; $stmt->debugDumpParams(); echo "<br>"; }
        $stmt->execute();
        $row_count = $stmt->rowCount();
//    dbg("=".__METHOD__.":Seat.testSeat rows:$row_count");
        if ($row_count == 1) {
            $row = $stmt->fetch();
            $this->note_member = $row['note_member'];
//      echo "Seat.testSeat note_member=$this->note_member.<br>";
        } else {
            echo "testSeat __construct note_member error: Too many rows:$row_count.<br>";
            throw new Exception('testSeat note_member SELECT error: Too many rows', -1);
        }

#  echo "Next this:{$this->get_game_id()}.<br>";
        $this->set_member_id("M");
*/

    }


/**
 * get the number of rows in a table from the test database                         
 */
/*
    private function getRowCount($testdb, $table_name)
    {
        # Get number of rows to choose from in table
        $nameCount = 0;
        $sql = "SELECT COUNT(*) FROM $table_name";
        if ($res = $testdb->query($sql)) {
            # Check the number of rows that match the SELECT statement
            $nameCount = $res->fetchColumn();
        }
        #echo "testdata $table_name count=$nameCount.<br>";
        return ($nameCount);
    }
*/
} 

//******************************************************************************
// end class Seat
//******************************************************************************
class SeatException extends Exception
{
#    
    private $_options = array();
    // Redefine the exception so message isn't optional
    public function __construct($message, 
                                $code = 0, 
                                Exception $previous = null,
                                $options = array('params')) {
#        dbg("=".__METHOD__.":SeatException={$message}:$code");
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);

            $this->_options = $options;

    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function GetOptions() { 
#    dbg("=".__METHOD__.":SeatException:GetOptions="; echo sizeof($this->_options); echo "");
    return $this->_options; 
    }
}

?>
