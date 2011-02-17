/*LICENSE INFORMATION*
 * Kure is distributed under the terms of the GNU General Public License
 * (http://www.gnu.org/licenses/gpl.html).
 * Kure Copyright 2007-2008 Ben Carlsson
 * 
 *-->
 * This file is part of Kure.
 * 
 * Kure is free software: you can redistribute it and/or modify it under the terms of the 
 * GNU General Public License as published by the Free Software Foundation, either version
 * 3 of the License, or (at your option) any later version.
 * 
 * Kure is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with Kure.  If
 * not, see <http://www.gnu.org/licenses/>.
 *-->
 *
 * Please see index.php for notes.
 */

// I know absolutely no javascript; i only made a few small cosmetic changes to this. /skoh-fley
// original from http://www.acme.com/javascript/

Tabs = function(element) {
  this.element = element;
  var table1Element = document.createElement('table');
  table1Element.border = '0';
  table1Element.cellPadding = '0';
  table1Element.cellSpacing = '0';
  element.appendChild(table1Element);
  var tbody1Element = document.createElement('tbody');
  table1Element.appendChild(tbody1Element);
  var tr1Element = document.createElement('tr');
  tbody1Element.appendChild(tr1Element);
  var td1Element = document.createElement('td');
  td1Element.align = 'left';
  tr1Element.appendChild(td1Element);
  var table2Element = document.createElement('table');
  table2Element.border = '0';
  table2Element.width = '100%';
  table2Element.cellPadding = '5';
  table2Element.cellSpacing = '0';
  td1Element.appendChild(table2Element);
  var tbody2Element = document.createElement('tbody');
  table2Element.appendChild(tbody2Element);
  var tr2Element = document.createElement('tr');
  tbody2Element.appendChild(tr2Element);
  var td2Element = document.createElement('td');
  td2Element.style.cssText = Tabs.paddingStyle;
  td2Element.width = "100%";
  td2Element.innerHTML = Tabs.paddingStr;
  tr2Element.appendChild(td2Element);
  var tr3Element = document.createElement('tr');
  tbody1Element.appendChild(tr3Element);
  var td4Element = document.createElement('td');
  td4Element.style.cssText = Tabs.panesStyle;
  tr3Element.appendChild(td4Element);
  this.tabsElement = tr2Element;
  this.paddingElement = td2Element;
  this.panesElement = td4Element;
  this.items = [];
  this.activeItem = null;
};

Tabs.activeTabStyle = 'white-space: nowrap; border-bottom: 1px #aaaaaa dashed;';
Tabs.inactiveTabStyle = 'white-space: nowrap;';
Tabs.paddingStyle = 'margin: 0; border: 0px;';
Tabs.panesStyle = 'margin: 0; border: 0px; padding: 1em';
Tabs.paddingStr = '&nbsp;';
Tabs.globalItems = [];

Tabs.prototype.Add = function(name,activateCallback) {
  var globalItemId = Tabs.globalItems.length;
  var padElement = document.createElement('td');
  padElement.style.cssText = Tabs.paddingStyle;
  padElement.innerHTML = Tabs.paddingStr;
  this.tabsElement.insertBefore(padElement,this.paddingElement);
  var tabElement = document.createElement('td');
  tabElement.style.cssText = Tabs.inactiveTabStyle;
  tabElement.innerHTML = '<a class="smallsub" href="javascript:Tabs.Switch('+globalItemId+')">'+name+'</a>';
  this.tabsElement.insertBefore(tabElement,this.paddingElement);
  var paneElement = document.createElement('div');
  var item = new Tabs.Item(this,name,activateCallback,tabElement,paneElement);
  this.items.push(item);
  Tabs.globalItems.push(item);
  if(this.items.length == 1)
    item.Activate();
};

Tabs.Item = function(parent,name,activateCallback,tabElement,paneElement) {
  this.parent = parent;
  this.name = name;
  this.activateCallback = activateCallback;
  this.tabElement = tabElement;
  this.paneElement = paneElement;
};

Tabs.Item.prototype.Activate = function() {
  if(this.parent.activeItem != null) {
    this.parent.activeItem.tabElement.style.cssText = Tabs.inactiveTabStyle;
    this.parent.panesElement.removeChild(this.parent.activeItem.paneElement);
  }
  this.parent.activeItem = this;
  this.tabElement.style.cssText = Tabs.activeTabStyle;
  this.parent.panesElement.appendChild(this.paneElement);
  this.activateCallback(this.paneElement);
};

Tabs.Switch = function(globalItemId) {
  Tabs.globalItems[globalItemId].Activate();
};
