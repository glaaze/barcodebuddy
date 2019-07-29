<?php
/**
 * Barcode Buddy for Grocy
 *
 * PHP version 7
 *
 * LICENSE: This source file is subject to version 3.0 of the GNU General
 * Public License v3.0 that is attached to this project.
 *
 * @author     Marc Ole Bulling
 * @copyright  2019 Marc Ole Bulling
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU GPL v3.0
 * @since      File available since Release 1.0
 */


/**
 * functions for web ui
 * 
 * @author     Marc Ole Bulling
 * @copyright  2019 Marc Ole Bulling
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU GPL v3.0
 * @since      File available since Release 1.0
 *
 */


function printMainTables() {
echo '
      <main class="mdl-layout__content">
        <div class="mdl-layout__tab-panel is-active" id="overview">
       <section class="section--center mdl-grid--no-spacing mdl-grid mdl-shadow--2dp">
            <div class="mdl-card mdl-cell  mdl-cell--12-col">
              <div class="mdl-card__supporting-text" style="overflow-x: auto; ">
                <h4>New Barcodes</h4><br>';
$barcodes = getStoredBarcodes();
if (sizeof($barcodes['known']) > 0 || sizeof($barcodes['unknown']) > 0) {
    $productinfo = getProductInfo();
}
echo generateTable($barcodes, true);
echo '
 </div>
            </div>
            <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="btn1">
              <i class="material-icons">more_vert</i>
            </button>
            <ul class="mdl-menu mdl-js-menu mdl-menu--bottom-right" for="btn1">
              <li class="mdl-menu__item" onclick="window.location.href=\''.$_SERVER['PHP_SELF'].'?delete=known\'">Delete all</li>
            </ul>
          </section>
       <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
            <div class="mdl-card mdl-cell mdl-cell--12-col">
              <div class="mdl-card__supporting-text">
                <h4>Unknown Barcodes</h4>';
echo generateTable($barcodes, false);
echo '              </div>
            </div>
            <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="btn2">
              <i class="material-icons">more_vert</i>
            </button>
            <ul class="mdl-menu mdl-js-menu mdl-menu--bottom-right" for="btn2">
              <li class="mdl-menu__item" onclick="window.location.href=\''.$_SERVER['PHP_SELF'].'?delete=unknown\'">Delete all</li>
            </ul>
          </section>
          <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
            <div class="mdl-card mdl-cell mdl-cell--12-col">
              <div class="mdl-card__supporting-text">
                <h4>Processed Barcodes</h4>';
printLog();
echo'              </div>
            </div>
            <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="btn3">
              <i class="material-icons">more_vert</i>
            </button>
            <ul class="mdl-menu mdl-js-menu mdl-menu--bottom-right" for="btn3">
              <li class="mdl-menu__item" onclick="window.location.href=\''.$_SERVER['PHP_SELF'].'?delete=log\'">Clear log</li>
            </ul>';
}

//Generate the table with barcodes
function generateTable($barcodes, $isKnown) {
    if ($isKnown) {
        if (sizeof($barcodes['known']) == 0) {
            return "No known barcodes yet.";
        } else {
            $returnString = '<form name="form" method="post" action="' . $_SERVER['PHP_SELF'] . '" >
                <table class="mdl-data-table mdl-js-data-table mdl-cell " >
                 <thead>
                    <tr>
                      <th class="mdl-data-table__cell--non-numeric">Name</th>
                      <th class="mdl-data-table__cell--non-numeric">Barcode</th>
                      <th>Quantity</th>
                      <th class="mdl-data-table__cell--non-numeric">Product</th>
                      <th class="mdl-data-table__cell--non-numeric">Action</th>
                      <th class="mdl-data-table__cell--non-numeric">Tags</th>
                      <th class="mdl-data-table__cell--non-numeric">Delete</th>
                    </tr>
                  </thead>
                  <tbody>';
            
            $returnString = $returnString . generateTableRow($barcodes, true) . '</tbody>
                </table>
                </form>';
            return $returnString;
        }
    } else {
        if (sizeof($barcodes['unknown']) == 0) {
            return "No unknown barcodes yet.";
        } else {
            $returnString = '<form name="form" method="post" action="' . $_SERVER['PHP_SELF'] . '" >
                <table class="mdl-data-table mdl-js-data-table mdl-cell " >
                 <thead>
                    <tr>
                      <th class="mdl-data-table__cell--non-numeric">Barcode</th>
                      <th class="mdl-data-table__cell--non-numeric">Look up</th>
                      <th>Quantity</th>
                      <th class="mdl-data-table__cell--non-numeric">Product</th>
                      <th class="mdl-data-table__cell--non-numeric">Action</th>
                      <th class="mdl-data-table__cell--non-numeric">Delete</th>
                    </tr>
                  </thead>
                  <tbody>';
            
            $returnString = $returnString . generateTableRow($barcodes, false) . '</tbody>
                </table>
                </form>';
            return $returnString;
        }
    }
}


//generate each row for the table
function generateTableRow($barcodes, $isKnown) {
    global $productinfo;
    $returnString = "";
    if ($isKnown) {
        foreach ($barcodes['known'] as $item) {
            $returnString = $returnString . '<tr>
        <td class="mdl-data-table__cell--non-numeric">' . $item['name'] . '</td>
              <td class="mdl-data-table__cell--non-numeric">' . $item['barcode'] . '</td>
              <td>' . $item['amount'] . '</td>
              <td class="mdl-data-table__cell--non-numeric"><select name="select_' . $item['id'] . '">' . printSelections($item['match'], $productinfo) . '</select></td>
        <td><button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" name="button_add" type="submit"  value="' . $item['id'] . '">Add</button> <button             class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" name="button_consume" type="submit" value="' . $item['id'] . '">Consume</button> </td>
        <td>' . explodeWords($item['name'], $item['id']) . '</td>
        <td><button name="button_delete" type="submit" class="mdl-button mdl-js-button mdl-js-ripple-effect" value="' . $item['id'] . '">Delete</button></td></tr>';
        }
    } else {
        foreach ($barcodes['unknown'] as $item) {
            $returnString = $returnString . '<tr>
    <td class="mdl-data-table__cell--non-numeric">' . $item['barcode'] . '</td>
          <td class="mdl-data-table__cell--non-numeric"><a href="http://google.com/search?q=' . $item['barcode'] . '" target="_blank">Search for barcode</a></td>
          <td>' . $item['amount'] . '</td>
          <td class="mdl-data-table__cell--non-numeric"><select name="select_' . $item['id'] . '">' . printSelections($item['match'], $productinfo) . '</select></td>
        <td><button class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" name="button_add" type="submit"  value="' . $item['id'] . '">Add</button> <button             class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent" name="button_consume" type="submit" value="' . $item['id'] . '">Consume</button> </td>
        <td><button name="button_delete" type="submit" class="mdl-button mdl-js-button mdl-js-ripple-effect" value="' . $item['id'] . '">Delete</button></td></tr>';
        }
    }
    return $returnString;
}


//Check if a button on the web ui was pressed and process
function processButtons() {
    global $db;
    
    if (isset($_GET["delete"])) {
        deleteAll($_GET["delete"]);
        //Hide get
        header("Location: " . $_SERVER["PHP_SELF"]);
        die();
    }
    
    if (isset($_POST["button_delete"])) {
        $id = $_POST["button_delete"];
        checkIfNumeric($id);
        deleteBarcode($id);
        //Hide POST, so we can refresh
        header("Location: " . $_SERVER["PHP_SELF"]);
        die();
    }
    
    
    if (isset($_POST["button_delete"])) {
        $id = $_POST["button_delete"];
        checkIfNumeric($id);
        deleteBarcode($id);
        //Hide POST, so we can refresh
        header("Location: " . $_SERVER["PHP_SELF"]);
        die();
    }
    
    if (isset($_POST["button_add_manual"])) {
        if (isset($_POST["newbarcodes"]) && strlen(trim($_POST["newbarcodes"])) > 0) {
            $barcodes = explode("\n", trim($_POST['newbarcodes']));
            foreach ($barcodes as $barcode) {
		$trimmedBarcode = trim(sanitizeString($barcode));
		if (strlen($trimmedBarcode)>0) {
                	processNewBarcode($trimmedBarcode, false);
		}
            }
        }
        
        //Hide POST, so we can refresh
        header("Location: " . $_SERVER["PHP_SELF"]);
        die();
    }
    
    if (isset($_POST["button_add"]) || isset($_POST["button_consume"])) {
        if (isset($_POST["button_consume"])) {
            $isConsume = true;
            $id        = $_POST["button_consume"];
        } else {
            $isConsume = false;
            $id        = $_POST["button_add"];
        }
        checkIfNumeric($id);
        $gidSelected = $_POST["select_" . $id];
        $res         = $db->query("SELECT * FROM Barcodes WHERE id='$id'");
        if ($gidSelected != 0 && ($row = $res->fetchArray())) {
            $barcode = sanitizeString($row["barcode"], true);
            $amount  = $row["amount"];
            checkIfNumeric($amount);
            foreach ($_POST["tags"][$id] as $tag) {
                $db->exec("INSERT INTO Tags(tag, itemId) VALUES('" . sanitizeString($tag) . "', $gidSelected);");
            }
            $previousBarcodes = getProductInfo(sanitizeString($gidSelected))["barcode"];
            if ($previousBarcodes == NULL) {
                setBarcode($gidSelected, $barcode);
            } else {
                setBarcode($gidSelected, $previousBarcodes . "," . $barcode);
            }
            deleteBarcode($id);
            if ($isConsume) {
                consumeProduct($gidSelected, $amount);
            } else {
                purchaseProduct($gidSelected, $amount);
            }
        }
        //Hide POST, so we can refresh
        header("Location: " . $_SERVER["PHP_SELF"]);
        die();
    }
}



function printHeader() {
    echo '<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <title>Barcode Buddy</title>

    <!-- Add to homescreen for Chrome on Android -->
<!--    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="images/android-desktop.png"> -->

    <!-- Add to homescreen for Safari on iOS -->
<!--    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Material Design Lite">
    <link rel="apple-touch-icon-precomposed" href="images/ios-desktop.png">

    <link rel="shortcut icon" href="images/favicon.png"> -->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css">
    <link rel="stylesheet" href="styles.css">

    <style>
    #add-barcode {
      position: fixed;
      display: block;
      right: 0;
      bottom: 0;
      margin-right: 40px;
      margin-bottom: 40px;
      z-index: 900;
    }
/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
    </style>

  </head>

 <body class="mdl-demo mdl-color--grey-100 mdl-color-text--grey-700 mdl-base">

<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
  <header class="mdl-layout__header">
    <div class="mdl-layout__header-row">
      <!-- Title -->
      <span class="mdl-layout-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Barcode Buddy</span>
      <!-- Add spacer, to align navigation to the right -->
      <div class="mdl-layout-spacer"></div>';
      if (USE_WEBSOCKET) {
      echo '      <nav class="mdl-navigation mdl-layout--always">
        <a class="mdl-navigation__link" target="_blank" href="./screen.php">Screen</a>
      </nav>';
      }
  echo'  </div>
  </header>
  <div class="mdl-layout__drawer">
    <span class="mdl-layout-title">Settings</span>
    <nav class="mdl-navigation">
      <a class="mdl-navigation__link" href="">General</a>
      <a class="mdl-navigation__link" href="">Tags</a>
      <a class="mdl-navigation__link" href="">Quantities</a>
      <a class="mdl-navigation__link" href="">Chores</a>
    </nav>
  </div>';
}


function printFooter() {
    global $WEBSOCKET_PROXY_URL;
    echo '
          </section>
          <section class="section--footer mdl-grid">
          </section>
        </div>
       
        <footer class="mdl-mega-footer">
          <div class="mdl-mega-footer--bottom-section">
            <div class="mdl-logo">
              More Information
            </div>
            <ul class="mdl-mega-footer--link-list">
              <li><a href="https://github.com/Forceu/barcodebuddy/">Help</a></li>
              <li><a href="https://github.com/Forceu/barcodebuddy/">Source Code</a></li>
              <li><a href="https://github.com/Forceu/barcodebuddy/blob/master/LICENSE">License</a></li>
              <li>by Marc Ole Bulling</li>
            </ul>
          </div>
        </footer>
      </main>
    </div>
<div id="myModal" class="modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Add barcode</h2>

Enter your barcodes below, one each line.&nbsp;<br><br>
<form name="form" method="post" action="' . $_SERVER['PHP_SELF'] . '" >
<textarea name="newbarcodes" id="newbarcodes" class="mdl-textfield__input" rows="15"></textarea>
<span style="font-size: 9px;">It is recommended to use a script that grabs the barcode scanner input, instead of doing it manually. See the <a href="https://github.com/Forceu/barcodebuddy" rel="noopener noreferrer" target="_blank">project website</a> on how to do this.</span><br><br><br>


<button  class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-color--accent mdl-color-text--accent-contrast" name="button_add_manual" type="submit" value="Add">Add</button>​
</form>
  </div>

</div>

 <button id="add-barcode" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-color--accent mdl-color-text--accent-contrast">Add barcode</button> 
    <script src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("add-barcode");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
document.getElementById("newbarcodes").focus();
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>';
    if (USE_WEBSOCKET) {
        echo '<script>
      var ws = new WebSocket(';
        if (!USE_SSL_PROXY) {
            echo "'ws://" . $_SERVER["SERVER_NAME"] . ":" . WEBSOCKET_PUBLIC_PORT . "/screen');";
        } else {
            echo "'" . $WEBSOCKET_PROXY_URL . "');";
        }
        echo ' 
      ws.onopen = function() {
      };
      ws.onclose = function() {
      };
      ws.onmessage = function(event) {
        window.location.reload(true); 
      };

    </script>';
    }
    echo '</body>
</html>';
}
;


//outputs stored logs to the textarea
function printLog() {
    $logs = getLogs();
    if (sizeof($logs) == 0) {
        echo "No barcodes processed yet.";
    } else {
        echo '<textarea readonly class="mdl-textfield__input" rows="15">';
        foreach ($logs as $log) {
            echo $log . "\r\n";
        }
        echo '</textarea>';
    }
}

?>