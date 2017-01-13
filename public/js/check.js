var cek = sessionStorage.getItem("cek");
var kek = sessionStorage.getItem("kek");
if (cek == null || kek == null) {
	sessionStorage.clear();
	window.location.href = root+"Logout";
}
