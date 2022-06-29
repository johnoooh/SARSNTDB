<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>SARS-CoV2 Page</title>
    <link rel="stylesheet" href="bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
      .borderless,
      .borderless > tbody > tr > td,
      .borderless > tbody > tr > th,
      .borderless > tfoot > tr > td,
      .borderless > tfoot > tr > th,
      .borderless > thead > tr > td,
      .borderless > thead > tr > th {
          border: none;
      }
      .genomeCanvas {
          border: 1px solid gray;
          background-color: lightgray;
          width: 100%;
          height: 30px;
      }
      .genome-div {
          padding: 10px;
          border: 2px solid green;
      }
    </style>
  </head>

  <?php
    if(isset($_GET['Protein'])){
        $Protein = $_GET['Protein'];
    }

    if(isset($_GET['Picture'])){
      $Picture = $_GET['Picture'];
    }

    $q1 = "";
    if($Protein != "") {
      $q1 .= " AND Gene_1.Protein = '" . $Protein . "'";
    }

    require_once './connection.php';
    $sql = "  SELECT  Gene_1.Gene, Gene_1.Protein, Gene_1.Accession, Gene_1.Motif, Gene_1.Region, Gene_1.Function, 
                      Gene_1.Start, Gene_1.End, Gene_1.Aa_count, Gene_1.Function_detail, Gene_1.Non_translated_RNA_sequence, 
                      Protein_Images.Picture, Domain.Feature, Domain.Name, Domain.Start Feature_Start, Domain.End Feature_End
              FROM Gene_1 
                LEFT JOIN Protein_Images ON (Gene_1.Protein = Protein_Images.Protein) 
                LEFT JOIN Domain ON (Gene_1.Protein = Domain.Protein) 
              WHERE 1=1 $q1
              ORDER BY Gene_1.Protein, Domain.Feature, Domain.Name";
    $result = $con->query($sql);
    if (!$result) {
      echo ("query error");
      exit();
    }
    $result_rows = $result->fetch_all(MYSQLI_ASSOC);
    
  ?>
  <body>    
    <p><b onclick="getGenomeData(false)"><u>Genome Search</u></b> -> Genome Detail </p>
    <div class="container-fluid">
      <div class="row">
        <?php  
          $predata = $functiondata = $postdata = '';
          $counter = 0;
          $prevProtein = "";
          $prevFeature = "";

          foreach($result_rows as $row) {
            if ($prevProtein != $row['Protein']) {
              $prevProtein = $row['Protein'];
              
              $counter = 0;
              
              echo $predata . $functiondata . $postdata;  

              $predata = $functiondata = $postdata = '';

              $prevFeature = '';
            }            
            
            if ($counter == 0) {
              
              $predata.='<div class="col-md-5">';
              $predata.='  <img src="data:image/png;base64,'.base64_encode($row['Picture'] ).'" width="400" height = auto />';
              
              $predata.='</div>';
              
              $predata.='<div class="col-md-6">';
              
              $predata.=' <div class="row">';
              $predata.='   <div class="genome-div"><canvas id="genomeCanvas" class="genomeCanvas"></canvas></div>';
              $predata.=' </div>';

              $predata.=' <div class="row">';
              $predata.='   <h2>'.$row['Protein'].'</h2>';
              $predata.='   <h4 style="color:#5cb85c;">'.$row['Gene'].'</h4>';
              $predata.=' </div>';
              
              $predata.=' <div class="row"><table class="table borderless">';
              $predata.='   <tr><td class="col-md-12"><b>Function Detail</b></td></tr>';
              $predata.='   <tr><td class="col-md-12">'.$row['Function_detail'].'</td></tr>';
              $predata.=' </table></div>';
              
              $predata.=' <div class="row"><table class="table borderless">';
              $predata.='   <tr><td class="col-md-2"><b>Start - End: </b></td><td><i>'.$row['Start'].' - '.$row['End'].'</i></td></tr>';
              $predata.='   <tr><td class="col-md-2"><b>Aa Count: </b></td><td><i>'.$row['Aa_count'].'</i></td></tr>';
              $predata.=' </table></div>';       
              
              $predata.=' <div class="row"><table class="table borderless">';              
            
            }            
            if ($prevFeature != $row['Feature']) {
              $prevFeature = $row['Feature'];

              if($counter != 0){
                $functiondata.='    </table></div>';
                $functiondata.='   </td></tr>';                
              }
              
              $functiondata.='   <tr><td class="col-md-12"><b>'.$row['Feature'].'</b></td></tr>';
              $functiondata.='   <tr><td class="col-md-12"><div><table class="table table-bordered">';
              $functiondata.='      <tr>';
              $functiondata.='        <td class="col-md-5"><i>'.$row['Name'].'</i></td>';
              $functiondata.='        <td class="col-md-5"><i>'.$row['Feature_Start'].' - '.$row['Feature_End'].'</i></td>';
              $functiondata.='      </tr>';
            } else {
              $functiondata.='      <tr>';
              $functiondata.='        <td class="col-md-5"><i>'.$row['Name'].'</i></td>';
              $functiondata.='        <td class="col-md-5"><i>'.$row['Feature_Start'].' - '.$row['Feature_End'].'</i></td>';
              $functiondata.='      </tr>';
            }                        
            
            if ($counter == 0) {
              $postdata.='  </table></div>';

              $postdata.='  <div class="row"><table class="table borderless">';
              $postdata.='    <tr><td class="col-md-12"><b>Non Translated RNA Sequence</b></td></tr>';
              $postdata.='    <tr><td class="col-md-12">'.$row['Non_translated_RNA_sequence'].'</td></tr>';
              $postdata.='  </table></div>';

              $postdata.='</div';
            }
            
            $counter++;              
          }
          echo $predata . $functiondata . $postdata;
        ?>
      </div>
    </div>
  </body>
  <script type="text/javascript" src="JS\domainPlotter.js"></script> 
  <script type="text/javascript">

    

    var color = '#606300';
    for(var i=0; i<1; ) {

    const rndInt = Math.floor(Math.random() * 10) + 1;

    if(i%3 == 0) {
        color = '#0000ff';
    }
    else if (i%3 == 1) {
        color = '#ff0000';
    }
    else if (i%3 == 2) {
        color = '#00ff00';
    }
    i = i + rndInt;
    drawLine(i+10, i+200, i+10, 0, '#d8eb34', 500);

    //rndInt = Math.floor(Math.random() * 10) + 1;

    drawLine(120, i+200, 120, 0, '#ff0000', 30);
    //drawRect(i+10, i+200, rndInt, 100, color);

    }
</script>
</html>
ß