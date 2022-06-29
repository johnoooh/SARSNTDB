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
        this.strokewidth = strokewidth || 2;
        this.redraw(this.x, this.y);
        return (this);
    }
    rect.prototype.redraw = function (x, y) {
        this.x = x || this.x;
        this.y = y || this.y;
        this.draw(this.stroke);
        return (this);
    }
    //
    rect.prototype.highlight = function (x, y) {
        this.x = x || this.x;
        this.y = y || this.y;
        this.draw("orange");
        return (this);
    }
    
    rect.prototype.draw = function (stroke) {
        ctx.save();
        ctx.beginPath();
        ctx.fillStyle = this.fill;
        ctx.strokeStyle = stroke;
        ctx.lineWidth = this.strokewidth;
        ctx.rect(this.x, this.y, this.width, this.height);
        ctx.stroke();
        ctx.fill();
        ctx.restore();
    }
    
    rect.prototype.isPointInside = function (x, y) {
        //console.log('X: ' + this.x + '; Y: ' + this.y + '; mouseX: ' + x + '; mouseY: ' + y + 'isXwithtin: ' + (x >= this.x && x <= (this.x + this.width)) + '; isYWithin: ' + (y >= this.y && y <= (this.y + this.height)));

        return ((x >= this.x && x <= (this.x + this.width)) && 
                (y >= this.y && y <= (this.y + this.height)));
    }

    return rect;
})();

function getMousePosInCanvas(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
    return {
      x: evt.clientX - rect.left,
      y: evt.clientY - rect.top
    };
}

//
function handleMouseMove(e) {
    mouseX = getMousePosInCanvas(canvas, e).x;
    mouseY = getMousePosInCanvas(canvas, e).y;

    // Put your mousemove stuff here
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    for (var i = 0; i < rects.length; i++) {
        console.log('rects[i].isPointInside(mouseX, mouseY):  ' + rects[i].isPointInside(mouseX, mouseY));
        if (rects[i].isPointInside(mouseX, mouseY)) {
            var tooltipSpan = document.getElementById('canvasDomainTooltip');
            tooltipSpan.className = "canvasDomainTooltipShow";
            tooltipSpan.style.top = e.clientY + 20 + 'px';
            tooltipSpan.style.left = e.clientX + 'px';
            rects[i].highlight();
            //console.log(rects[i].id + '; x: ' + e.clientX + '; y: ' + e.clientY);
            
            
        } else {

            var tooltipSpan = document.getElementById('canvasDomainTooltip');
            tooltipSpan.className = "canvasDomainTooltipHidden";
            rects[i].redraw();
            //console.log(rects[i].id + '; x: ' + e.clientX + '; y: ' + e.clientY);

        }
    }
}


function handleMouseDown(e) {
    mouseX = getMousePos(canvas, e).x;
    mouseY = getMousePos(canvas, e).y;

    // Put your mousedown stuff here
    var clicked = "";
    for (var i = 0; i < rects.length; i++) {
        if (rects[i].isPointInside(mouseX, mouseY)) {
            clicked += rects[i].id + " "
        }
    }
    if (clicked.length > 0) {
        alert("Clicked rectangles: " + clicked);
    }
}



var rects = [];
rects.push(new rect("Red-Rectangle",   0, 0, 150, 50, "red", "black", 2));
//rects.push(new rect("Green-Rectangle", 25, 5, 50, 50, "green", "black", 2));
rects.push(new rect("Blue-Rectangle",  85, 5, 100, 50, "blue", "black", 2));

canvas.addEventListener("mousemove", handleMouseMove);
canvas.addEventListener("click", handleMouseDown);

