<?php

    require_once("GenomeComparisonInfo.php");

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    if(isset($_GET['Protein'])){
        $Protein = $_GET['Protein'];
    }
    // if ($Protein=="Surface Glycoprotein"){
    //   $Protein = "S";
    //   $lpro = "sprotein";
    // }elseif($Protein=="Nucleocapsid proteins"){
    //   $lpro = "mprotein";
    //   $Protein = "N";
    // }elseif($Protein=="M"){
    //   $lpro = "mprotein";
    // }elseif($Protein=="M"){
    //   $lpro = "mprotein";
    // }elseif($Protein=="M"){
    //   $lpro = "mprotein";
    // }
    
    $q1 = "";
    if($Protein != "") {
      if (strpos($Protein,"protein") !== false ){
        if (strpos($Protein,"ORF")){
          $tmp_prot =explode(" ",$Protein); 
          $tmp_prot = array_slice($tmp_prot, 0,1);
          $lpro = strtolower($tmp_prot);
        }else{
          // echo "protein no orf";
          $tmp_prot = substr($Protein, 0,1);
          $lpro = $tmp_prot + "protein";
        }
      $q1 .= " AND cov_comp.matchedcol = '" . $tmp_prot . "'";
      }else{
        $lpro = $Protein;
        $tmp_prot = $Protein;
      }


    }
      

    require_once './connection.php';
    // $sql = "  SELECT cov_comp.*, s.Sequence FROM domain cov_comp
    //               inner join Sequence s on cov_comp.gene = s.Protein
    //             where 1=1 $q1";

    $sql = "  SELECT * FROM cov_comp
                where 1=1 $q1";


    $result = $con->query($sql);
    
    //new******
    $sql = "  SELECT '" . $lpro . "' FROM compseq ";

    
    $resultseq = $con->query($sql);


    if (!$result) {
      echo ("query error covcomp data");
      exit();
    }

    
    //********* */
    // debug_to_console("test");
    $result_rows_seq = $resultseq->fetch_all(MYSQLI_ASSOC);
    $lpro= strtolower($Protein);
    $counterp=0;

    if ($Protein=="S"){
      
      $lpro = "sprotein";

      
    }elseif($Protein=="N"){
      $lpro = "nprotein";
    }elseif($Protein=="M"){
      $lpro = "mprotein";
    }elseif($Protein=="E"){
      $lpro = "eprotein";
    }

    foreach($result_rows_seq as $row)
    {
      
      if ($counterp == 0){
        $sars1 = $row[$lpro];
        // $counter++;
        $counterp++;

      }elseif($counterp==1){
          
        $sars2 = $row[$lpro];
        $counterp++;   
      } elseif ($counterp==2){
        $sarsevalu = $row[$lpro];
        
      }
    }
      
    
    $result_rows = $result->fetch_all(MYSQLI_ASSOC);
    
    // $sarsCov2Name = strtolower($Protein) = '';
    $sarsCov2Name = $proteinSequence = '';
    foreach($result_rows as $row)
    {
        // debug_to_console($row);
        if($sarsCov2Name != $row['domainNameCov2'])
        {
          $obj = new GenomeComparisonInfo();
          $comparisonRecs[] = $obj;
          $sarsCov2Name = $row['domainNameCov2'];
        }

        // $obj -> gene = $row['Gene'];
        // $obj -> feature = $row['Feature'];
        // $obj -> domainNameCov2 = $row['Name_SARSCoV2'];
        // $obj -> domainNameCov = $row['Name_SARSCoV'];
        // $obj -> cov2AAStartEnd = $row['SARS-CoV-2_aa_Range_(start-end)'];
        // $obj -> cov2Start = $row['SARS-CoV-2_nt_Range_start'];
        // $obj -> cov2End = $row['SARS-CoV-2_nt_Range_end'];
        // $obj -> covAAStartEnd = $row['SARS-CoV_aa_Range_(start-end)'];
        // $obj -> covStart = $row['SARS-CoV_nt_Range_start'];
        // $obj -> covEnd = $row['SARS-CoV_nt_Range_end'];
        // $obj -> identities = $row['Identities'];
        // $obj -> positives = $row['Positives'];

        $obj -> gene = $row['gene'];
        $obj -> feature = $row['feature'];
        $obj -> domainNameCov2 = $row['domainNameCov2'];
        $obj -> domainNameCov = $row['domainNameCov'];
        $obj -> cov2AAStartEnd = $row['cov2AAStartEnd'];
        $obj -> cov2Start = $row['cov2Start'];
        $obj -> cov2End = $row['cov2End'];
        $obj -> covAAStartEnd = $row['covAAStartEnd'];
        $obj -> covStart = $row['covStart'];
        $obj -> covEnd = $row['covEnd'];
        $obj -> identities = $row['identities'];
        $obj -> positives = $row['positives'];
        $obj -> sars1seq = $sars1;
        $obj -> sars2seq = $sars2;
        $obj -> sarsevalseq = $sarsevalu;
        $obj -> dashRange = $row['dashRange'];
        $obj -> dashRange2 = $row['dashRange2'];
        


        // $obj -> gene = $row[0];
        // $obj -> feature = $row[1];
        // $obj -> domainNameCov2 = $row[2];
        // $obj -> domainNameCov = $row[3];
        // $obj -> cov2AAStartEnd = $row[4];
        // $obj -> cov2Start = $row[5];
        // $obj -> cov2End = $row[6];
        // $obj -> covAAStartEnd = $row[7];
        // $obj -> covStart = $row[8];
        // $obj -> covEnd = $row[9];
        // $obj -> identities = $row[10];
        // $obj -> positives = $row[11];

        // if($row['Sequence'] != "")
        // {
        //   $proteinSequence .= '<div><img src="data:image/png;charset=utf8;base64,'.base64_encode($row['Sequence']).'"  /></div>' ;
        //   //$row['Sequence'] = base64_encode($row['Sequence']);
        // }

        // $obj -> proteinSequenceImgTags = $proteinSequence;


    }
    echo json_encode($comparisonRecs);

?>
