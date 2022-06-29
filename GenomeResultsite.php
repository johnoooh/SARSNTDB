<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="style.css" />
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
    $gene=$protein=$start=$end="";
    /*if(isset($_GET['Gene'])){
        $gene = $_GET['Gene'];
    }*/
    if(isset($_GET['Protein'])){
        $protein = $_GET['Protein'];
    }
    if(isset($_GET['Start'])){
        $start = $_GET['Start'];
    }
    if(isset($_GET['End'])){
        $end = $_GET['End'];
    }

    $q1 = "";
    /*if($gene != ""){
      $q1 .= " AND Gene_1.Gene LIKE'%" . $gene . "%'";
    } */

    if($protein != ""){
      $q1 .= " AND Gene_1.Protein = '" . $protein . "'";
    }

    if($start != "" and $end == "") {
      $q1 .= " AND " . $start . " BETWEEN Gene_1.Start AND Gene_1.End";
    }

    if($start != "" and $end != "") {
      $q1 .= " AND (Gene_1.Start BETWEEN '" . $start . "' AND '" . $end . "' OR Gene_1.End BETWEEN '" . $start . "' AND '" . $end . "') ";
    }

    if($start == "" and $end != "") {
      $q1 .= " AND " . $end . " BETWEEN Gene_1.Start AND Gene_1.End";
    }

    require_once './connection.php';
    $sql = "SELECT Gene, Protein, Accession, Start, End, Function FROM Gene_1 WHERE 1=1 $q1 ORDER BY Gene_1.Start = 0, Gene_1.Start, Gene_1.Protein";
    $result = $con->query($sql);
    if (!$result) {
      echo ("query error");
      exit();
    }
    $result_rows = $result->fetch_all(MYSQLI_ASSOC);
    $total = $result->num_rows;
  ?>
  <body>
    <h6 class="header-records">Number of records: <?= $total; ?></h6>
    <div class="datagrid">
      <table class='table'>
        <thead>
          <tr class="dark">
            <th class="header" width='10%'> </th>
            <th class="header" width='15%'>Gene</th>
            <th class="header" width='15%'>Protein</th>
            <th class="header" width='12%'>Accession</th>
            <th class="header" width='5%'>Start</th> 
            <th class="header" width='5%'>End</th>
            <th class="header" width='38%'>Description</th>
          </tr>
        </thead>
        <tbody>
          <?php
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
                $data ='<td><a onclick="getGeneDetails(\''.$row['Gene'].'\',\''.$row['Protein'].'\')">View Detail</a></td>';
                $data.='<td>'.$row['Gene'].'</td>';
                $data.='<td>'.$row['Protein'].'</td>';
                $data.='<td>'.$row['Accession'].'</td>';
                $data.='<td>'.(($row['Start']==0) ? "" : $row['Start']).'</td>';
                $data.='<td>'.(($row['End']==0) ? "" : $row['End']).'</td>';
                $data.='<td>'.$row['Function'].'</td></tr>';
                echo $data;
              }
          ?>
        </tbody>
      </table>
    </div>    
  </body>
</html>



