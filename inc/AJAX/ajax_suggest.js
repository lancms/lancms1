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
          var root = xml.getElementsByTagName('item');
          var suggestions_name = new Array();
	  var suggestions_ID = new Array();
          var cnt = 0;
          for (var i=0; i < root.length; i++){
//               var node = root.childNodes.item(i);
               if (root[i].length > 0)
		    cnt++;{
                    suggestions_name[cnt] = root[i].getAttribute('name');
		    suggestions_ID[cnt] = root[i].getAttribute('ID');
               }
          }
          for(i=0; i < suggestions_name.length; i++) {
                   var val = suggestions_name[i];
                   if (val.length > 0){
//                        var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
//                        suggest += 'onmouseout="javascript:suggestOut(this);" ';
//                        suggest += 'onclick="javascript:setValue(this.innerHTML);" ';
//                        suggest += 'class="suggest_link">' + val + '</div>';
			var suggest = '<tr><td><a href=?module=kiosk&action=addWare&ware=' + suggestions_ID[i];
			suggest += '>' + val + '</a></td></tr>';
                        ss.innerHTML += suggest;
               }
          }
     }
}

