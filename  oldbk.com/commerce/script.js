function dynamicSelect(id1, id2) {

 if (document.getElementById && document.getElementsByTagName) {

  var sel1 = document.getElementById(id1);
  var sel2 = document.getElementById(id2);
  var clone = sel2.cloneNode(true);
  var clonedOptions = clone.getElementsByTagName("option");
  refreshDynamicSelectOptions(sel1, sel2, clonedOptions);

  sel1.onchange = function() {
  refreshDynamicSelectOptions(sel1, sel2, clonedOptions);
  }
 }
}

function refreshDynamicSelectOptions(sel1, sel2, clonedOptions) {

 while (sel2.options.length) {
  sel2.remove(0);
 }
 var pattern1 = /( |^)(select)( |$)/;
 var pattern2 = new RegExp("( |^)(" + sel1.options[sel1.selectedIndex].value + ")( |$)");

 for (var i = 0; i < clonedOptions.length; i++) {

  if (clonedOptions[i].className.match(pattern1) ||
  clonedOptions[i].className.match(pattern2)) {
  sel2.appendChild(clonedOptions[i].cloneNode(true));
  }
 }
}

window.onload = function() {
	dynamicSelect("arttype", "artproto");
}
