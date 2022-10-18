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
    <script src="./sortable.js"></script>
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
                m.coordinate, g.protein, g.domain, SUM(m.mutcount) no_of_samples, g.protSeq, g.RNA_sequence, g.Start
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

      function copyFunction(prot,seq) {
        // Get the text field
        var copyText = ">"+prot+"\n"+seq;

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        // Alert the copied text
        alert("Copied the text: " + copyText.value);
      }
    </script>
    <div class="datagrid">
      <table class='sortable' >
        <thead>
          <tr class="dark">
            <th  width='12%'>Coordinate</th>
            <th width='12%'>Reference Base</th>
            <th width='12%'>Alternate Base</th>
            <!--<th class="header" width='12%'>Instrument</th>-->
            
            <th class="no-sort" width='12%'>Protein</th>
            <th class="no-sort" width='30%'>Amino Acid Change</th>
            <th width='10%'>No. of Samples</th>
            <th width='10%'>%    Containing Mutation</th>
            <th width='10%'>SNAP2 Analysis</th>
            
          </tr>
        </thead>
        
        <tbody>
        
          <?php
              $columns = array_column($result_rows, 'coordinate');
              array_multisort($columns, SORT_ASC, $result_rows);
              $color1 = 'background-color:White';
              $color2 = 'background-color:LightGray';
              $prev_color = $color1;
              $aminoacids=array("F","L","I","M","V","S","P","T","A","Y","*","H","Q","N","K","D","E","C","W","R","G","X");

              $triplets=array("(TTT |TTC )","(TTA |TTG |CT. )","(ATT |ATC |ATA )","(ATG )","(GT. )","(TC. |AGT |AGC )",
              "(CC. )","(AC. )","(GC. )","(TAT |TAC )","(TAA |TAG |TGA )","(CAT |CAC )",
              "(CAA |CAG )","(AAT |AAC )","(AAA |AAG )","(GAT |GAC )","(GAA |GAG )","(TGT |TGC )",
              "(TGG )","(CG. |AGA |AGG )","(GG. )","(\S\S\S )");

              $copyFasta = "";
              foreach($result_rows as $row) {

                $mutGeneCoord = $row['coordinate']-$row['Start'];
                $newseq = substr_replace($row['RNA_sequence'], $row['alternate'], $mutGeneCoord,1);

                $temp = chunk_split($newseq,3,' ');
                $peptide = preg_replace ($triplets, $aminoacids, $temp);
                $length = strlen($row['protSeq']);
                
                // $change="No change";

                if ($peptide == $row['protSeq']){
                  $change="Synonymous change";
                }else{
                  #checks for what kind of variant it is 
                  for ($index = 0; $index < $length; $index++) {
           
                    $newAA = $peptide[$index];                      
                    $canonAA=$row['protSeq'][$index];
                    
                    if ($newAA==$canonAA){

                    }else{
                      if ($newAA== "*"){
                        $change="Nonsense Variant";
                        break;
                      }else{
                        $change="Missense Variant: p.";
                        $change.=$canonAA;
                        $change.=$index+1;
                        // $change.="|||||||||||||||||";

                        $change.=$newAA;
                        // $change.="  |  ";

                        $orf1ansp = ['Nsp1',
                        'Nsp2',
                        'Nsp3',
                        'Nsp4',
                        'Nsp5',
                        'Nsp6',
                        'Nsp7',
                        'Nsp8',
                        'Nsp9',
                        'Nsp10',
                        'Nsp11',];
                        $orf1bnsp = ['Nsp12',
                        'Nsp13',
                        'Nsp14',
                        'Nsp15',
                        'Nsp16'];


                        $abbvProts = ["Surface Glycoprotein","Envelope Membrane Protein", "Membrane Protein","Nucleocapsid proteins"];

                        #below is not really used, I disabled the covarniant link as it doesnt work for 99% of mutations
                        if (in_array($row['protein'],$abbvProts)){
                          $tmp_prot = substr($row['protein'], 0,1);
                          $covariantlink=$tmp_prot;
                          $ind1 = $index+1;
                          $covariantlink.=".".$canonAA.$ind1;
                        }else if (in_array($row['protein'],$orf1ansp)){
                          $covariantlink="";
                        }else if (in_array($row['protein'],$orf1bnsp)){
                          $covariantlink="";
                        }else{
                          $tmp_prot = $row['protein'];
                          $covariantlink=$tmp_prot;
                          

                          $ind1 = $index+1;
                                                }
                          
                        // $covariantlink = "";
                        // $change.='<a href="https://covariants.org/variants/'.$covariantlink.'">Try your luck on Covariant</a>';
                        break;
                      }
                    }
  
                  }
                }

                // for ($index = 0; $index < $length; $index++) {
                  // $triplet = substr($newseq,$index,$index+3);
                  // $peptide = preg_replace ($triplets[$genetic_code], $aminoacids, $temp);

                // }
                $total_samples= 18900;
                $percentage= round(($row["no_of_samples"]/18900)*100,2);
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
                $data.='<td>'.$change.'</td>';
                $data.='<td>'.$row['no_of_samples'].'</td>';
                $data.='<td>'.$percentage.'</td>';
                $data.= '<td><button onclick="copyFunction(\''.$row['protein'].'\',\''.$row['protSeq'].'\')">SNAP2</button> </td>';
              
                $data.='</tr>';
                echo $data; 
              }
          ?>
        </tbody>
      </table>
    </div>
  </body>
  <script>

      function copyFunction(prot,seq) {
        // Get the text field
        var copyText = ">"+prot+"\n"+seq;

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        // Alert the copied text
        alert("Copied the text: " + copyText.value);
      }
    </script>
</html>
