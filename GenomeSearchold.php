<!DOCTYPE html>
<html lang="en">
<head>
    <title>SARS-CoV2 Page</title>
    <link rel="stylesheet" href="bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <?php include "./Navigation.php";?>
    <?php include "./ProtienInfo.php";?>
    <!-- <script src="./JS/colorArraysc.js"></script> -->

    <style>
        .btn 
        {
          margin-top: 25px;
          margin-right: 5px;
        }
        .search-header 
        {
          padding-left: 10px;
        }
        .search-body
        {
          background-color: lightgrey;
        }
        .panel-body
        {
          background-color: white;
        }

        
    </style>
    <!--<script type="text/javascript" src="JS\domainPlotter.js"></script> -->

    <script>
      var clearButton = document.getElementById("clear_btn");
      //clearButton.addEventListener("click", resetForm);
      var proteinInfo = null;
      var parsedProteinData;
      var proteinSeqImgDiv;
      function getCovCov2ComparisonData() {
        var protein = document.getElementById("Protein").value;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {   
              // console.log(this.responseText);
              // console.log(this);

              // var parsedData = JSON.parse(this.responseText);

              getCovAndCov2ComparisonPage(this.responseText);
            }
          }
          xmlhttp.open("GET", "GenomeComparisonData.php?Protein="+protein, true);
          xmlhttp.send();
      }

      function getCovAndCov2ComparisonPage(data) {
        // console.log(data)
          var protein = document.getElementById("Protein").value;
          var xmlHttpHtmlPost = new XMLHttpRequest();
          xmlHttpHtmlPost.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {   
              dataview.innerHTML = this.responseText;
              var parsedData = JSON.parse(data);
              // console.log(parsedData[0].sars1seq)
                    
              // console.log(parsedData);
              var wdw = window.innerWidth;
              // console.log(wdw);
              var sars1js = parsedData[0].sars1seq;
              var sars2js = parsedData[0].sars2seq;
              var sarsevaljs = parsedData[0].sarsevalseq;
              var i=0;
              const cov2Array = Array();
              const cov1Array = Array();
              
              while (i<parsedData.length){
                // get the st and eds in separate arrays. 
                var tmpsted1 = parsedData[i].covAAStartEnd;
                cov1Array.push(tmpsted1.split("-"));
                var tmpsted2 = parsedData[i].cov2AAStartEnd;
                cov2Array.push(tmpsted2.split("-"));
                i++
              }

              console.log(cov1Array);
              console.log(cov2Array);
              var space = wdw-50;
              // console.log(typeof sars1js)
              var par = sars1js.length;

              var outhtml = "";
              var indcount = 0;
              // var node1 = document.getElementById('sars1');
              // var node2 = document.getElementById('sars2');
              // var nodeeval = document.getElementById('sarseval');
              var divnode = document.getElementById('protein-sequence');

              var interval = 80;
              // console.log(node1)
              var edval =0;
              
              while (par>indcount){
              


                var tmp1 = sars1js.slice(indcount,indcount +interval);
                var tmp2 = sars2js.slice(indcount,indcount +interval);
                var tmpEval = sarsevaljs.slice(indcount,indcount +interval);
                var blockDiv = document.createElement('div');

                indcount += interval;   
                edval += tmp1.length;
                var newEntry1 = document.createElement('p');
                textContent1 = document.createTextNode(`SARS-CoV \xa0 ${tmp1}  ${edval}`);
                newEntry1.appendChild(textContent1);
                newEntry1.style["margin"] = 0;

                var newEntry2 = document.createElement('p');
                textContent2 = document.createTextNode(`SARS-CoV-2 ${tmp2}`);
                
                newEntry2.appendChild(textContent2);
                newEntry2.style["margin"] = 0;

                var newEntryeval = document.createElement('p');
                textContenteval = document.createTextNode(`\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0\xa0${tmpEval} `);
                newEntryeval.appendChild(textContenteval);
                newEntryeval.style["margin"] = 0;
                
                blockDiv.appendChild(newEntry1);
                blockDiv.appendChild(newEntry2);
                blockDiv.appendChild(newEntryeval);
                
                
                divnode.appendChild(blockDiv);

                       
              }
        
     
              
              // if(proteinSeqImgDiv)
              //   proteinSeqImgDiv.innerHTML = parsedData[0].proteinSequenceImgTags;
              
            }
          }
          xmlHttpHtmlPost.open("POST", "GenomeComparison.php", true);
          xmlHttpHtmlPost.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xmlHttpHtmlPost.send('comparisonData=' + data + "&protein=" + protein);
      }

      function resetForm(){
          document.getElementById("Start").value="";
          document.getElementById("End").value="";
      }  
      function getGenomeData(getData) {
        var protein = document.getElementById("Protein").value;
        var start = document.getElementById("Start").value;
        var end = document.getElementById("End").value;

        var dataview = document.getElementById("dataview");
        dataview.style.display="none";
        var searchgrid = document.getElementById("searchgrid");
        searchgrid.style.display="block";
        var datagrid = document.getElementById("datagrid");
        
        if(getData === true) {
          var xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {            
              datagrid.innerHTML = this.responseText;

            }
          }
          console.log(protein)
          xmlhttp.open("GET", "GenomeResult.php?Protein="+protein+"&Start="+start+"&End="+end, true);
          xmlhttp.send();
        }
      }

      function getGeneDetails(gene, protein) {
        console.log(gene,protein);
        $proteinData = '';
        if(gene !== "" || protein !== "") {
          var dataview = document.getElementById("dataview");
          dataview.style.display="block";
          var searchgrid = document.getElementById("searchgrid");
          searchgrid.style.display="none";

          //post the 
          var xmlHttpHtmlPost = new XMLHttpRequest();
          xmlHttpHtmlPost.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {            
              // console.log(this.responseText);
              dataview.innerHTML = this.responseText;
              
              var s = document.createElement('script');
              s.type = 'text/javascript';
              s.src = 'JS\\domainplotter5.js';
              try {
                document.body.appendChild(s);
              } catch (e) {
                document.body.appendChild(s);
              }

              proteinImageDiv = document.getElementById('protein-image-div');
              if(proteinImageDiv)
                  proteinImageDiv.innerHTML = proteinInfo.imageTag;

            }
          }
          xmlHttpHtmlPost.open("POST", "GenomeDetail.php", true);
          xmlHttpHtmlPost.setRequestHeader("Content-type", "application/x-www-form-urlencoded");


          var xmlHttpGetData = new XMLHttpRequest();
          xmlHttpGetData.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              //$proteinData = 'proteinData='+this.responseText;
              console.log(this)
              proteinInfo = JSON.parse(this.responseText);
              parsedProteinData = proteinInfo.detailInfo;
              $proteinData = 'proteinData=' + JSON.stringify(proteinInfo.detailInfo);
              xmlHttpHtmlPost.send($proteinData)
              // console.log($proteinData);
              // console.log(proteinInfo,"...");
            }
          }
          xmlHttpGetData.open("GET", "GenomeDetailData.php?Gene="+gene+"&Protein="+protein, true);
          xmlHttpGetData.send();
        }
      }
    </script>
</head>

<body class="search-body">
  <h4 class="search-header">Genome</h4> 
  <div class="panel-body">
    <div id="searchgrid">         
      <div class="form-group" style="height:10%; width:100%;">
        <div class="row">
          <fieldset id="Gene_row">
            
            <div class="col-md-2">
                <label for="Start">Start</label>
                <input id="Start" type="text" class="form-control" id="Start" />
            </div>
            <div class="col-md-2">
                <label for="End">End</label>
                <input id="End" type="text" class="form-control" id="End" />
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-success" id="submit_btn" name="submit" onclick="getGenomeData(true)">Submit</button>
                <button type="button" onclick="resetForm();" id="clear_btn" class="btn btn-secondary">Clear</button>
            </div>

            <div class="col-md-2">
              <label for="Protein">Gene/Protein</label>
              <select class = "form-control" id="Protein" onchange="getGenomeData(true)">
                <option value= "">All</option>
                <option value="Nsp1">Nsp1</option>
                <option value="Nsp2">Nsp2</option>
                <option value="Nsp3">Nsp3</option>
                <option value="Nsp4">Nsp4</option>
                <option value="Nsp5">Nsp5</option>
                <option value="Nsp6">Nsp6</option>
                <option value="Nsp7">Nsp7</option>
                <option value="Nsp8">Nsp8</option>
                <option value="Nsp9">Nsp9</option>
                <option value="Nsp10">Nsp10</option>
                <option value="Nsp11">Nsp11</option>
                <option value="Nsp12">Nsp12</option>
                <option value="Nsp13">Nsp13</option>
                <option value="Nsp14">Nsp14</option>
                <option value="Nsp15">Nsp15</option>
                <option value="Nsp16">Nsp16</option>
                <option value="Surface Glycoprotein">S Gene</option>
                <option value="ORF3a Protein">ORF3a</option>
                <option value="Envelope Membrane Protein">E Gene</option>
                <option value="Membrane Protein">M Gene</option>
                <option value="ORF6a Protein">ORF6a</option>
                <option value="ORF7a Protein">ORF7a</option>
                <option value="ORF7b Protein">ORF7b</option>
                <option value="ORF8 Protein">ORF8</option>
                <option value="ORF9 protein">ORF9</option>
                <option value="Nucleocapsid proteins">N Gene</option>
                <option value="ORF10 Protein">ORF10</option>
              </select>
            </div>

          </fieldset>
          <br />
      </div>
      </div>
      <div id="datagrid" style="height:90%; width:100%;">
        <i>Apply filters and hit submit to start seeing results.</i>
      </div>
    </div>
    <div id="dataview" style="height:90%; width:100%;"></div>
  </div>
</body>
</html>
