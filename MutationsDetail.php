<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="bootstrap.css" />
    
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <style>
      .header {
          position: sticky;
          top:0;
      }
      .datagrid {
          width: 100%;
          height: 500px;
          overflow: auto;
      }
      tr.dark th{
        background: #333;
        color: white;
      }
      th{
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
      }
      .table {
        text-align: left;
      }
    </style>
  </head>

  <?php
    $region = $referenceBase = $alternateBase = $instrument = $start = $end = "";
    if(isset($_GET['Region'])){
        $region = $_GET['Region'];
    }
    if(isset($_GET['ReferenceBase'])){
        $referenceBase = $_GET['ReferenceBase'];
    }
    if(isset($_GET['AlternateBase'])){
        $alternateBase = $_GET['AlternateBase'];
    }
    if(isset($_GET['Instrument'])){
        $instrument = $_GET['Instrument'];
    }
    if(isset($_GET['Start'])){
        $start = $_GET['Start'];
    }
    if(isset($_GET['End'])){
        $end = $_GET['End'];
    }

    $q1 = "";

    if($region != ""){
    $q1 .= " AND g.Protein = '" . $region . "'";
    }

    if($referenceBase != ""){
    $q1 .= " AND m.reference = '" . $referenceBase . "'";
    }

    if($alternateBase != ""){
    $q1 .= " AND m.alternate = '" . $alternateBase . "'";
    }

    if($start != "" and $end == "") {
    $q1 .= " AND m.coordinate >= " . $start;
    }

    if($start != "" and $end != "") {
    $q1 .= " AND m.coordinate BETWEEN " . $start . " AND " . $end;
    }

    if($start == "" and $end != "") {
    $q1 .= " AND m.coordinate <= " . $end;
    }

    if($instrument != ""){
    $q1 .= " AND m.instrument = '" . $instrument . "'";
    }

    require_once('connection.php');
    $sql = "SELECT
                distinct m.reference, m.alternate,  
                m.coordinate, g.protein, g.domain, SUM(m.mutcount) no_of_samples
            FROM mutations m
                INNER JOIN Gene_1 g ON m.coordinate BETWEEN g.start AND g.end
            WHERE 1=1 $q1
            GROUP BY m.reference, m.alternate, 
            
            m.coordinate, g.protein, g.domain 
            ORDER BY m.coordinate";
    $result = $con->query($sql);
    // echo ($sql);

    if (!$result) {
      echo ($sql);
      echo ("query error");
      exit();
    }
    $result_rows = $result->fetch_all(MYSQLI_ASSOC);
    $total = $result->num_rows;
  ?>
  <body>
    <script>
      console.log("test")
    </script>

    <div class="datagrid">
      <table class='table'>
        <thead>
          <tr class="dark">
            <th class="header" width='12%'>Coordinate</th>
            <th class="header" width='12%'>Reference Base</th>
            <th class="header" width='12%'>Alternate Base</th>
            <!--<th class="header" width='12%'>Instrument</th>-->
            
            <th class="header" width='12%'>Protein</th>
            <th class="header" width='30%'>Domain</th>
            <th class="header" width='10%'>No. of samples</th>
          </tr>
        </thead>
        <tbody>
          <?php
              $columns = array_column($result_rows, 'coordinate');
              array_multisort($columns, SORT_ASC, $result_rows);
              $color1 = 'background-color:White';
              $color2 = 'background-color:LightGray';
              $prev_color = $color1;
              foreach($result_rows as $row) {
                $data = '';
                if ($prev_color==$color1){
                  $data.= "<tr style=".$color2.">" ;
                  $prev_color=$color2;
                } elseif ($prev_color==$color2){
                  $data.= "<tr style=".$color1.">" ;
                  $prev_color=$color1;
                }
                $data.='<td>'.$row['coordinate'].'</td>';
                $data.='<td>'.$row['reference'].'</td>';
                $data.='<td>'.$row['alternate'].'</td>';
                //$data.='<td>'.$row['instrument'].'</td>';
                $data.='<td>'.$row['protein'].'</td>';
                $data.='<td>'.$row['domain'].'</td>';
                $data.='<td>'.$row['no_of_samples'].'</td>';
                $data.='</tr>';
                echo $data;
              }
          ?>
        </tbody>
      </table>
    </div>
  </body>
</html>
