var canvas = document.getElementById('genomeCanvas');
var bodyTag = document.getElementsByTagName('body')[0];
var tooltipSpan = document.getElementById('canvasDomainTooltip');

var ctx = setupCanvas(canvas);
var positionInfo = canvas.getBoundingClientRect();
var canvasHeight = positionInfo.height;
var canvasWidth = positionInfo.width;
var hit = false;
var relativeFeatureStart;
var relativeFeatureEnd
var relativeProteinStart = 0;
var relativeProteinEnd;

//This is to remove the blurriness of the image in the canvas 
function setupCanvas(canvas) 
{
    var dpr = 1; //window.devicePixelRatio || 1;
    var rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    var ctx = canvas.getContext('2d');
    ctx.scale(dpr,dpr);
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
      this.fill = fill || "gray";
      this.stroke = stroke || "skyblue";
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
      this.draw("black", 20, 5);
      return (this);
  }
  rect.prototype.draw = function (stroke, addHeight, addStrokeWidth) {
      ctx.save();
      ctx.beginPath();
      ctx.fillStyle = this.fill;
      ctx.strokeStyle = stroke;
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

function handleMouseMove(e) {
    mouseX = getMousePosInCanvas(canvas, e).x;
    mouseY = getMousePosInCanvas(canvas, e).y;

    //console.log('clientX: ' + e.clientX + '; clientY: ' + e.clientY + '; mouseX: ' + mouseX + '; mouseY: ' + mouseY);
    hit = false;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    var tt = document.getElementById('canvasDomainTooltip');

    for (var i = 0; i < compiledData.length; i++) {
        if (compiledData[i].isPointInside(mouseX, mouseY)) 
        {
            compiledData[i].highlight();
            hit = true;

            tt.className = "canvasDomainTooltipShow";
            tt.style.top = e.clientY + 35 + 'px';
            tt.style.left = e.clientX + 10 + 'px';
            tt.innerHTML = compiledData[i].id;
        }
        else {
            compiledData[i].redraw();
        } 
    }
    if(!hit) {
        var tooltipSpan = document.getElementById('canvasDomainTooltip');
        tooltipSpan.className = "canvasDomainTooltipHidden";        
        console.log('HIDE IT !!!!!');
    }
}

//parsedProteinData.sort(compare);

var data = [], compiledData = [];
data = getProteinCanvasData();
data.sort(compare);
for(var i=0;i < data.length; i++) 
{
    compiledData.push(new rect(data[i].name, data[i].x, data[i].y, data[i].w, data[i].h, 
        data[i].fc, data[i].bc, data[i].bw));
}

function getProteinCanvasData() 
{
    var result = [];
    for(var i=0;i < parsedProteinData.length; i++) 
    {
        row = parsedProteinData[i];
        relativeProteinEnd = row['End'] - row['Start'];

        relativeFeatureStart = row['Feature_Start'] - row['Start'];

        if(row['Feature_End']) {
            relativeFeatureEnd   = row['Feature_End'] - row['Start'];
        } else {
            relativeFeatureEnd   = row['Feature_Start'];
        }

        proteinLength = row['End'] - row['Start'];
        // console.log('Feature: ' + row['Name'] + ' - pstart: ' + row['Start'] + ' pend: ' + row['End'] +
        // '; featureStart: ' + row['Feature_Start'] + '; featureEnd: ' + row['Feature_End']);
        
        var id = row['Name'];
        var startX = Math.floor(canvasWidth * (relativeFeatureStart / proteinLength));
        var endX = Math.floor(canvasWidth * (relativeFeatureEnd / proteinLength));
        var width = row['Feature_End'] ? endX-startX : 1; 
        var height = 30;
        var fillColor = '#'+(0x1000000+Math.random()*0xffffff).toString(16).substr(1,6);    
        var borderColor = "white";
        var borderWidth = 0;

        var domainColorPalette = document.getElementById(id.replace(/\s/g, "_") + row['Feature_Start']);
        if(domainColorPalette)
            domainColorPalette.style.backgroundColor = fillColor;

        console.log(id.replace(/\s/g, "_") + ' :: ' + fillColor);
        // console.log('Feature: ' + row['Name'] + ' - start: ' + startX + ' end: ' + endX +
        //  '; width: ' + width + '; relativeFeatureStart: ' + relativeFeatureStart + '; relativeFeatureEnd: ' + relativeFeatureEnd);

        result.push({
            name: row['Name'],
            x: startX,
            y: 0,
            w: width,
            h: height,
            fc: fillColor,
            bc: borderColor,
            bw: borderWidth
        });
    }
    return result;
}

//data = data.sort(compare);


function compare( a, b ) 
{
    if ( a.width < b.width ){
      return -1;
    }
    if ( a.width > b.width ){
      return 1;
    }
    return 0;
}


canvas.addEventListener('mousemove', handleMouseMove);
canvas.addEventListener('mouseout', function(e) {
    tooltipSpan.className = 'canvasDomainTooltipHidden';
});
