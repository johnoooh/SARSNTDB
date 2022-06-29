var canvas = document.getElementById('genomeCanvas');
var ctx = setupCanvas(canvas);
var positionInfo = canvas.getBoundingClientRect();
var canvasHeight = positionInfo.height;
var canvasWidth = positionInfo.width;
var offsetX = canvas.offsetLeft;
var offsetY = canvas.offsetTop;


function setupCanvas(canvas) 
{
    var dpr = window.devicePixelRatio || 1;
    var rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    var ctx = canvas.getContext('2d');
    ctx.scale(dpr,dpr);
    return ctx;
}

var box = new Path2D();


var rect = (function () {

    // constructor
    function rect(id, x, y, width, height, fill, stroke, strokewidth) {
        this.x = x;
        this.y = y;
        this.id = id;
        this.width = width;
        this.height = height;
        // this.fill = fill || "gray";
        // this.stroke = stroke || "skyblue";
        this.strokewidth = strokewidth || 2;
        // this.redraw(this.x, this.y);
        // return (this);
        // Create circle
        box = new Path2D();
        box.rect(this.x, this.y, this.width, this.height);

        //box.arc(150, 75, 50, 0, 2 * Math.PI);
        ctx.fillStyle = fill;
        ctx.strokeStyle = stroke;
        ctx.lineWidth = this.strokewidth;
        ctx.fill(box);
        return this;
    }
    return rect;
})();

var rects = [];
rects.push(new rect("Red-Rectangle",   0, 0, 150, 50, "red", "black", 2));
//rects.push(new rect("Green-Rectangle", 25, 5, 50, 50, "green", "black", 2));
//rects.push(new rect("Blue-Rectangle",  85, 5, 100, 50, "blue", "black", 2));


function getMousePosInCanvas(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
      x: evt.clientX - rect.left,
      y: evt.clientY - rect.top
    };
}

canvas.addEventListener('mousemove', function(e) {
    mouseX = getMousePosInCanvas(canvas, e).x;
    mouseY = getMousePosInCanvas(canvas, e).y;

    console.log('clientX: ' + e.clientX + '; clientY: ' + e.clientY + '; mouseX: ' + mouseX + '; mouseY: ' + mouseY);

    // Check whether point is inside box
    if (ctx.isPointInPath(box, mouseX, mouseY)) {
      ctx.fillStyle = 'green';
      var tooltipSpan = document.getElementById('canvasDomainTooltip');
      tooltipSpan.className = "canvasDomainTooltipShow";
      tooltipSpan.style.top = e.clientY + 20 + 'px';
      tooltipSpan.style.left = e.clientX + 'px';
    }
    else {
      ctx.fillStyle = 'red';

      var tooltipSpan = document.getElementById('canvasDomainTooltip');
      tooltipSpan.className = "canvasDomainTooltipHidden";
    }
  
    // Draw circle
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fill(box);
  });