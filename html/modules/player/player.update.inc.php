<?php # player.inc.php
/**
 *  Add or update a Player.
 *  File name: player.update.inc.php
 *  @author David Demaree <dave.demaree@yahoo.com>
  *** History ***  
 * 14-03-20 Updated for phpDoc.  DHD
 * 14-03-09 Original.  DHD
 * Future:
 */
dbg("+include:" . __FILE__ . "$page_id");
function playerUpdate() {
/**
 * Add or Update                                                           
 */
    # declare globals
    global $debug, $plyr, $error_msgs;
    dbg("+".__FUNCTION__."={$_POST['member_id']}");
# initialize the player form
require(BASE_URI . "modules/player/player.form.init.php");
    $plyr->set_to_POST();   # initialize player with data from $_POST

    plyrValidate();
//  dbg("+".__FUNCTION__."={$plyr->get_member_id()}:{$error_msgs['count']}");
    if ($error_msgs['count'] == 0) {
        try {
            # is this an insert or an update?
            $row_count = $plyr->find();
            if($row_count == 0) {  
                dbg("+".__FUNCTION__.":inserting:{$plyr->get_member_id()}");
                $plyr->insert();
            } elseif($row_count == 1) {
                dbg("+".__FUNCTION__.":updating:{$plyr->get_member_id()}");
                $plyr->update();
            } else {
                $e = new Exception("Multiple ($row_count) player ({$plyr->get_member_id()}) records for effective date ({$plyr->get_eff_date()}).", 20000);
                throw new Exception($e);
            }
        }
        catch (playerException $d) {
            switch ($d->getCode()) {
            case 23110:
                $error_msgs['nickname'] = "Player with this nickname ({$plyr->get_nickname()}) already exists. ({$d->getCode()})";
                $error_msgs['errorDiv'] = "See errors below";
                $error_msgs['count'] += 1;
                break;
            case 2104: # Column validation failed before insert/update
                $err_list = array();
                $err_list[] = array();
                $error_msgs['errorDiv'] = $d->getMessage() . " (2104)";
                $err_list = $d->getOptions();
                dbg("+".__FUNCTION__.":arraysize=" . sizeof($err_list));
                foreach ($err_list as $col => $val) {
//          echo "plyr.update errors=$col:$val[0]:$val[1].<br>";
                    $error_msgs["$col"] = $val[1];
                    $error_msgs['count'] += 1;
                    dbg("+".__FUNCTION__.":err col=$col:{$error_msgs["$col"]}");
/*
                    $errMsgField="$col" . "ErrorMsg";
                    ${$errMsgField} = $val[1];
                    dbg("+".__FUNCTION__.":errMsgField=$errMsgField:${$errMsgField}");
*/
                }
                break;
            default:
                echo "plyr insert/update failed:plyr->get_member_id():" . $d->getMessage() . ":" . $d->getCode() . ".<br>";
                $p = new Exception($d->getPrevious());
                echo "plyr Previous exception:plyr->get_member_id():" . $p->getMessage() . ".<br>";
                throw new Exception($p);
            }
#      if ($d->getCode() > 0) {  # Assume that message is user-friendly
#      } else {  # Undefined error
        } 
    } 
    $_POST["ID"] = $plyr->get_member_id();
    if ($error_msgs['count'] == 0) {
        if($row_count == 0) {  
            $error_msgs['errorDiv'] = "player record added.";
        } else {
            $error_msgs['errorDiv'] = "player record updated.";
        }
    }

    dbg("-".__FUNCTION__.":end={$plyr->get_member_id()}");
# Future: Get player stats


# Show the player form
require(BASE_URI . "modules/player/player.form.php");

}


function plyrValidate() {
/**
 * Validate player data                                                   
 */
    # declare globals
    global $debug, $plyr, $player_form_fields, $error_msgs;
    dbg("+".__FUNCTION__."={$_POST['member_id']}");
#kluge!!!  Should be picked up in player.form.init.php!
$player_form_fields = array("member_id", "name_last", "name_first", "nickname");

        dbg(" ".__FUNCTION__.":player_form_fields=" . print_r($player_form_fields));
        # validate fields
        foreach ($player_form_fields as $field) {
            try {
                $func = "validate_$field";
//        dbg(" ".__FUNCTION__.":plyr:plyrUpdate:validate fields={$func}");
                $plyr->$func();
            }
            catch (playerException $e) {
                dbg(" ".__FUNCTION__.":error={$e->getMessage()}");
                $error_msgs["$field"] = $e->getMessage();
                $error_msgs['count'] += 1;
//      $error_msgs['errorDiv'] = "See errors below";
            }
        }
#    session_dump();

    dbg("-".__FUNCTION__.":end={$plyr->get_member_id()}:{$error_msgs['count']}");

}

dbg("-include:" . __FILE__ . "");
//******************************************************************************
// End of player.update.inc.php
//******************************************************************************
?>
