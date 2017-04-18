var cek = sessionStorage.getItem("cek");
var kek = sessionStorage.getItem("kek");
if (cek == null || kek == null) {
	sessionStorage.clear();
	window.location.href = ROOT+"Logout";
}

var firstTime = localStorage.getItem("firstTime");
if (firstTime == null) {
	alert("Use the right click to use features");
	localStorage.setItem("firstTime", "nope");
}
