var tabs = document.querySelectorAll("nav.tabs li");
var panels = document.querySelectorAll("section.panels > div");

for (var i = 0; i < tabs.length; i++) {
  var tab = tabs[i];
  var text = tab.innerHTML;
  tab.innerHTML = "";

  var a = document.createElement("a");
  a.href = "javascript:void(0)";
  a.innerHTML = text;
  a.tab = tab;
  tab.appendChild(a);

  if (panels.length > i) {
    a.panel = panels[i];

    a.onclick = function() {
      var panel = this.panel;

      for (var i = 0; i < panels.length; i++)
        panels[i].className = "hidden";

      for (var i = 0; i < tabs.length; i++)
        tabs[i].className = "not-active";

      panel.className = "shown";

      this.tab.className = "active";
    }

    if (i == 0)
      a.click();
  }
}

function appendField(type) {
	if (type == 'visualisations') {
		var form = document.getElementById('twi-settings-' + type + '-form');
		var formChilds = form.getElementsByTagName('div');
		if (formChilds.length == 0) {
			var formInputs = form.getElementsByTagName('input');
			form.insertBefore(createFormField(type, formChilds.length), formInputs[0]);
		} else {
			form.insertBefore(createFormField(type, formChilds.length), formChilds[formChilds.length - 1].nextSibling);
		}
	}
}

function createFormField(type, number) {
	if (type == 'visualisations') {
		var div = document.createElement("div");
		div.className = 'field-container';
	
		createChildField('id', number, div);
		createChildField('path', number, div);
		createChildField('page', number, div);
		createChildField('position', number, div);
		createChildField('caption', number, div);
		createChildField('url', number, div);
		return div;				
	}
}

function createChildField(name, number, parent) {
	var label = document.createElement("label");
        label.innerHTML = name;
        label.htmlFor = number + '_' + name;

        var input = document.createElement("input");
        input.setAttribute('type', 'text');
        input.name = 'json[' + number + '][' + name + ']';
        input.id = name;

        parent.appendChild(label);
        parent.appendChild(input);
        parent.appendChild(document.createElement("br"));
}
