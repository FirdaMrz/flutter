<?php
include 'koneksi.php'; // Menghubungkan ke file koneksi database

// Mendefinisikan kode untuk transaksi login dan daftar
define("LOGIN", "01"); // Konstanta untuk transaksi login
define("DAFTAR", "02"); // Konstanta untuk transaksi pendaftaran
define("GET_BOOKS", "03"); // Konstanta untuk mengambil semua buku
define("GET_BOOK_DETAILS", "04"); // Konstanta untuk mengambil detail buku berdasarkan ID
define("SEARCH_BOOKS", "05"); // Konstanta untuk mencari buku berdasarkan query
define("GET_CATEGORIES", "06"); // Konstanta untuk mengambil semua kategori buku
define("GET_DISTRIBUTORS", "07"); // Konstanta untuk mengambil semua distributor
define("GET_SUPPLIES", "08"); // Konstanta untuk mengambil data pemasokan (pasok)



// Ambil data dari request POST
if (isset($_POST['TokoBuku'])) {
    // Konversi data JSON menjadi array
    $paket = $_POST['TokoBuku'];
    $vPaket = json_decode($paket, true); // Mengubah JSON menjadi array PHP

   // Memastikan paket telah di-decode dan memiliki key yang diharapkan
   if (isset($vPaket['TRX'])) {
    // Menggunakan switch untuk menentukan tindakan berdasarkan nilai TRX
    switch ($vPaket['TRX']) {
        case LOGIN:
            // Memanggil fungsi Login dan mengembalikan hasilnya dalam format JSON
            echo json_encode(Login($vPaket['DATA']));
            break;
        case DAFTAR:
            // Memanggil fungsi Daftar untuk mendaftar pengguna baru
            echo json_encode(Daftar($vPaket['DATA']));
            break;
        case GET_BOOKS:
            // Mengambil semua buku dari database
            echo json_encode(getBooks());
            break;
        case GET_BOOK_DETAILS:
            // Mengambil detail buku berdasarkan ID buku yang diberikan
            echo json_encode(getBookDetails($vPaket['DATA']['book_id']));
            break;
        case SEARCH_BOOKS:
            // Mencari buku berdasarkan query yang diberikan
            echo json_encode(searchBooks($vPaket['DATA']['query']));
            break;
        case GET_CATEGORIES:
            // Mengambil semua kategori buku dari database
            echo json_encode(getCategories());
            break;
            case GET_DISTRIBUTORS:
                // Mengambil data semua distributor
                echo json_encode(getDistributors());
                break;
            case GET_SUPPLIES:
                // Mengambil data pemasokan (pasok)
                echo json_encode(getSupplies());
                break;
        default:
            // Jika TRX tidak dikenali, mengembalikan pesan kesalahan
            echo json_encode(["success" => false, "message" => "Transaksi tidak valid"]);
            break;
    }
} else {
    // Jika data tidak lengkap atau format salah
    echo json_encode(["success" => false, "message" => "Data tidak lengkap atau format salah"]);
}
} else {
// Jika request tidak valid atau tidak ada
echo json_encode(["success" => false, "message" => "Request tidak valid"]);
}




// Fungsi untuk login
function Login($data) {
    include 'koneksi.php'; // Menghubungkan ke database lagi dalam fungsi

    $username = isset($data['Username']) ? $data['Username'] : null; // Mengambil username dari data JSON
    $password = isset($data['Password']) ? md5($data['Password']) : null; // Mengambil dan mengenkripsi password

    // Memastikan username dan password telah diisi
    if ($username && $password) {
        $query = "SELECT username, password FROM kasir WHERE username = '$username' AND password = '$password'"; // Query untuk cek username dan password di database
        $result = mysqli_query($conn, $query); // Menjalankan query

        if ($result) {
            // Memeriksa apakah ada hasil yang ditemukan
            if (mysqli_num_rows($result) > 0) { // Jika ada data yang cocok
                $user = mysqli_fetch_assoc($result); // Mengambil data user
                mysqli_close($conn); // Menutup koneksi
                return ["success" => true, "message" => "Login berhasil", "data" => $user]; // Respon berhasil
            } else {
                mysqli_close($conn); // Menutup koneksi
                return ["success" => false, "message" => "Username atau password salah"]; // Respon gagal login
            }
        } else {
            mysqli_close($conn); // Menutup koneksi jika query error
            return ["success" => false, "message" => "Error: " . mysqli_error($conn)]; // Respon jika terjadi kesalahan query
        }
    } else {
        return ["success" => false, "message" => "Username dan password harus diisi"]; // Respon jika username atau password kosong
    }
}




// Fungsi untuk pendaftaran
function Daftar($data) {
    include 'koneksi.php'; // Menghubungkan ke database lagi dalam fungsi

    // Ambil semua data dari JSON
    $username = isset($data['Username']) ? $data['Username'] : null;
    $password = isset($data['Password']) ? md5($data['Password']) : null;
    $email = isset($data['Email']) ? $data['Email'] : null;
    $nama = isset($data['Nama']) ? $data['Nama'] : null;
    $alamat = isset($data['Alamat']) ? $data['Alamat'] : null;
    $telepon = isset($data['Telepon']) ? $data['Telepon'] : null;
    $status = isset($data['Status']) ? $data['Status'] : null;
    $fullname = isset($data['Fullname']) ? $data['Fullname'] : null;
    $akses = isset($data['akses']) ? $data['akses'] : null;

    // Memastikan data pendaftaran telah diisi
    if ($username && $password && $email && $nama && $alamat && $telepon && $status && $fullname && $akses) {
        // Memeriksa apakah username sudah ada
        $queryCheck = "SELECT * FROM kasir WHERE username = '$username'";
        $resultCheck = mysqli_query($conn, $queryCheck);

        if (mysqli_num_rows($resultCheck) > 0) { // Cek apakah username sudah digunakan
            mysqli_close($conn); // Menutup koneksi
            return ["success" => false, "message" => "Username sudah digunakan"]; // Respon jika username sudah ada
        }

        // Menyimpan data pengguna baru ke dalam database
        $query = "INSERT INTO kasir (username, password, email, nama, alamat, telepon, status, fullname, akses) 
                  VALUES ('$username', '$password', '$email', '$nama', '$alamat', '$telepon', '$status', '$fullname', '$akses')";
        if (mysqli_query($conn, $query)) { // Jika query berhasil
            mysqli_close($conn); // Menutup koneksi
            return ["success" => true, "message" => "Pendaftaran berhasil"]; // Respon berhasil
        } else {
            mysqli_close($conn); // Menutup koneksi jika query gagal
            return ["success" => false, "message" => "Gagal daftar: " . mysqli_error($conn)]; // Respon jika terjadi kesalahan query
        }
    } else {
        return ["success" => false, "message" => "Data pendaftaran tidak lengkap"]; // Respon jika data tidak lengkap
    }
}




function getBooks() {
    // Menginclude file koneksi.php yang berisi konfigurasi untuk terhubung ke database
    include 'koneksi.php';

    // Menyusun query SQL untuk mengambil semua data dari tabel 'buku'
    $query = "SELECT * FROM buku"; // 'buku' adalah nama tabel dalam database

    // Menjalankan query dan menyimpan hasilnya dalam variabel $result
    $result = mysqli_query($conn, $query);
    $books = []; // Array untuk menyimpan data buku

    // Memeriksa apakah query berhasil dijalankan
    if ($result) {
        // Mengambil setiap baris hasil query dan menyimpannya dalam array $books
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row; // Menyimpan setiap buku dalam array
        }
        // Menutup koneksi ke database
        mysqli_close($conn);
        // Mengembalikan hasil dalam format JSON
        return ["success" => true, "data" => $books];
    } else {
        // Jika query gagal dijalankan, tutup koneksi dan kembalikan pesan kesalahan
        mysqli_close($conn);
        return ["success" => false, "message" => "Gagal mengambil data buku"];
    }
}





// Fungsi untuk Mengambil Detail Buku
function getBookDetails($bookId) {
    // Menginclude file koneksi.php yang berisi konfigurasi untuk terhubung ke database
    include 'koneksi.php';

    // Memeriksa apakah $bookId valid atau tidak. Jika tidak ada, kembalikan pesan kesalahan.
    if (!$bookId) {
        return ["success" => false, "message" => "ID buku tidak ditemukan"];
    }

    // Query untuk mengambil data buku berdasarkan id_buku. Pastikan untuk menggunakan nama kolom yang benar sesuai dengan tabel.
    $query = "SELECT * FROM buku WHERE id_buku = '$bookId'";
    // Menjalankan query ke database
    $result = mysqli_query($conn, $query);

    // Memeriksa apakah query berhasil dijalankan
    if ($result) {
        // Mengambil hasil query sebagai array associative
        $book = mysqli_fetch_assoc($result);
        // Menutup koneksi database
        mysqli_close($conn);

        // Memeriksa apakah buku ditemukan
        if ($book) {
            // Jika buku ditemukan, kembalikan data buku dalam format JSON
            return ["success" => true, "data" => $book];
        } else {
            // Jika tidak ada buku yang ditemukan dengan id_buku tersebut, kembalikan pesan kesalahan
            return ["success" => false, "message" => "Buku tidak ditemukan"];
        }
    } else {
        // Jika query gagal dijalankan, tutup koneksi dan kembalikan pesan kesalahan
        mysqli_close($conn);
        return ["success" => false, "message" => "Gagal mengambil detail buku: " . mysqli_error($conn)];
    }
}





// Mencari buku berdasarkan query yang diberikan
function searchBooks($query) {
    include 'koneksi.php'; // Menghubungkan ke database
    
    // Menghindari SQL Injection
    $query = mysqli_real_escape_string($conn, $query); 

    // SQL untuk mencari buku berdasarkan judul
    $sql = "SELECT * FROM buku WHERE judul LIKE '%$query%'"; // Sesuaikan dengan nama kolom yang benar
    $result = mysqli_query($conn, $sql); // Menjalankan query pencarian

    $books = []; // Inisialisasi array untuk menyimpan hasil pencarian
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) { // Mengambil setiap baris hasil query
            $books[] = $row; // Menyimpan hasil pencarian ke dalam array
        }
        mysqli_close($conn); // Menutup koneksi ke database
        return ["success" => true, "data" => $books]; // Mengembalikan hasil pencarian
    } else {
        mysqli_close($conn); // Menutup koneksi jika gagal
        return ["success" => false, "message" => "Gagal mencari buku berdasarkan judul"]; // Mengembalikan pesan kesalahan
    }
}




// Fungsi untuk Mengambil Kategori Buku
function getCategories() {
    include 'koneksi.php'; // Menghubungkan ke database
    $query = "SELECT * FROM kategori"; // Mengambil semua kategori
    $result = mysqli_query($conn, $query); // Menjalankan query untuk mengambil data

    $categories = []; // Inisialisasi array untuk menyimpan kategori
    while ($row = mysqli_fetch_assoc($result)) { // Mengambil setiap baris hasil query
        $categories[] = $row; // Menyimpan kategori ke dalam array
    }
    mysqli_close($conn); // Menutup koneksi ke database
    
    return ["success" => true, "data" => $categories]; // Mengembalikan hasil dalam format JSON
}


// Fungsi untuk Mengambil Data Distributor Menggunakan POST
function getDistributors() {
    include 'koneksi.php'; // Menghubungkan ke database
    $query = "SELECT * FROM distributor"; // Mengambil semua data distributor
    $result = mysqli_query($conn, $query); // Menjalankan query

    $distributors = []; // Inisialisasi array untuk menyimpan distributor
    while ($row = mysqli_fetch_assoc($result)) { // Mengambil setiap baris hasil query
        $distributors[] = $row; // Menyimpan data distributor ke dalam array
    }
    mysqli_close($conn); // Menutup koneksi ke database

    return ["success" => true, "data" => $distributors]; // Mengembalikan hasil dalam format JSON
}

// Endpoint handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(getDistributors());
}




// Fungsi untuk Mengambil Data Pasok Menggunakan POST
function getSupplies() {
    include 'koneksi.php'; // Menghubungkan ke database
    $query = "SELECT * FROM pasok"; // Mengambil semua data pasok
    $result = mysqli_query($conn, $query); // Menjalankan query

    $supplies = []; // Inisialisasi array untuk menyimpan data pasok
    while ($row = mysqli_fetch_assoc($result)) { // Mengambil setiap baris hasil query
        $supplies[] = $row; // Menyimpan data pasok ke dalam array
    }
    mysqli_close($conn); // Menutup koneksi ke database

    return ["success" => true, "data" => $supplies]; // Mengembalikan hasil dalam format JSON
}

// Endpoint handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(getSupplies());
}


?>