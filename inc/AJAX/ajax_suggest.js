//This doesn't work in IE 6
var suggestReq = new XMLHttpRequest();
function suggest() {
     if (suggestReq.readyState == 4 || suggestReq.readyState == 0) {
          var str = escape(document.getElementById('ware').value);
          suggestReq.open("GET", '?api=kiosk&action=searchitems&search=' + str, true);
          suggestReq.onreadystatechange = handleSuggestions; 
          suggestReq.send(null);
     }
}


function handleSuggestions() {
     if (suggestReq.readyState == 4) {
          var ss = document.getElementById('suggest')
          ss.innerHTML = '';
          var xml = suggestReq.responseXML;
          var root = xml.getElementsByTagName('item').item(0);
          var suggestions = new Array();
          var cnt = 0;
          for (var i=0; i < root.childNodes.length; i++){
               var node = root.childNodes.item(i);
               if (node.childNodes.length > 0){
                    suggestions[cnt++] = node.childNodes.item(0).data;
               }
          }
          for(i=0; i < suggestions.length; i++) {
                   var val = suggestions[i];
                   if (val.length > 0){
                        var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
                        suggest += 'onmouseout="javascript:suggestOut(this);" ';
                        suggest += 'onclick="javascript:setValue(this.innerHTML);" ';
                        suggest += 'class="suggest_link">' + val + '</div>';
                        ss.innerHTML += suggest;
               }
          }
     }
}

