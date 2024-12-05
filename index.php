<?php
include 'koneksi.php'; // Menghubungkan ke file koneksi database

// Konstanta untuk kode transaksi

// Login dan Pendaftaran
define("LOGIN", "01");              // Transaksi login
define("DAFTAR", "02");             // Transaksi pendaftaran

// Buku
define("GET_BOOKS", "03");          // Mengambil semua buku
define("GET_BOOK_DETAILS", "04");   // Mengambil detail buku berdasarkan ID
define("SEARCH_BOOKS", "05");       // Mencari buku berdasarkan query

// Kategori
define("GET_CATEGORIES", "06");     // Mengambil semua kategori buku

// Distributor dan Pemasokan
define("GET_DISTRIBUTORS", "07");   // Mengambil semua distributor
define("GET_PASOK", "08");          // Mengambil data pemasokan (pasok)

// Detail Penjualan dan Transaksi
define("GET_DETAIL_PENJUALAN", "09"); // Mengambil semua data detail_penjualan
define("GET_PENJUALAN", "11");        // Mengambil semua data penjualan

// Data Kasir
define("GET_KASIR", "10");          // Mengambil semua data kasir

// Keranjang
define("GET_KERANJANG", "12");       // Mengambil semua data keranjang
define("ADD_TO_CART", "13");         // Menambah item ke keranjang
define("UPDATE_CART_QUANTITY", "14"); // Memperbarui jumlah item dalam keranjang
define("REMOVE_FROM_CART", "15");    // Menghapus item dari keranjang
define("CHECKOUT", "16");            // Melakukan checkout
define("LOGOUT", "17");            // Melakukan Logout




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
                echo json_encode(Login($vPaket['DATA'])); // Fungsi Login
                break;

            case DAFTAR:
                echo json_encode(Daftar($vPaket['DATA'])); // Fungsi Daftar pengguna baru
                break;

            case GET_BOOKS:
                echo json_encode(getBooks()); // Fungsi mengambil semua buku
                break;

            case GET_BOOK_DETAILS:
                if (isset($vPaket['DATA']['book_id'])) {
                    $book_id = $vPaket['DATA']['book_id'];
                    echo json_encode(getBookDetails($book_id)); // Fungsi detail buku
                } else {
                    echo json_encode(["success" => false, "message" => "ID Buku diperlukan."]);
                }
                break;

            case SEARCH_BOOKS:
                if (isset($vPaket['DATA']['query'])) {
                    $query = $vPaket['DATA']['query'];
                    echo json_encode(searchBooks($query)); // Fungsi mencari buku
                } else {
                    echo json_encode(["success" => false, "message" => "Query pencarian diperlukan."]);
                }
                break;

            case GET_CATEGORIES:
                echo json_encode(getCategories()); // Fungsi mengambil kategori
                break;

            case GET_DISTRIBUTORS:
                echo json_encode(getDistributors()); // Fungsi mengambil distributor
                break;

            case GET_PASOK:
                echo json_encode(getPasok()); // Fungsi mengambil data pemasok
                break;

            case GET_DETAIL_PENJUALAN:
                echo json_encode(getDetailPenjualan()); // Fungsi mengambil detail penjualan
                break;

            case GET_KASIR:
                echo json_encode(getKasir()); // Fungsi mengambil data kasir
                break;

            case GET_PENJUALAN:
                echo json_encode(getPenjualan()); // Fungsi mengambil data penjualan
                break;

            case GET_KERANJANG:
                echo json_encode(getKeranjang()); // Fungsi mengambil keranjang
                break;

            case ADD_TO_CART:
                if (isset($vPaket['DATA']['id_kasir']) && isset($vPaket['DATA']['id_buku']) && isset($vPaket['DATA']['jumlah'])) {
                    $id_kasir = $vPaket['DATA']['id_kasir'];
                    $id_buku = $vPaket['DATA']['id_buku'];
                    $jumlah = $vPaket['DATA']['jumlah'];
                echo json_encode(addToCart($id_kasir, $id_buku, $jumlah)); // Fungsi menambahkan ke keranjang
                } else {
                echo json_encode(["success" => false, "message" => "ID Kasir, ID Buku, dan Jumlah harus disediakan."]);
                }
                break;
                

            case UPDATE_CART_QUANTITY:
                // Pastikan id_buku, jumlah, dan id_kasir ada dalam request
                if (isset($vPaket['DATA']['id_buku']) && isset($vPaket['DATA']['jumlah']) && isset($vPaket['DATA']['id_kasir'])) {
                        $id_buku = $vPaket['DATA']['id_buku'];  // Ambil id_buku dari request
                        $jumlah = $vPaket['DATA']['jumlah'];    // Ambil jumlah dari request
                        $id_kasir = $vPaket['DATA']['id_kasir']; // Ambil id_kasir dari request
                    
                // Panggil fungsi untuk memperbarui jumlah item di keranjang
                echo json_encode(updateCartQuantity($id_buku, $jumlah, $id_kasir)); 
                } else {
                // Jika id_buku, jumlah, atau id_kasir tidak ada, kembalikan error
                echo json_encode(["success" => false, "message" => "ID Buku, Jumlah, dan ID Kasir harus disediakan."]);
                }
                break;
                    

            case REMOVE_FROM_CART:
                if (isset($vPaket['DATA']['id_buku'])) {
                        $id_buku = $vPaket['DATA']['id_buku'];
                echo json_encode(removeFromCart($id_buku)); // Fungsi untuk menghapus item dari keranjang
                } else {
                echo json_encode(["success" => false, "message" => "ID Buku harus disediakan."]);
                }
                break;


            case CHECKOUT:
                if (isset($vPaket['DATA']['id_kasir']) && isset($vPaket['DATA']['id_buku']) && isset($vPaket['DATA']['jumlah'])) {
                        $id_kasir = $vPaket['DATA']['id_kasir'];
                        $id_buku = $vPaket['DATA']['id_buku'];
                        $jumlah = $vPaket['DATA']['jumlah'];
                    
                // Panggil fungsi checkout
                echo json_encode(processCheckout($id_kasir, $id_buku, $jumlah));
                } else {
                echo json_encode(["success" => false, "message" => "ID Kasir, ID Buku, dan Jumlah harus disediakan untuk checkout."]);
                }
                break;
                    
            
            case LOGOUT:
                echo json_encode(Logout($vPaket['DATA'])); // Fungsi Logout
                break;
                
            default:
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



//Fungsi untuk Login//
function Login($data) {
    include 'koneksi.php';
    session_start(); // Memulai sesi

    $username = isset($data['Username']) ? $data['Username'] : null;
    $password = isset($data['Password']) ? md5($data['Password']) : null;

    if ($username && $password) {
        $query = "SELECT username, password FROM kasir WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);

                // Simpan data pengguna ke dalam sesi
                $_SESSION['user'] = $user['username'];

                mysqli_close($conn);
                return ["success" => true, "message" => "Login berhasil", "data" => $user];
            } else {
                mysqli_close($conn);
                return ["success" => false, "message" => "Username atau password salah"];
            }
        } else {
            mysqli_close($conn);
            return ["success" => false, "message" => "Error: " . mysqli_error($conn)];
        }
    } else {
        return ["success" => false, "message" => "Username dan password harus diisi"];
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




// Fungsi untuk Mengambil Data Distributor
function getDistributors() {
    include 'koneksi.php'; // Menghubungkan ke database
    $query = "SELECT * FROM distributor"; // Mengambil semua data distributor
    $result = mysqli_query($conn, $query); // Menjalankan query untuk mengambil data

    $distributors = []; // Inisialisasi array untuk menyimpan data distributor
    while ($row = mysqli_fetch_assoc($result)) { // Mengambil setiap baris hasil query
        $distributors[] = $row; // Menyimpan data distributor ke dalam array
    }
    mysqli_close($conn); // Menutup koneksi ke database
    
    return ["success" => true, "data" => $distributors]; // Mengembalikan hasil dalam format JSON
}




// Fungsi untuk Mengambil Data Pasok
function getPasok() {
    include 'koneksi.php'; // Menghubungkan ke database
    $query = "SELECT * FROM pasok"; // Query untuk mengambil semua data dari tabel pasok
    $result = mysqli_query($conn, $query); // Menjalankan query

    $pasokData = []; // Inisialisasi array untuk menyimpan data pasok
    while ($row = mysqli_fetch_assoc($result)) { // Mengambil setiap baris hasil query
        $pasokData[] = $row; // Menyimpan data ke dalam array
    }
    mysqli_close($conn); // Menutup koneksi ke database
    
    return ["success" => true, "data" => $pasokData]; // Mengembalikan hasil dalam format JSON
}





//Fungsi untuk mengambil data detailpenjualan//
function getDetailPenjualan() {
    include 'koneksi.php'; // Menghubungkan ke database
    $query = "SELECT * FROM detail_penjualan"; // Query untuk mengambil semua data detail_penjualan
    $result = mysqli_query($conn, $query); // Menjalankan query

    $detailPenjualan = []; // Inisialisasi array untuk menyimpan data detail_penjualan
    while ($row = mysqli_fetch_assoc($result)) { // Mengambil setiap baris hasil query
        $detailPenjualan[] = $row; // Menyimpan data ke dalam array
    }
    mysqli_close($conn); // Menutup koneksi ke database
    
    return ["success" => true, "data" => $detailPenjualan]; // Mengembalikan hasil dalam format JSON
}



//Fungsi untuk mengambil data kasir//
function getKasir() {
    // Menghubungkan ke file koneksi database
    include 'koneksi.php';

    // Menyusun query SQL untuk mengambil semua data dari tabel 'kasir'
    $query = "SELECT * FROM kasir";

    // Menjalankan query dan menyimpan hasilnya di variabel $result
    $result = mysqli_query($conn, $query);

    // Inisialisasi array kosong untuk menyimpan data kasir
    $kasir = [];

    // Mengambil setiap baris hasil query dan menyimpannya ke dalam array $kasir
    while ($row = mysqli_fetch_assoc($result)) {
        $kasir[] = $row; // Menambahkan baris data ke dalam array
    }

    // Menutup koneksi ke database
    mysqli_close($conn);
    
    // Mengembalikan hasil dalam format JSON yang menunjukkan status sukses dan data kasir
    return ["success" => true, "data" => $kasir];
}





//Fungsi untuk mengambil data penjualan//
function getPenjualan() {
    include 'koneksi.php'; // Menghubungkan ke database.

    // Cek koneksi database
    if (!$conn) {
        return [
            "success" => false,
            "message" => "Koneksi database gagal: " . mysqli_connect_error()
        ];
    }

    // Query untuk mengambil semua data dari tabel 'penjualan'
    $query = "SELECT * FROM penjualan";
    $result = mysqli_query($conn, $query); // Menjalankan query.

    // Cek apakah query berhasil dijalankan
    if (!$result) {
        mysqli_close($conn); // Tutup koneksi jika gagal
        return [
            "success" => false,
            "message" => "Gagal menjalankan query: " . mysqli_error($conn)
        ];
    }

    $dataPenjualan = []; // Array untuk menyimpan hasil data penjualan.
    while ($row = mysqli_fetch_assoc($result)) {
        $dataPenjualan[] = $row; // Menambahkan setiap baris data ke array.
    }

    // Menutup koneksi database
    mysqli_close($conn);

    // Mengembalikan hasil dalam format JSON-friendly
    return [
        "success" => true,
        "data" => $dataPenjualan
    ];
}




//Fungsi untuk mengambil data keranjang//
function getKeranjang() {
    // Menghubungkan ke file koneksi database
    include 'koneksi.php';

    // Pastikan koneksi berhasil
    if (!$conn) {
        return [
            "success" => false,
            "message" => "Koneksi database gagal: " . mysqli_connect_error()
        ];
    }

    // Query SQL untuk mengambil data dari tabel 'keranjang'
    $query = "SELECT * FROM keranjang";

    // Menjalankan query pada koneksi database
    $result = mysqli_query($conn, $query);

    // Periksa apakah query berhasil dijalankan
    if (!$result) {
        mysqli_close($conn); // Tutup koneksi sebelum mengembalikan error
        return [
            "success" => false,
            "message" => "Gagal mengambil data keranjang: " . mysqli_error($conn)
        ];
    }

    // Array untuk menyimpan data keranjang
    $items = [];

    // Loop untuk mengambil setiap baris data hasil query
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }

    // Menutup koneksi database
    mysqli_close($conn);

    // Mengembalikan hasil dalam format JSON-friendly
    return [
        "success" => true,
        "data" => $items
    ];
}




//Fungsi untuk menambah item ke keranjang//
function addToCart($id_kasir, $id_buku, $jumlah) {
    include 'koneksi.php'; // Menghubungkan ke file koneksi database.

    // Pastikan id_kasir, id_buku, dan jumlah valid
    if (empty($id_kasir) || empty($id_buku) || empty($jumlah)) {
        return ["success" => false, "message" => "ID Kasir, ID Buku, dan Jumlah tidak boleh kosong."];
    }

    // Query SQL untuk memasukkan data ke tabel 'keranjang'
    $query = "INSERT INTO keranjang (id_kasir, id_buku, jumlah) VALUES (?, ?, ?)";

    // Menggunakan prepared statement untuk mencegah SQL Injection.
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt === false) {
        return ["success" => false, "message" => "Gagal mempersiapkan statement: " . mysqli_error($conn)];
    }

    // Mengikat parameter input
    mysqli_stmt_bind_param($stmt, "iii", $id_kasir, $id_buku, $jumlah);

    // Eksekusi statement
    if (mysqli_stmt_execute($stmt)) {
        $result = ["success" => true, "message" => "Data berhasil ditambahkan ke keranjang."];
    } else {
        $result = ["success" => false, "message" => "Gagal menambahkan data ke keranjang: " . mysqli_error($conn)];
    }

    // Menutup statement dan koneksi
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $result; // Mengembalikan hasil eksekusi dalam format JSON-friendly.
}






//Fungsi untuk memperbarui jumlah item dalam keranjang//
function updateCartQuantity($id_buku, $jumlah, $id_kasir) {
    include 'koneksi.php'; // Menghubungkan ke file koneksi database.

    // Pastikan id_buku, jumlah, dan id_kasir valid
    if (empty($id_buku) || empty($jumlah) || empty($id_kasir)) {
        return ["success" => false, "message" => "ID Buku, Jumlah, dan ID Kasir tidak boleh kosong."];
    }

    // Query SQL untuk memperbarui jumlah item di tabel 'keranjang'
    $query = "UPDATE keranjang SET jumlah = ?, id_kasir = ? WHERE id_buku = ?";

    // Menggunakan prepared statement untuk mencegah SQL Injection.
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt === false) {
        return ["success" => false, "message" => "Gagal mempersiapkan statement: " . mysqli_error($conn)];
    }

    // Mengikat parameter input
    mysqli_stmt_bind_param($stmt, "iii", $jumlah, $id_kasir, $id_buku);

    // Eksekusi statement
    if (mysqli_stmt_execute($stmt)) {
        $result = ["success" => true, "message" => "Jumlah item berhasil diperbarui."];
    } else {
        $result = ["success" => false, "message" => "Gagal memperbarui jumlah item: " . mysqli_error($conn)];
    }

    // Menutup statement dan koneksi
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $result; // Mengembalikan hasil eksekusi dalam format JSON-friendly.
}






//Fungsi untuk menghapus item dari keranjang//
function removeFromCart($id_buku) {
    include 'koneksi.php'; // Hubungkan ke database

    // Query untuk menghapus item berdasarkan id_buku
    $query = "DELETE FROM keranjang WHERE id_buku = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt === false) {
        return ["success" => false, "message" => "Gagal mempersiapkan query: " . mysqli_error($conn)];
    }

    // Bind parameter
    mysqli_stmt_bind_param($stmt, "i", $id_buku);

    // Eksekusi query
    if (mysqli_stmt_execute($stmt)) {
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        if ($affected_rows > 0) {
            $response = ["success" => true, "message" => "Item berhasil dihapus dari keranjang."];
        } else {
            $response = ["success" => false, "message" => "Item tidak ditemukan dalam keranjang."];
        }
    } else {
        $response = ["success" => false, "message" => "Gagal menghapus item: " . mysqli_error($conn)];
    }

    // Tutup statement dan koneksi
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $response;
}





//Fungsi untuk proses checkout//
function processCheckout($id_kasir, $id_buku, $jumlah) {
    include 'koneksi.php';

    // Validasi parameter
    if (empty($id_kasir) || empty($id_buku) || $jumlah <= 0) {
        return ["success" => false, "message" => "ID Kasir, ID Buku, dan Jumlah harus valid."];
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Periksa stok buku
        $query_stok = "SELECT stok, harga_jual FROM buku WHERE id_buku = ?";
        $stmt_stok = mysqli_prepare($conn, $query_stok);
        if (!$stmt_stok) {
            throw new Exception("Gagal menyiapkan statement stok: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_stok, "i", $id_buku);
        mysqli_stmt_execute($stmt_stok);
        $result_stok = mysqli_stmt_get_result($stmt_stok);
        $buku = mysqli_fetch_assoc($result_stok);

        if (!$buku) {
            throw new Exception("Buku tidak ditemukan.");
        }
        if ($buku['stok'] < $jumlah) {
            throw new Exception("Stok buku tidak mencukupi. Stok saat ini: " . $buku['stok']);
        }

        $harga_jual = $buku['harga_jual'];

        // Tambahkan ke tabel penjualan
        $query_penjualan = "INSERT INTO penjualan (id_kasir, tanggal) VALUES (?, NOW())";
        $stmt_penjualan = mysqli_prepare($conn, $query_penjualan);
        if (!$stmt_penjualan) {
            throw new Exception("Gagal menyiapkan statement penjualan: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_penjualan, "i", $id_kasir);
        if (!mysqli_stmt_execute($stmt_penjualan)) {
            throw new Exception("Gagal menambahkan data ke tabel penjualan: " . mysqli_error($conn));
        }
        $penjualan_id = mysqli_insert_id($conn);


        // Tambahkan ke tabel detail_penjualan
        $query_detail = "INSERT INTO detail_penjualan (id_penjualan, id_buku, jumlah, harga_jual) VALUES (?, ?, ?, ?)";
        $stmt_detail = mysqli_prepare($conn, $query_detail);
        if (!$stmt_detail) {
        throw new Exception("Gagal menyiapkan statement detail: " . mysqli_error($conn));
        }
        $total_harga = $jumlah * $harga_jual;
        mysqli_stmt_bind_param($stmt_detail, "iiid", $penjualan_id, $id_buku, $jumlah, $total_harga);
        if (!mysqli_stmt_execute($stmt_detail)) {
        throw new Exception("Gagal menambahkan data ke detail penjualan: " . mysqli_error($conn));
        }


        // Update stok buku
        $query_update_stok = "UPDATE buku SET stok = stok - ? WHERE id_buku = ?";
        $stmt_update_stok = mysqli_prepare($conn, $query_update_stok);
        if (!$stmt_update_stok) {
            throw new Exception("Gagal menyiapkan statement update stok: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_update_stok, "ii", $jumlah, $id_buku);
        if (!mysqli_stmt_execute($stmt_update_stok)) {
            throw new Exception("Gagal memperbarui stok buku: " . mysqli_error($conn));
        }

        // Commit transaksi
        mysqli_commit($conn);

        return [
            "success" => true,
            "message" => "Checkout berhasil.",
            "penjualan_id" => $penjualan_id,
            "total_harga" => $total_harga
        ];
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ["success" => false, "message" => $e->getMessage()];
    } finally {
        if (isset($stmt_stok)) mysqli_stmt_close($stmt_stok);
        if (isset($stmt_penjualan)) mysqli_stmt_close($stmt_penjualan);
        if (isset($stmt_detail)) mysqli_stmt_close($stmt_detail);
        if (isset($stmt_update_stok)) mysqli_stmt_close($stmt_update_stok);
        mysqli_close($conn);
    }
}




//Fungsi untuk Logout//
function Logout() {
    session_start(); // Memulai sesi

    // Debugging isi sesi
    error_log("Session Content: " . print_r($_SESSION, true));

    if (isset($_SESSION['user'])) {
        session_unset(); // Hapus semua data sesi
        session_destroy(); // Hancurkan sesi
        return ["success" => true, "message" => "Logout berhasil"];
    } else {
        return ["success" => false, "message" => "Tidak ada sesi yang aktif"];
    }
}





?>