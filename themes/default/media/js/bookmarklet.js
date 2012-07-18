(function(){
var b = document.getElementsByTagName("body")[0];
var originalBodyOverflow = b.style.overflow;
b.style.overflow = "hidden";

// Is the bookmarklet already open?
if (b.className.match(/(?:^|\s)has_swiftriver_bookmarklet(?!\S)/))
	return;

b.className += " has_swiftriver_bookmarklet";

// The modal window's frame
var container = document.createElement("div");
container.style.display = "block";
container.style.position = "fixed";
container.style.zIndex = "9999999";
container.style.top = 0;
container.style.right = 0;
container.style.bottom = 0;
container.style.left = 0;
container.style.overflowX = "auto";
container.style.overflowY = "scroll";
container.style.backgroundColor = "rgba(0, 0, 0, 0.75)";

var modalWindow = document.createElement("div");
modalWindow.style.position = "relative";
modalWindow.style.zIndex = 2;
modalWindow.style.overflow = "hidden";
modalWindow.style.width = "500px";
modalWindow.style.height = "425px";
modalWindow.style.borderRadius = "3px";
modalWindow.style.margin = "90px auto 20px";
modalWindow.style.float = "none";

var i = document.createElement("iframe");
i.src = "http://swiftweb.local/brian/bookmarklet?url=" + encodeURIComponent(window.location);
i.scrolling = "no";
i.style.width = "100%";
i.style.height = "100%"
i.style.border = "none";
i.style.overflow = "hidden";
modalWindow.appendChild(i);

// Invisible close button superimposed on the iframe.
var closeButton = document.createElement("div");
closeButton.style.position = "absolute";
closeButton.style.cursor = "pointer";
closeButton.style.zIndex = 3;
closeButton.style.top = "20px";
closeButton.style.right = 0;
closeButton.style.width = "80px";
closeButton.style.height = "15px";
closeButton.style.margin = 0;
closeButton.style.float = "none";
closeButton.style.textAlign = "right";
closeButton.style.backgroundColor = "rgba(0, 0, 0, 0.01)";
modalWindow.appendChild(closeButton);

container.appendChild(modalWindow);
document.getElementsByTagName("body")[0].appendChild(container);

// Attach close handler
closeButton.onclick = function() {
	b.removeChild(container);
	b.style.overflow = originalBodyOverflow;
	b.className = b.className.replace(/(?:^|\s)has_swiftriver_bookmarklet(?!\S)/, '')
	return false;
}

})();