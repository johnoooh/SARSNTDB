

/*
    1.) Make sure that entire genome data is supported.
    2.) within genome, represent protein data.
    3.) within protein, represent domains, regions, etc

    var sampledata = [
        { fx: 0,    fy: 0, tx: 0,   ty: 20, color: 'blue', width: 481 },
        { fx: 300,  fy: 0, tx: 300, ty: 20, color: 'red', width: 20 },
        { fx: 30,   fy: 0, tx: 30,  ty: 20, color: 'green', width: 15 },
        { fx: 0,    fy: 0, tx: 0,   ty: 20, color: 'blue', width: 481 },
        { fx: 120,  fy: 0, tx: 120, ty: 20, color: 'red', width: 45 },
        { fx: 130,  fy: 0, tx: 130,  ty: 20, color: 'green', width: 15 }
    ];
*/

var canvas = document.getElementById('genomeCanvas');
var context = setupCanvas(canvas);
var positionInfo = canvas.getBoundingClientRect();
var canvasHeight = positionInfo.height;
var canvasWidth = positionInfo.width;



//parsedProteinData['start'] -> represents 0th pixel in canvas
//parsedProteinData['end'] -> represents 100%th pixel in canvas

var data = [];
for(var i=0;i < parsedProteinData.length; i++) 
{
    row = parsedProteinData[i];
    relFeatureCoordsStart = row['Feature_Start'] - row['Start'];
    relFeatureCoordsEnd = row['Feature_End'] - row['Start'];

    proteinLength = row['End'] - row['Start'];

    startX =  Math.floor(canvasWidth * (relFeatureCoordsStart / proteinLength));
    endX = Math.floor(canvasWidth * (relFeatureCoordsEnd / proteinLength));

    console.log('Feature: ' + row['Name'] + ' - start: ' + startX + ' end: ' + endX + '; width: ' + (endX-startX));

    var color = Math.floor(Math.random()*16777215).toString(16); 

    data.push({
        fx: startX,
        fy: 0,
        tx: startX,
        ty: 50,
        color: '#' + color,
        width: endX-startX,
        name: row['Name']
    });

}
data = data.sort(compare);


for(var i=0;i < data.length; i++) 
{
    console.log('Feature: ' + data[i].name + '; ' + 
    'color: ' + data[i].color + '; ' +
    'fx: ' + data[i].fx + '; ' + 
    'width: ' + data[i].width);
    drawLine(data[i].fx, data[i].fy, data[i].tx, data[i].ty, data[i].color, data[i].width);
}



function setupCanvas(canvas) 
{
    var dpr = window.devicePixelRatio || 1;
    var rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    var ctx = canvas.getContext('2d');
    ctx.scale(dpr,dpr);
    canvas.addEventListener("mousemove", function(e){
        mouseX = parseInt(e.clientX - offsetX);
        mouseY = parseInt(e.clientY - offsetY);
        // Put your mousemove stuff here
        console.log('mouseX: ' + mouseX + '; mouseY: ' + mouseY);
    
    });
    return ctx;
}


function drawLine(fromPosX, fromPosY, toPosX, toPosY, color, width)
{
    if(context) {
        context.beginPath();
        context.moveTo(fromPosX, fromPosY);
        context.lineTo(toPosX, toPosY);
        context.lineWidth = width;
        context.strokeStyle = color;
        context.stroke();
    }
}

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




