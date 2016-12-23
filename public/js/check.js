cek = sessionStorage.getItem("cek");
kek = sessionStorage.getItem("kek");
if (cek == null || kek == null) {
	window.location.href = "Logout";
}
