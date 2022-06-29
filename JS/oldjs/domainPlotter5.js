var canvas = document.getElementById('genomeCanvas');
var bodyTag = document.getElementsByTagName('body')[0];
var tooltipSpan = document.getElementById('canvasDomainTooltip');

var ctx = setupCanvas(canvas);
var positionInfo = canvas.getBoundingClientRect();
var canvasHeight = positionInfo.height;
var canvasWidth = positionInfo.width;
var hit = false;
var relativeFeatureStart;
var relativerowFeatureEnd;
var relativeProteinStart = 0;
var relativeProteinEnd;
var colorsSoFar = [];

var genomeLength = 29904 - 1;



//This is to remove the blurriness of the image in the canvas 
function setupCanvas(canvas) 
{
    var dpr = 1; //window.devicePixelRatio || 1;
    if(canvas) {
        var rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        var ctx = canvas.getContext('2d');
        ctx.scale(dpr,dpr);
    }
    return ctx;
}

// This is to get the exact mouse position within in the Canvas instead 
// of the mouse position in the page
function getMousePosInCanvas(canvas, evt) {
  var rect = canvas.getBoundingClientRect();
  return {
    x: evt.clientX - rect.left,
    y: evt.clientY - rect.top
  };
}

var rect = (function () {
    // constructor
    function rect(id, x, y, width, height, fill, stroke, strokewidth) {
      this.x = x;
      this.y = y;
      this.id = id;
      this.width = width;
      this.height = height;
      this.fill = fill;// || "gray";
      //this.stroke = stroke || "skyblue";
      this.strokewidth = strokewidth;
      this.box = new Path2D();
      this.redraw(this.x, this.y);
      return (this);
  }
  rect.prototype.redraw = function (x, y) {
      this.x = x || this.x;
      this.y = y || this.y;
      this.draw(this.stroke);
      return (this);
  }
  rect.prototype.highlight = function (x, y) {
      this.x = x || this.x;
      this.y = y || this.y;
      this.draw("black", 20, 0);
      return (this);
  }
  rect.prototype.draw = function (stroke, addHeight, addStrokeWidth) {
      ctx.save();
      ctx.beginPath();
      ctx.fillStyle = this.fill;
      //ctx.strokeStyle = stroke;
      ctx.lineWidth = addStrokeWidth ? addStrokeWidth + this.strokewidth: this.strokewidth;
      this.adjustedHeight = addHeight ? this.height + addHeight:  this.height;
      this.yPos = (canvasHeight - this.height)/2;
      ctx.rect(this.x + this.strokewidth/2, this.yPos + this.strokewidth/2, this.width, this.height);
      ctx.stroke();
      ctx.fill();
      ctx.restore();
  }
  rect.prototype.isPointInside = function (x, y) {
        return ((x >= this.x && x <= (this.x + this.width)) && 
        (y >= this.yPos && y <= (this.yPos + this.adjustedHeight)));        
  }
  return rect;
})();

var data = [], filteredData=[];
var result = [];
var toggleGenomeEl = document.getElementById('toggleGenomeCanvas');
filteredData = parsedProteinData.filter(p => p.Current_Protein);

if(toggleGenomeEl.checked)
    data = getGenomeCanvasData(parsedProteinData, true);
else
    data = getGenomeCanvasData(filteredData, false);

function getGenomeCanvasData(pData, toggleFullGenome) 
{
    var darkBackgroundHeight = 70;
    var dataHeight = 30;
    var hasBlackBackground = true;
    var fillColor;
    var colorArray = [
        "#610c31",
        "#1830d6",
        "#af4b76",
        "#49448d",
        "#8d9112",
        "#59b7aa",
        "#695691",
        "#0b2c2e",
        "#a51a48",
        "#c35240",
        "#856255",
        "#78e16a",
        "#8acb46",
        "#32814f",
        "#fd612f",
        "#db066f",
        "#b90938",
        "#098452",
        "#d5c651",
        "#0be6ad",
        "#b22c7a",
        "#9c2a41",
        "#84ed28",
        "#3b9cda",
        "#1fad25",
        "#8136ba",
        "#801bff",
        "#2c2a4c",
        "#d28de8",
        "#1f6cd3",
        "#637ece",
        "#6908c1",
        "#d29076",
        "#355be0",
        "#8525be",
        "#02e053",
        "#503bfb",
        "#f74852",
        "#b783ad",
        "#b60127",
        "#3e178a",
        "#e57bc3",
        "#5dfebe",
        "#7eb821",
        "#07779e",
        "#f11e6e",
        "#a482ae",
        "#11dae2",
        "#ec2bc5",
        "#258885",
        "#9c1b4e",
        "#1c1e75",
        "#7b9307",
        "#72fbe5",
        "#decf9e",
        "#485cab",
        "#6fa61b",
        "#a1ed76",
        "#3f1469",
        "#112f4d",
        "#dc10dd",
        "#1b044b",
        "#4d9597",
        "#e56738",
        "#e1cd2f",
        "#9af414",
        "#d8228a",
        "#7c25aa",
        "#782889",
        "#37f928",
        "#1746de",
        "#89e2ce",
        "#f0f051",
        "#6e257f",
        "#80f11f",
        "#1a5fb9",
        "#557891",
        "#77a909",
        "#f6c6cc",
        "#c33987",
        "#420a9e",
        "#3ec1b3",
        "#b8143e",
        "#073b2c",
        "#011ae1",
        "#2c1cc6",
        "#341523",
        "#cd96cc",
        "#cb5adf",
        "#615042",
        "#042c8b",
        "#99a5b1",
        "#5b0c25",
        "#5ec6e6",
        "#575a0e",
        "#fd2fa0",
        "#88ac9d",
        "#8861ed",
        "#24b43c",
        "#184d53",
        "#4f21d6",
        "#cdf06c",
        "#654bb1",
        "#147cd9",
        "#b39fda",
        "#8840f7",
        "#2f0c05",
        "#e68109",
        "#5d62c2",
        "#27c813",
        "#4a0d50",
        "#b71ece",
        "#3fa523",
        "#a9f5ab",
        "#e7b245",
        "#94922d",
        "#a1c50d",
        "#734b00",
        "#0d8cfd",
        "#32432a",
        "#fcba6e",
        "#538eb6",
        "#07d0e7",
        "#3c84ca",
        "#57e6b8",
        "#858d03",
        "#8b607e",
        "#c690eb",
        "#f54f6c",
        "#0162a4",
        "#6d8142",
        "#35443a",
        "#26a272",
        "#09f62e",
        "#239d1d",
        "#276037",
        "#1f1576",
        "#e2e3b6",
        "#f621ad",
        "#b36fec"
    ]
    const mappedColorArray = new Map();
    for(i=0; i < parsedProteinData.length;i++){
        mappedColorArray.set(parsedProteinData[i],colorArray[i]);
    }
    // console.log(pData);
    for(var i=0;i < pData.length; i++) 
    {
        row = pData[i];
        //console.log(row);
        // fillColor = giveMeNewColor(hasBlackBackground);
        // colorsSoFar.push(fillColor);
        if(row['Current_Protein']) {
            // if(hasBlackBackground){
            //     plotRowData(row, row['Start'], row['End'], '#a2a2a2', darkBackgroundHeight, hasBlackBackground, true);
            //     console.log("black")
            // } 

            hasBlackBackground = false;
            plotRowData(row, row['Feature_Start'], row['Feature_End'], mappedColorArray.get(pData[i]), dataHeight, hasBlackBackground, true); 

        } else if (toggleFullGenome) {
            plotRowData(row, row['Feature_Start'], row['Feature_End'], mappedColorArray.get(pData[i]), dataHeight, false);  
        } else if (row['Current_Protein']) {

            if(hasBlackBackground){
                plotRowData(row, row['Start'], row['End'], '#a2a2a2', darkBackgroundHeight, hasBlackBackground, true);
            } 

            hasBlackBackground = false;
            plotRowData(row, row['Feature_Start'], row['Feature_End'], mappedColorArray.get(pData[i]), dataHeight, hasBlackBackground,true); 
            console.log()




        //Todo: some domains have a value of 'blank' for start and end. so, domain width is 0
        //We need to handle that as well here.
        }
    }
    return result;
}

function giveMeNewColor(allowBlackBackground) 
{

    var fillColor = '#'+(0x1000000+Math.random()*0xffffff).toString(16).substr(1,6);  
    if(fillColor == '#a2a2a2' && !allowBlackBackground && colorsSoFar.indexOf(fillColor) > -1) {
        giveMeNewColor(false);
    }
    return fillColor;
}

function plotRowData(row, start, end, fillColor, height, hasBlackBackground,fillEntire) 
{   
    if(!(start && end))
        return;

    if(hasBlackBackground) 
    {
        id = row['Current_Protein'];
    } 
    else 
    {
        id = row['domainNameCov2'];    
    }
    
    if (fillEntire){
        var genelen = row['End']- row['Start'];
        var startX = Math.floor(canvasWidth * ((start-row['Start'])/ genelen));
        var endX = Math.floor(canvasWidth * ((end-row['Start']) / genelen));
        var width = endX-startX; 
        console.log(fillEntire)
        
    } else{
        var startX = Math.floor(canvasWidth * (start/ genomeLength));
        var endX = Math.floor(canvasWidth * (end / genomeLength));
        var width = row['Feature_End'] ? endX-startX : 1; 
    }

    var borderColor = "white";
    var borderWidth = 0;
    
    
    var domainColorPalette = document.getElementById(row["domainNameCov2"].replace(/\s/g, "_") + row['Feature_Start']);
    // console.log(domainColorPalette);
    if(domainColorPalette)
        domainColorPalette.style.backgroundColor = fillColor;

    //console.log(id.replace(/\s/g, "_") + ' :: ' + fillColor);
    
    result.push(new rect(id, startX, 0, width, height, 
                        fillColor, borderColor, borderWidth)
                ); 
}

function handleMouseMove(e) {
    mouseX = getMousePosInCanvas(canvas, e).x;
    mouseY = getMousePosInCanvas(canvas, e).y;

    hit = false;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    var tt = document.getElementById('canvasDomainTooltip');
    console.log('data: [' + data.length + ']')
    for (var i = 0; i < data.length; i++) {
        if (data[i].isPointInside(mouseX, mouseY)) 
        {
            data[i].highlight();
            hit = true;

            tt.className = "canvasDomainTooltipShow";
            tt.style.top = e.clientY + 35 + 'px';
            tt.style.left = e.clientX + 10 + 'px';
            tt.innerHTML = data[i].id;
        }
        else {
            data[i].redraw();
        } 
    }
    if(!hit) {
        var tooltipSpan = document.getElementById('canvasDomainTooltip');
        tooltipSpan.className = "canvasDomainTooltipHidden";        
        //console.log('HIDE IT !!!!!');
    }
}


canvas.addEventListener('mousemove', handleMouseMove);
canvas.addEventListener('mouseout', function(e) {
    tooltipSpan.className = 'canvasDomainTooltipHidden';
});
// alert(toggleGenomeEl.checked);
toggleGenomeEl.addEventListener('change', function(e) {
    // alert(toggleGenomeEl.checked);
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    result = [];
    if(toggleGenomeEl.checked)
        data = getGenomeCanvasData(parsedProteinData, true);
    else
        data = getGenomeCanvasData(filteredData, false);
    console.log(data)
});