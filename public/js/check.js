var cek = sessionStorage.getItem("cek");
var kek = sessionStorage.getItem("kek");
if (cek == null || kek == null) {
	window.location.href = "Logout";
}
