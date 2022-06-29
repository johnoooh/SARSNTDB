

function mutClick(btn) {
//     console.log(btn.parentNode.parentNode.children[2].innerText);
//     console.log(btn.parentNode.parentNode.children[4].innerText);
    var startEnd = btn.parentNode.parentNode.children[2].innerText;
    var startEdArray = startEnd.split("-");
   
    document.location.href = "./MutationsSearch.php?start=" + startEdArray[0] + "&end=" + startEdArray[1];
    
    }
